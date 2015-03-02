<?php

	namespace Bol\Plaza\API;
	
	class Comms {
			/* API Access key as provided by Bol.com
			 * The access key is the shorter one of the two keys that you have received
			 */
		private $accessKey;
		
			/* Secret key as provided by Bol.com
			 * The secret key is the longer one of the two keys that you have received
			 */
		private $secretKey;

		private $environments = Array(
			'testing'=>Array(
				'url' => 'https://test-plazaapi.bol.com',
				'port' => 443
			),
			'production'=>Array(
				'url' => 'https://plazaapi.bol.com', // @TODO: Check if not .acc2
				'port' => 443
			)
		);
		private	$currentEnv = 'production';
		
		public $enableResponseHeaders = false;
		
		/**
		 * @param debug {Boolean} Whether or not to print debug information.
		 */
		private $debug;
		
		
		private $responseToFile = false;
		
		public function __construct($parent, $accessKey, $secretKey, $targetTestEnv) {
			$this->debug = $parent->debug ? true : false;

			if ($accessKey != '' && $secretKey != '') {
				$this->accessKey = $accessKey;
				$this->secretKey = $secretKey;
			} else {
				throw new \Bol\Plaza\PlazaException('Invalid accessKey / secretKey pair provided.');
			}
			
				// Should we target the testing environment?
			if ($targetTestEnv === true)
				$this->currentEnv = 'testing';
		}

		/** Public method to perform a call to the API */
		public function plazaCall($targetUri, $httpMethod = 'POST', $xmlPayLoad = false, $mimeType = 'application/xml') {
			
				// Perform the call
			$callResult = $this -> _compileAndPerformHTTPCall($targetUri, $httpMethod, $xmlPayLoad, $mimeType);
			
				// Print debug info and return 
			if ($this -> debug) \Bol\Plaza\API\Tools::debug($callResult, true); // @TODO: Checken of dit wel goed werkt.


				// Return the entire call result object.
			return $callResult;

		}

		/**
		* Compiles the encoded auth header string.
		*/
		private function _compileAuthHeader($targetUri, $date, $httpMethod, $mimeType = 'application/xml') {
			$httpMethod = strtoupper($httpMethod);
			
			if (!in_array($httpMethod, Array('PUT','POST','DELETE','GET', 'HEAD'))) {
				throw new \Bol\Plaza\PlazaException('Invalid HTTP Method "'.$httpMethod.'"');  
			}
			
			if (strpos($targetUri, '?') !== false) {
				$targetUri = substr($targetUri, 0, strpos($targetUri, '?'));
			}
			
			$signatureElements = Array();
			$signatureElements[] = $httpMethod."\n"; // Extra newline
			$signatureElements[] = $mimeType; 
			$signatureElements[] = $date;
			$signatureElements[] = "x-bol-date:".$date;
			$signatureElements[] = $targetUri;
			
			$signatureString = implode("\n", $signatureElements);

			$signature = $this->accessKey.':';
			$signature.= base64_encode(
							hash_hmac(
								'SHA256', 
								$signatureString, 
								$this->secretKey, 
								true
							)
						);
			return $signature;
		}


		/**
		 * Private Error handler logic
		 */
		//private function _handleError(&$result, &$headers, &$xmlPayLoad, &$HTTPHeadersFromRequest) {
		private function _handleError(&$curlResults, &$HTTPHeadersFromRequest) {
				// Fallback error messages
			$ErrorCode = 'Bol Error Code unavailable';
			$ErrorMsg = 'Errormessage unavailable';

				// Extract error messages and codes from the server response IF ANY XML was returned at all. 
			if (strlen(trim($curlResults['payload']))>0) {
				$xmlError = new \DOMDocument();
				if (@$xmlError -> loadXML($curlResults['payload']) !== false) {
					$ErrorCode = @$xmlError->getElementsByTagNameNS('http://config.services.bol.com/schemas/serviceerror-1.5.xsd', 'errorCode');
					$ErrorCode = @$ErrorCode->item(0)->nodeValue;
					
					$ErrorMsg = @$xmlError->getElementsByTagNameNS('http://config.services.bol.com/schemas/serviceerror-1.5.xsd', 'errorMessage');
					$ErrorMsg = @$ErrorMsg->item(0)->nodeValue;				
				}
			}
			
			if (isset($curlResults['status']['http_code'])) {
				$ErrorHTTPCode = 'HTTP1/1 '.$curlResults['status']['http_code'];
				
				switch ($curlResults['status']['http_code']) {
					case '401':
						$ErrorMsg = 'Unauthorized';
					break;
					case '403':
						$ErrorMsg = 'Forbidden';
					break;
					case '404':
						$ErrorMsg = 'Not Found';
					break;
					case '409':
						$ErrorMsg = 'Rate limiting in effect';
					break;
				}
			}


			if ($this->debug) {
				echo '<pre>';
				echo 'XML sent to server: '.(strlen($curlResults['payLoad'])>0 ? ('"'.htmlentities($curlResults['payLoad']).'"') : 'None')."\n\n";
				echo "Curl Status Info:\n";
				print_r($curlResults['status']);
				echo "\n\nHTTP Headers:\n";
				print_r($HTTPHeadersFromRequest);
				echo "</pre>";
			}

			$Exception = new \Bol\Plaza\PlazaException(
				'An error occured while communicating with the server. See the getHTTPClientStatus(), getRawBody() and getHTTPError() methods on the Exception for more details.',
				null,
				null
			);
			$Exception -> setHTTPClientStatus($curlResults['status']);
			$Exception -> setRawBody($curlResults['payload']);
			$Exception -> setHTTPError($curlResults['error']);
			throw $Exception;
		}
		
		
		/**
		 * Perform an API call.
		 * @param targetUri - The URI (Not a complete URL) the call should be placed to. 
		 * @param $httpMethod - GET or POST 
		 * @param $xmlPayLoad - String containing the XML to be sent. 
		 * @param $mimeType - Typically application/xml;
		 * 
		 * @return Array with 3 keys: playload, status and error
		 * 
		 * @Throws Exception 
		 */
		private function _compileAndPerformHTTPCall($targetUri, $httpMethod, $xmlPayLoad, $mimeType) {
			/*	@TODO: Add ontent filtering.
			Check if there's a / at the beginning of the Uri for example.
			Check if the xml seems valid */
			
			
				// Set the date variable here to ensure the same date is used in the x-bol-authorization header and the x-bol-date header
			$date = gmdate('D, d M Y H:i:s T');
				
				// Get authenticaction header
			$headerXBolAuth = $this->_compileAuthHeader($targetUri, $date, $httpMethod, $mimeType);
			
			$HTTPHeaders = Array(
				"Content-type: ".$mimeType, 
				"X-BOL-Date: ".$date, 
				"X-BOL-Authorization: ".$headerXBolAuth
			);

			//print_r($HTTPHeaders);
			//die();
				// Setup Curl
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HTTPHEADER, $HTTPHeaders);
			curl_setopt($curl, CURLOPT_HEADER, $this->enableResponseHeaders);
			curl_setopt($curl, CURLOPT_URL, $this->environments[$this->currentEnv]['url'] . $targetUri);
			curl_setopt($curl, CURLOPT_PORT, $this->environments[$this->currentEnv]['port']);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
			if ($httpMethod == 'POST') {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlPayLoad);
			} else if ($httpMethod == 'PUT') {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlPayLoad);
			} else if ($httpMethod == 'DELETE') {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
			} else if ($httpMethod == 'HEAD') {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
			}

			if ($this->responseToFile !== false) {
				if (file_exists(dirname($this->responseToFile)) && is_writable(dirname($this->responseToFile))) {
					$fp = fopen($this->responseToFile, 'w');
					curl_setopt($curl, CURLOPT_FILE, $fp);
				} else {
					throw new \Bol\Plaza\PlazaException('Unable to open location "'.$this->responseToFile.'" for writing');
				}
			}
			
			/*
			 * Enabling the following is NOT recommended but could serve if there's trouble with the SSL certificate.
			 * This has happened in the past but should not happen again. (Shoulda Coulda Woulda)
			 * */ 
			 curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			 
				// Execute the call
			$curlPayload = curl_exec($curl);
			$curlStatus = curl_getinfo($curl);
			$curlError = curl_error($curl);

				// Clean up after ourselves
			curl_close($curl);

				// Disable the "write to file" option.
			$this->responseToFile(false);
			
			
			$curlResults = array(
				'payload'	=>$curlPayload,	// Contains the HTTP Body 
				'status'	=>$curlStatus,	// Contains the curl status
				'error'		=>$curlError	// Contains cURL's error message (if any)
			);
			
				// Handle the response
			if (!$curlStatus['http_code'] || $curlStatus['http_code'] < 200 || $curlStatus['http_code'] > 299) {
					// If the server returned an error, fail screaming like a pig....yet in a graceful manner ;)
					// @TODO: This is not ideal as it can't be controlled. This should be rewritten to become something optional or something...
				$this->_handleError($curlResults, $HTTPHeaders);
			} else {
					// Return the results to the caller.
				return $curlResults;
			}
		}

		/**
		 * Configures the HTTP client to store the HTTP response to disk. 
		 * After executing the call and storing it, this setting will be disabled again automatically!
		 * @param $fileName String specifying the complete path to the file that should be saved or false to disable this feature
		 */
		public function responseToFile($fileName) {
			if ($fileName === false) {
				$this->responseToFile = false;
			} else if (!file_exists(dirname($fileName)) || !is_writable(dirname($fileName))) {
				throw new \Bol\Plaza\PlazaException('Unable to open location "'.$fileName.'" for writing');
			}
			
			$this->responseToFile = $fileName;
		}
	}
?>