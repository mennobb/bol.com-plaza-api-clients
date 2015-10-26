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
	}

	/**
	 * Returns an associative array of open orders.
	 * No XML / Objects to make the code as lightweight as possible and nice and easy to work with.
	 */
	public function getOpenOrders() {
		$responseArray = $this->Comms->plazaCall('/services/rest/orders/v1/open', 'GET');
		$this->fixOpenOrdersResponse($responseArray);
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
		$this->fixProcessingStatusResponse($responseArray);
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
		$this->fixPaymentsForMonthResponse($responseArray);
		return $responseArray;
	}

	/**
	 * The fact that this method is needed shows the biggest downside of using xml to array conversion in this SDK.
	 * This method hardly uses extra memory and is quite fast.
	 */
	private function fixOpenOrdersResponse(&$o) {
		// Allow iterating over an order stream that only has 1 order.
		if (isset($o['bns:OpenOrders']['bns:OpenOrder']['bns:OrderId']))
			$o['bns:OpenOrders']['bns:OpenOrder'] = Array($o['bns:OpenOrders']['bns:OpenOrder']);

		// Allow iterating orderItems if there's only 1 order item.
		foreach ($o['bns:OpenOrders']['bns:OpenOrder'] as $key => $singleOrder)
			if (!isset($singleOrder['bns:OpenOrderItems']['bns:OpenOrderItem'][0]))
				$o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:OpenOrderItems']['bns:OpenOrderItem'] = array($singleOrder['bns:OpenOrderItems']['bns:OpenOrderItem']);

		// Replace empty arrays for empty strings in the orderItems.
		// They occur when the original XML contained something like <deliveryPeriod></deliveryPeriod>
		foreach ($o['bns:OpenOrders']['bns:OpenOrder'] as $key => $singleOrder)
			foreach ($o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:OpenOrderItems']['bns:OpenOrderItem'] as $ooKey => $singleOrderedItem)
				$o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:OpenOrderItems']['bns:OpenOrderItem'][$ooKey] = \Bol\Plaza\Classes\Tools::replaceEmptyArraysWithEmptyStrings($singleOrderedItem);

		// Replace missing values or values of type "array" in the address details
		$allFields = array('bns:SalutationCode', 'bns:FirstName', 'bns:Surname', 'bns:Streetname', 'bns:Housenumber', 'bns:HousenumberExtended', 'bns:AddressSupplement', 'bns:ZipCode', 'bns:City', 'bns:CountryCode', 'bns:Email', 'bns:Telephone', 'bns:Company');
		foreach ($o['bns:OpenOrders']['bns:OpenOrder'] as $key => $singleOrder)
			foreach ($o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:Buyer'] as $addrType => $addrDetails)
				foreach ($o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:Buyer'][$addrType] as $addrDetailItemKey => $addrDetailItemValue)
					foreach ($allFields as $field)
						if (!isset($o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:Buyer'][$addrType][$field]) || is_array($o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:Buyer'][$addrType][$field]))
							$o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:Buyer'][$addrType][$field] = '';

		// Replace missing values or values of type "array" in all orderItems
		$allFields = array('bns:OrderItemId', 'bns:EAN', 'bns:ReferenceCode', 'bns:Title', 'bns:Quantity', 'bns:Price', 'bns:DeliveryPeriod', 'bns:TransactionFee');
		foreach ($o['bns:OpenOrders']['bns:OpenOrder'] as $key => $singleOrder)
			foreach ($o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:OpenOrderItems']['bns:OpenOrderItem'] as $ooItemKey => $ooItem)
				foreach($o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:OpenOrderItems']['bns:OpenOrderItem'][$ooItemKey] as $ooItemDetailKey => $ooItemDetailValue)
					foreach ($allFields as $field)
						if (!isset($o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:OpenOrderItems']['bns:OpenOrderItem'][$ooItemKey][$field]) || is_array($o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:OpenOrderItems']['bns:OpenOrderItem'][$ooItemKey][$field]))
							$o['bns:OpenOrders']['bns:OpenOrder'][$key]['bns:OpenOrderItems']['bns:OpenOrderItem'][$ooItemKey][$field] = '';
		// Done... No "return $o;" because we operated on a var passed by reference.
	}

	private function fixProcessingStatusResponse(&$o) {
		if (isset($o['bns:ProcessOrdersOverview']['bns:Order']['bns:OrderId']))
			$o['bns:ProcessOrdersOverview']['bns:Order'] = array($o['bns:ProcessOrdersOverview']['bns:Order']);

		foreach ($o['bns:ProcessOrdersOverview']['bns:Order'] as $orderIdx => $order)
			if (isset($o['bns:ProcessOrdersOverview']['bns:Order'][$orderIdx]['bns:OrderItemList']['bns:OrderItemData']['bns:OrderItemId']))
				$o['bns:ProcessOrdersOverview']['bns:Order'][$orderIdx]['bns:OrderItemList']['bns:OrderItemData'] = Array($o['bns:ProcessOrdersOverview']['bns:Order'][$orderIdx]['bns:OrderItemList']['bns:OrderItemData']);

		// Done... No "return $o;" because we operated on a var passed by reference.
	}

	/**
	 * TODO: Make this method as "complete" as the fixOpenOrdersResponse() method.
	 * Now it doesn't check for missing fields or fields whose empty value was turned into an array instead of an empty string.
	 */
	private function fixPaymentsForMonthResponse(&$o) {
		// Allow iterating over a payment stream that only has 1 payment.
		if (isset($o['bns:Payments']['Payment']['bns:CreditInvoiceNumber']))
			$o['bns:Payments']['Payment'] = array($o['bns:Payments']['Payment']);

		foreach ($o['bns:Payments']['Payment'] as $paymentIdx => $payment)
			if (isset($o['bns:Payments']['Payment'][$paymentIdx]['PaymentShipments']['PackageSlipNumber']))
				$o['bns:Payments']['Payment'][$paymentIdx]['PaymentShipments'] = array($o['bns:Payments']['Payment'][$paymentIdx]['PaymentShipments']);

		foreach ($o['bns:Payments']['Payment'][$paymentIdx]['PaymentShipments'] as $paymentShipmentIdx => $paymentShipment)
			if (isset($o['bns:Payments']['Payment'][$paymentIdx]['PaymentShipments'][$paymentShipmentIdx]['PaymentShipmentItems']['PaymentShipmentItem']['OrderItemId']))
				$o['bns:Payments']['Payment'][$paymentIdx]['PaymentShipments'][$paymentShipmentIdx]['PaymentShipmentItems']['PaymentShipmentItem'] = array($o['bns:Payments']['Payment'][$paymentIdx]['PaymentShipments'][$paymentShipmentIdx]['PaymentShipmentItems']['PaymentShipmentItem']);

		// Done... No "return $o;" because we operated on a var passed by reference.
	}
}
