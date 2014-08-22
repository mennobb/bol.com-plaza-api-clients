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
				'url' => 'https://plazaapi.bol.com',
				'port' => 443
			)
		);
		private	$currentEnv = 'production';
		
		/**
		 * @param debug {Boolean} Whether or not to print debug information.
		 */
		private $debug;
		
		public function __construct($parent, $accessKey, $secretKey, $targetTestEnv) {
			$this->debug = $parent->debug ? true : false;

			if ($accessKey != '' && $secretKey != '') {
				$this->accessKey = $accessKey;
				$this->secretKey = $secretKey;
			} else {
				throw new \InvalidArgumentException('Invalid accessKey / secretKey pair provided.');
				//trigger_error('Invalid accessKey / secretKey pair provided.', E_USER_ERROR); 
			}
			
				// Should we target the testing environment?
			if ($targetTestEnv === true)
				$this->currentEnv = 'testing';

		}

		/** Public method to perform a call to the API */
		public function plazaCall($targetUri, $httpMethod = 'POST', $xmlPayLoad = false, $mimeType = 'application/xml') {
			$response = $this -> _compileAndPerformHTTPCall($targetUri, $httpMethod, $xmlPayLoad, $mimeType);
			if ($this->debug) \Bol\Plaza\API\Tools::debug($response, true);
			$xmlObject = new \DOMDocument();
			$xmlObject -> loadXML($response);
			return \Bol\Plaza\API\Tools::xmlToArray($xmlObject);
		}

		/**
		* Compiles the encoded auth header string.
		*/
		private function _compileAuthHeader($targetUri, $date, $httpMethod = 'POST', $mimeType = 'application/xml') {
			$httpMethod = strtoupper($httpMethod);
			if ($httpMethod !== 'POST')
				$httpMethod = 'GET';
			
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
		private function _handleError(&$result, &$headers, &$xmlPayLoad, &$HTTPHeaders) {
				// Fallback error messages
			$ErrorCode = 'Errorcode unavailable as there was no content received from the server.';
			$ErrorMsg = 'Errormessage unavailable as there was no content received from the server.';
			
			if (strlen($result)>0) {
				$xmlError = new \DOMDocument();
				$xmlError -> loadXML($result);
				
				try {
					$ErrorCode = $xmlError->getElementsByTagNameNS('http://config.services.bol.com/schemas/serviceerror-1.5.xsd', 'errorCode');
					$ErrorCode = $ErrorCode->item(0)->nodeValue;
					
					$ErrorMsg = $xmlError->getElementsByTagNameNS('http://config.services.bol.com/schemas/serviceerror-1.5.xsd', 'errorMessage');
					$ErrorMsg = $ErrorMsg->item(0)->nodeValue;
				} catch (Exception $e) {
					echo 'An error occurred while parsing the XML Error Message. Raw XML printed below<br>';
				}
			}
			
	
			// @TODO: Dit netjes oplossen. Mooie custom Exception definieren en deze data in stoppen.
			echo 'XML Payload: "'.(strlen($xmlPayLoad)>0 ? $xmlPayLoad : 'No XML data received, so there\'s nothing to parse!')."\"\n<br>";
			echo "<pre>Curl header info:\n";
			print_r($headers);
			echo "HTTP Headers:\n";
			print_r($HTTPHeaders);
			echo "</pre>";
			
			if ($this->debug) {
				trigger_error($ErrorCode.' - '.$ErrorMsg, E_USER_ERROR);
			} else {
				throw new \Exception($ErrorCode.' - '.$ErrorMsg);
			}
		}
		
		
		/**
		 * Perform an API call.
		 * @param targetUri - The URI (Not a complete URL) the call should be placed to. 
		 * @param $httpMethod - GET or POST 
		 * @param $xmlPayLoad - String containing the XML to be sent. 
		 * @param $mimeType - Typically application/xml;
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
				"X-BOL-Date:".$date, 
				"X-BOL-Authorization: ".$headerXBolAuth
			);
			
				// Setup Curl
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HTTPHEADER, $HTTPHeaders);
			curl_setopt($curl, CURLOPT_URL, $this->environments[$this->currentEnv]['url'] . $targetUri);
			curl_setopt($curl, CURLOPT_PORT, $this->environments[$this->currentEnv]['port']);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			if ($httpMethod == 'POST') {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlPayLoad);
			}
			
			/*
			 * Enabling the following is NOT recommended but could serve if there's trouble with the SSL certificate.
			 * This has happened in the past but should not happen again. (Shoulda Coulda Woulda)
			 * */ 
			 curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			 
			if(curl_errno($curl)) {
				throw new \Exception((string)curl_errno($curl));
				return;
			}
			
				// Execute the call
			$curlResult = curl_exec($curl);
			$curlStatus = curl_getinfo($curl);
			
				// if $result is false then there was an error. We know this because CURLOPT_RETURNTRANSFER = true.
			if ($curlResult === false) {
				throw new \Exception((string)curl_error($curl));
				return; // superfluous.
			}
			
				// Clean up after ourselves
			curl_close($curl);
			
				// Handle the response
			if ($curlStatus['http_code'] !== 200 || !strstr($curlStatus['content_type'], 'application/xml')) {
					// If the server returned an error, fail screaming like a pig....yet graceful.
					// @TODO: This is not ideal as it can't be controlled. This hsould be rewritten to become something optional or something...
				$this->_handleError($curlResult, $curlStatus, $xmlPayLoad, $HTTPHeaders);
			} else {
					// Parse the response and return a simpleXml object
				if (strstr($curlStatus['content_type'], 'application/xml')) 
					return $curlResult;
				else
					throw new \Exception('Content of type application/xml expected. Received "'.$curlStatus['content_type'].'" instead.');
			}
		}
	}
?>
