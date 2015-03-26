<?php	
	namespace Bol\Plaza\API;
	
	use Bol\Plaza\API\Exceptions\PlazaException;
	use Bol\Plaza\API\Classes\Comms;
	use Bol\Plaza\API\Classes\ProcessOrder;
	use Bol\Plaza\API\Classes\Tools;
	use Bol\Plaza\API\Models\OfferUpdate;
	use Bol\Plaza\API\Models\StockUpdate;
	use Bol\Plaza\API\Models\OfferCreate;
	
	/**
	 * * To make the code as lightweight as possible we use as little classes as possible.
	 */
	
	class API {
			/**
			 * @param Holds the instance of the Comms class that takes care of the http communication
			 */
		private $Comms;
		
			/**
			 * @param Hold the instance of the Tools class. Handier than a Swiss Army knife
			 */
		private $Tools;
		
			/**
			 * @param Holds the Array that forms the basis for the ProcessOrders xml
			 */
		private $OrderTransaction;
		
			/**
			 * @param Holds the count of $OrderTransaction to avoid doing relatively expensive count operations
			 */
		private $OrderTransactionSize;


			/**
			 * @param Boolean 
			 */		
		public $debug;
			/**
			 * Constructor.
			 * The Public and Private key are provided by Bol.
			 * Note that these keys are NOT the same as the ones from Bol.com's OpenAPI.
			 * 
			 * @param accessKey {String} Public key
			 * @param secretKey {String} private key
			 * @param targetTestEnv {Boolean} Use test environment? Default: False (live)
			 */
		public function __construct($accessKey, $secretKey, $targetTestEnv=false, $debug=false) {
			$this->debug = $debug ? true : false; 
			$this -> Comms = new Comms($this, $accessKey, $secretKey, $targetTestEnv);
		}

		/**
		 * Create an offer on the server. The OfferID must be unique although no error will be thrown by the API when it turns out to be a duplicate as the process is asynchronous on bol.com's end.
		 * The getOffers() method will provide details regarding your failed "offer creates"
		 * @param $offer must be of type Bol\Plaza\API\Models\OfferCreate
		 */
		public function createOffer($offer) {
				// Type checking
			if (is_a($offer, 'OfferCreate')) {
				$result = $this->Comms->plazaCall(
					'/offers/v1/'.urlencode($offer->__get('OfferId')),
					'POST', 
					$offer->toXML()
				);

				if ($result['status']['http_code'] >= 200 && $result['status']['http_code'] < 300) {
					return true;
				} else {
					$Exception = new PlazaException(
						'An error occured while creating your offer. See the getHTTPClientStatus(), getRawBody() and getHTTPError() methods on the Exception for more details.',
						null,
						null
					);
					$Exception -> setHTTPClientStatus($result['status']);
					$Exception -> setRawBody($result['payload']);
					$Exception -> setHTTPError($result['error']);
					throw $Exception;
				}
			} else {
				throw new PlazaException('createOffer was called without a valid offer of type Bol\Plaza\API\Models\OfferCreate');
			}
		}
		
		/**
		 * Update an offer on the server. The OfferID must exist within your offers already, although no error will be thrown by the API when it turns out to be a duplicate as the process is asynchronous on bol.com's end.
		 * The getOffers() method will provide input regarding your failed "offer updates".
		 * @param $offer must be of type Bol\Plaza\API\Models\OfferUpdate
		 */
		public function updateOffer($offer) {
				// Type checking
			
			if (is_a($offer, 'OfferUpdate')) {
				$result = $this->Comms->plazaCall(
					'/offers/v1/'.urlencode($offer->__get('OfferId')),
					'PUT',
					$offer->toXML()
				);
				
				if ($result['status']['http_code'] >= 200 && $result['status']['http_code'] < 300) {
					return true;
				} else {
					$Exception = new PlazaException(
						'An error occured while updating your offer. See the getHTTPClientStatus(), getRawBody() and getHTTPError() methods on the Exception for more details.',
						null,
						null
					);
					$Exception -> setHTTPClientStatus($result['status']);
					$Exception -> setRawBody($result['payload']);
					$Exception -> setHTTPError($result['error']);
					throw $Exception;
				}
			} else {
				throw new PlazaException('updateOffer was called without a valid offer. Expected type Bol\Plaza\API\Models\OfferUpdate');
			}
		}
		
		
		/**
		 * Update an offer on the server. The OfferID must exist within your offers already, although no error will be thrown by the API when it turns out to be a duplicate as the process is asynchronous on bol.com's end.
		 * The getOffers() method will provide input regarding your failed "offer updates".
		 * @param $offer must be of type Bol\Plaza\API\Models\OfferUpdate
		 */
		public function updateOfferStock($offer) {
				// Type checking
			
			if (is_a($offer, 'StockUpdate')) {
				$result = $this->Comms->plazaCall(
					'/offers/v1/'.urlencode($offer->__get('OfferId')).'/stock',
					'PUT',
					$offer->toXML()
				);
				
				if ($result['status']['http_code'] >= 200 && $result['status']['http_code'] < 300) {
					return true;
				} else {
					$Exception = new PlazaException(
						'An error occured while updating your offer\'s stock. See the getHTTPClientStatus(), getRawBody() and getHTTPError() methods on the Exception for more details.',
						null,
						null
					);
					$Exception -> setHTTPClientStatus($result['status']);
					$Exception -> setRawBody($result['payload']);
					$Exception -> setHTTPError($result['error']);
					throw $Exception;
				}
			} else {
				throw new PlazaException('updateOffer was called without a valid offer. Expected type Bol\Plaza\API\Models\OfferUpdate');
			}
		}
		
		/**
		 * Delete an offer on the server.
		 * @param $offerId
		 */
		public function deleteOffer($offerId) {
			$offerId = trim($offerId);
			if ($offerId === '') {
				throw new PlazaException('Missing offerId parameter');
			}
			$result = $this->Comms->plazaCall('/offers/v1/'.urlencode($offerId), 'DELETE');
		}
		
		/**
		 * Fetch the download URL for a listing of your offers, optionally filtered by "published" (true/false)
		 * @param boolean published true | false | null
		 */
		public function getOffersDownloadURL($published = null) {
			$filter = '';
			if ($published === true) {
				$filter = '?filter=published';
			} elseif ($published === false) {
				$filter = '?filter=not-published';
			}
			
				// @TODO: Remove the following hack. 
			$this->Comms->enableResponseHeaders = true; // Enable the returning of HTTP headers. Note: This is a hackish solution.
			$result = $this->Comms->plazaCall('/offers/v1/export'.$filter, 'GET');
			$this->Comms->enableResponseHeaders = false; // Disable again. This too is a hackish solution!
			
				
			if ($result['status']['http_code'] >= 200 && $result['status']['http_code'] < 300) {
				$numMatches = preg_match('/Location: (.*)/i',$result['payload'], $matches);
				if ($numMatches == 0) {
					throw new PlazaException(
						'No Download Location was provided by the Plaza API.',
						null,
						null
					);
				}
				return substr($matches[1], strrpos($matches[1], '/')+1);
			} else {
				$Exception = new PlazaException(
					'An error occured while updating your offer. See the getHTTPClientStatus(), getRawBody() and getHTTPError() methods on the Exception for more details.',
					null,
					null
				);
				$Exception -> setHTTPClientStatus($result['status']);
				$Exception -> setRawBody($result['payload']);
				$Exception -> setHTTPError($result['error']);
				throw $Exception;
			}
		}

		/**
		 * Download the specified remove filename to the provide tempfile.
		 * @Return true on success, false on failure 
		 */
		public function getOffersFromURL($remoteFilename, $tmpFile) {
				// As a download may take more than the normal 30 seconds we'll be going against best practices here by removing the time limit.
			set_time_limit(0);
			
				// Configure the Comms Class Instance to store the response a file, being "$tmpFile"
			$this->Comms->responseToFile($tmpFile);
			
				// Perform request 
			$result = $this->Comms->plazaCall('/offers/v1/export/'.$remoteFilename, 'GET');
			
			if ($result['status']['http_code'] >= 200 && $result['status']['http_code'] < 300) {
				return true;
			} else if ($result['status']['http_code'] == 412) {
				return false;
			} else {
				$Exception = new PlazaException(
					'An error occured while creating your offer. See the getHTTPClientStatus(), getRawBody() and getHTTPError() methods on the Exception for more details.',
					null,
					null
				);
				$Exception -> setHTTPClientStatus($result['status']);
				$Exception -> setRawBody($result['payload']);
				$Exception -> setHTTPError($result['error']);
				throw $Exception;
			}
		}	
		
		/**
		 * Returns an associative array of open orders.
		 * No XML / Objects to make the code as lightweight as possible and nice and easy to work with.
		 */
		public function getOpenOrders() {
			$response = $this->Comms->plazaCall('/services/rest/orders/v1/open', 'GET');
			
				// Convert the response XML into an array 
			$xmlObject = new \DOMDocument();
			$xmlObject -> loadXML($response['payload']);
			
				//Return the array representation of the xml
			return Tools::xmlToArray($xmlObject);
		}


		/**
		 * Informing the server of shipping and cancellation notifications is done in 1 or more batches.
		 *
		 * This method instantiates a new object that will hold the batch of shipping/cancellation notifications.
		 * A single batch can hold up to 100 notifications.
		 */
		public function createNewOrderProcessingBatch() {
			$this->OrderTransaction = new ProcessOrder();
			$this->OrderTransactionSize = 0;
		}
		
		/**
		 * This methods gathers all the required input and compiles the XML to send to /services/rest/orders/v1/process
		 * @Throws: Exception
		 */
		public function commitProcessOrderBatch() {
			if ($this->OrderTransactionSize < 1)
				throw new PlazaException('Unable to commit empty order batch. Adding items first.');
			
			$xml = $this->OrderTransaction->toXml();
			$response = $this->Comms->plazaCall('/services/rest/orders/v1/process', 'POST', $xml);
				// Convert the response XML into an array 
			$xmlObject = new \DOMDocument();
			$xmlObject -> loadXML($response['payload']);
			
				//XML to Array
			$responseArray = Tools::xmlToArray($xmlObject);
			
			if ($this->debug) Tools::debug($xml, true);
			
			if (isset($responseArray['bns:ProcessOrdersResult']['bns:ProcessOrderId'])) {
					// Clean up
				unset($this->OrderTransaction);
				$this->OrderTransactionSize = 0;
				
					// Bring home the spoils of war.
				return $responseArray['bns:ProcessOrdersResult']['bns:ProcessOrderId'];
			}
			else throw new PlazaException("Unexpected response. Expected bns:ProcessOrdersResult but received ".print_r($responseArray, true));
		}


		/**
		 * Add a shipment to an Order Processing transaciont
		 * @param $a Array containing the Shipping Notification which is structured as follows:
		 * Array(
		 *	'OrderId'		=> 98765, // REQUIRED
		 *  'DateTime'		=> YYYY-MM-DDTHH:MM:SS	//OPTIONAL. Will default to the current date/time when omitted/false. NOTE: MUST BE IN GMT!
		 *	'Transporter'	=> Array( // OPTIONAL
		 *		'Code'		=>'TNT', // REQUIRED
		 *		'TrackAndTraceCode'=>'Trutjeprot' // OPTIONAL
		 *	),
		 *	'OrderItems'	=> Array( // REQUIRED
		 *		'id' => Array(12,34,56) // REQUIRED
		 *	)
		 * );
		 * 
		 * @Throws: Exception
		 * 
		 * NOTE: For current allowed values for Transporter=>Code see the 
		 * API documentation at http://developers.bol.com
		 * On March the 4th, 2014 this list was: BPOST_BE, DHL, DHLFORYOU, 
		 * DHL4YOU, DPD_BE, DPD_NL, FEDEX_BE, FEDEX_NL, KIALA_BE, KIALA_NL, 
		 * SLV, TNT, TNT_EXTRA, UPS
		 */
		public function addShipmentToOrderProcessingBatch($a) {
			if ($this->OrderTransactionSize > 99)
				throw new \Exception('Error while adding shipment to batch. Too many items in batch. Max batch size is 100');
			
			if (is_array($a)) {
				$this->OrderTransaction->addShipmentToOrderProcessingBatch($a);
				$this->OrderTransactionSize ++;
			} else
				throw new PlazaException('Invalid input. Associative array expected');
		}
		
		/**
		 * Add a cancellation to an Order Processing transaciont
		 * @param $a Array containing the Cancallation notification which is structured as follows:
		 * Array(
		 *	'OrderId'		=> 98765,				// REQUIRED
		 *  'DateTime'		=> YYYY-MM-DDTHH:MM:SS	//OPTIONAL. Will default to the current date/time when omitted/false. NOTE: MUST BE IN GMT!
		 *	'ReasonCode'	=> 'OUT_OF_STOCK', 		// REQUIRED
		 *	'OrderItems'	=> Array(
		 *		'id' => Array(12,34,56)
		 *	)
		 * );
		 * @Throws: Exception
		 */
		public function addCancellationToOrderProcessingBatch($a) {
			if ($this->OrderTransactionSize > 99)
				throw new PlazaException('Error while adding cancellation to batch. Too many items in batch. Max batch size is 100');
			
			if (is_array($a)) {
				$this->OrderTransaction->addCancellationToOrderProcessingBatch($a);
				$this->OrderTransactionSize ++;
			} else
				throw new PlazaException('Invalid input. Associative array expected');
		}
		
		/**
		 * Get OrderProcessingStatus
		 * When pushing a batch of OrderProcessing items to the server they are placed in a queue (cache) first.
		 * Once processed, the result of the batch can be read using the method below.
		 * The only way to know your batch was processed is by periodically polling the server.
		 * DO NOT create a loop that constantly polls as this could trigger the rate limiting mechanism and
		 * cause your server to be blocked for a period of time.
		 * 
		 * @param $processingId {Integer} The queue id your batch was assigned with.
		 * 
		 */
		 public function getProcessingStatus($processingId) {
		 	if (!$processingId)
				throw new PlazaException('Invalid (no) processing ID received.');
			
			$response = $this->Comms->plazaCall('/services/rest/orders/v1/process/'.$processingId, 'GET');
				
				// Convert the response XML into an array 
			$xmlObject = new \DOMDocument();
			$xmlObject -> loadXML($response['payload']);
			
				//Return the array representation of the xml
			return Tools::xmlToArray($xmlObject);
		 }


		/**
		 * Fetch a payment overview from the server.\
		 * @param year Four digit representation of the year
	 	 * @param month One or 2 digit representation of the month
		 * @Throws: Exception
		 */
		 public function getPaymentsForMonth($year, $month) {
		 	if (!is_numeric($year) || $year < 1970 || $year > 2100) {
		 		throw new PlazaException('Invalid Year "'.$year.'". Minimum value is 1970, maximum value is 2100');
		 	}
			if (!is_numeric($month) || $month < 1 || $month > 12) {
		 		throw new PlazaException('Invalid Month "'.$month.'". Minimum value is 1, maximum value is 12');
		 	}

			$month = str_pad($month, 2, '0', STR_PAD_LEFT);
			
			$yearmonth = (string)$year.(string)$month;

				// Perform call			
			$response = $this->Comms->plazaCall('/services/rest/payments/v1/payments/'.$yearmonth, 'GET');

				// Convert the response XML into an array 
			$xmlObject = new \DOMDocument();
			$xmlObject -> loadXML($response['payload']);
			
				//Return the array representation of the xml
			return Tools::xmlToArray($xmlObject);
		}
	}

	
/**
*	Here's some demo code. 
*	We'll perform a couple of calls that actually work.
*/


	
	
?>
