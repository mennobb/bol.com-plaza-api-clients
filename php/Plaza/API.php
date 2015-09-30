<?php
	
	namespace Bol\Plaza;
	
	use Bol\Plaza\Classes\Comms;
	use Bol\Plaza\Classes\Tools;
	use Bol\Plaza\Classes\ProcessOrder;
	
	/**
	 * * To make the code as lightweight as possible we use as little classes as possible.
	 */
	
	class API {
			/*
			 * Holds the instance of the Comms class that takes care of the http communication
			 */
		private $Comms;
		
			/*
			 * Hold the instance of the Tools class. Handier than a Swiss Army knife
			 */
		private $Tools;
		
			/*
			 * Holds the Array that forms the basis for the ProcessOrders xml
			 */
		private $OrderTransaction;
		
			/*
			 * Holds the count of $OrderTransaction to avoid doing relatively expensive count operations
			 */
		private $OrderTransactionSize;
		
		
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
			
			//$this -> Tools = new PlazaAPITools();
		}
		
		/**
		 * Returns an associative array of open orders.
		 * No XML / Objects to make the code as lightweight as possible and nice and easy to work with.
		 */
		public function getOpenOrders() {
			$responseArray = $this->Comms->plazaCall('/services/rest/orders/v1/open', 'GET');
			return $responseArray; 
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
				throw new \Exception('Unable to commit empty order batch. Adding items first.');
			
			$xml = $this->OrderTransaction->toXml();
			$responseArray = $this->Comms->plazaCall('/services/rest/orders/v1/process', 'POST', $xml);
			if ($this->debug) \Bol\Plaza\API\Tools::debug($xml, true);
			
			if (isset($responseArray['bns:ProcessOrdersResult']['bns:ProcessOrderId'])) {
					// Clean up
				unset($this->OrderTransaction);
				$this->OrderTransactionSize = 0;
				
					// Bring home the spoils of war.
				return $responseArray['bns:ProcessOrdersResult']['bns:ProcessOrderId'];
			}
			else throw new \Exception("Unexpected response. Expected bns:ProcessOrdersResult but received ".print_r($responseArray, true));
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
				throw new \Exception('Invalid input. Associative array expected');
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
				throw new \Exception('Error while adding cancellation to batch. Too many items in batch. Max batch size is 100');
			
			if (is_array($a)) {
				$this->OrderTransaction->addCancellationToOrderProcessingBatch($a);
				$this->OrderTransactionSize ++;
			} else
				throw new \Exception('Invalid input. Associative array expected');
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
				throw new \Exception('Invalid (no) processing ID received.');
			
			$responseArray = $this->Comms->plazaCall('/services/rest/orders/v1/process/'.$processingId, 'GET');
			return $responseArray;
		 }


		/**
		 * Fetch a payment overview from the server.\
		 * @param year Four digit representation of the year
	 	 * @param month One or 2 digit representation of the month
		 * @Throws: Exception
		 */
		 public function getPaymentsForMonth($year, $month) {
		 	if (!is_numeric($year) || $year < 1970 || $year > 2100) {
		 		throw new \Exception('Invalid Year "'.$year.'". Minimum value is 1970, maximum value is 2100');
		 	}
			if (!is_numeric($month) || $month < 1 || $month > 12) {
		 		throw new \Exception('Invalid Month "'.$month.'". Minimum value is 1, maximum value is 12');
		 	}

			$month = str_pad($month, 2, '0', STR_PAD_LEFT);
			
			$yearmonth = (string)$year.(string)$month;
			
			$responseArray = $this->Comms->plazaCall('/services/rest/payments/v1/payments/'.$yearmonth, 'GET');
			return $responseArray;
		}
	}

	
/**
*	Here's some demo code. 
*	We'll perform a couple of calls that actually work.
*/


	
	
?>
