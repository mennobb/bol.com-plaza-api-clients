<?php

	namespace Bol;
	
	require_once dirname(__file__).'/Plaza/API.class.php';
	
	
		// Make print_r's output look nice without added effort.
	//header('Content-type: text/plain');
	
		
	/**
	 * Instantiate the class by passing it the public and private key.
	 * The third (boolean) parameter tell the class to use the test environment (true) or the live environment (false)
	 * The fourth (boolean) parameter enables or disables the MINIMAL "debug" functionality.
	 */ 
	$PlazaAPI = new \Bol\Plaza\API('PuBL1CKEYT35T', 'PR1v@K3YT35T', true, false);
	
	
	
	/**
	 * This part of the code performs a simpel GET on the /services/rest/orders/v1/open API
	 * @return: Array of open orders or
	 */
	try {
			// Perform the logical first call: Get the open orders
		$openOrders = $PlazaAPI->getOpenOrders();
	} catch (Exception $e) {
		trigger_error($e, E_USER_ERROR);
	}
	// Uncomment om het resultaat als associative array te zien
	print_r($openOrders);




	/**
	 * This code demonstrates how to send 1 or more shipping/cancellation notifications to Bol.com
	 * This batch may hold between 1 and 100 items.
	 * 
	 * NOTE: After informing Bol.com of shippings / cancellations through the API you may still see
	 * your orders as "open" in the seller dashboard.
	 * 
	 * This has to do with system caches.
	 * The API briefly caches the input it receives and the seller dashboard also has it's own cache
	 * to offload the core systems. 
	 * 
	 * The most important feedback is the feedback one gets from the  getProcessingStatus() method.
	 * See the example code below for typical usage instructions.
	 */
	try {
			// Create a new batch. This is required before calling the methods "addShipmentToOrderProcessingBatch" or "addCancellationToOrderProcessingBatch"
		$PlazaAPI->createNewOrderProcessingBatch();
	
			// Add a shipment notification to the batch	
		$Shipment = Array(
			'OrderId'		=> 12345,
			'Transporter'	=> Array(
				'Code'		=>'BPOST_BE',
				'TrackAndTraceCode'=>'VLUGWATSNELNUTSJAKKA'
			),
			'OrderItems'	=> Array(
				'Id' => Array(12,34,56)
			),
			'DateExpectedDelivery' => '2015-06-15'
		);
		$PlazaAPI->addShipmentToOrderProcessingBatch($Shipment);
		
			// Add another shipment notification to the batch
		$Shipment = Array(
			'OrderId'		=> 98765,
			'Transporter'	=> Array(
				'Code'		=>'TNT',
				'TrackAndTraceCode'=>'1..2...HOPPAKEE'
			),
			'OrderItems'	=> Array(
				'Id' => Array(12,34,56)
			),
			'DateExpectedDelivery' => '2015-12-31'
		);
		$PlazaAPI->addShipmentToOrderProcessingBatch($Shipment);
		
			// Add a cancellation notification to the batch
		$Cancellation = Array(
			'OrderId'		=> 98765,
			'ReasonCode'	=> 'OUT_OF_STOCK',
			'OrderItems'	=> Array(
				'Id' => Array(12,34,56)
			)
		);
		$PlazaAPI->addCancellationToOrderProcessingBatch($Cancellation);

			
			// Done! Let's push this batch to the server
		$processOrderId = $PlazaAPI->commitProcessOrderBatch();
		
	} catch (Exception $e) {
		trigger_error($e, E_USER_ERROR);
	}


	/**
	 * Get the ProcessingStatus.
	 * It is important to check for each of the items in your order if they are contained within the OrderItemsList array.
	 * If not, they have NOT been accepted.
	 * All the SUCCESS statusses may make it seem like all is well but until you verify that each and every orderItem has been accepted you cannot be sure.
	 * 
	 * A sure sign of trouble is if orders that have been processed through the API have are still visible in the seller Dashboard after some time (say, a couple of hours at most)
	 */
	try {
		$PlazaAPI->getProcessingStatus($processOrderId);
	} catch (Exception $e) {
		trigger_error($e, E_USER_ERROR);
	}
	/**
	 * Have  you read the above comment about checking those orderItemIds in the processing status response???
	 * I can't stress that part enough...
	 * 
	 * Typically, the following orderItemData responses should be present.
	 * (
	 *		[bns:OrderId] => 1000126040
	 *		[bns:Status] => RECEIVED
	 *		[bns:DateTime] => 2014-03-07T15:52:01
	 *		[bns:OrderItemList] => Array
	 *			(
	 *				[bns:OrderItemData] => Array
	 *					(
	 *						[bns:OrderItemId] => 142767252
	 *						[bns:Process] => SHIP
	 *						[bns:Status] => RECEIVED //This status doesn't mean that Bol.com already processed your shipping notification.
	 *					)
	 *			)
	 *	)
	 *
	 * AND LATER
	 * 
	 * (
	 *		[bns:OrderId] => 1000126040
	 *		[bns:Status] => SUCCESS
	 *		[bns:DateTime] => 2014-03-07T15:52:01
	 *		[bns:OrderItemList] => Array
	 *			(
	 *				[bns:OrderItemData] => Array
	 *					(
	 *						[bns:OrderItemId] => 142767252
	 *						[bns:Process] => SHIP
	 *						[bns:Status] => SUCCESS //This status DOES mean that Bol.com processed your shipping notification. Yeeeey. Let us celebrate and drink milk.
	 *					)
	 *			)
	 *	)
	
	
	
	
	
	
	/**
	 * Get received payments overview for a specific month.
	 * 
	 * Note that there is no status to demonstrate that a payment has been removed from this list (due to orders that got returned to the seller).
	 */
	$paymentsArray = $PlazaAPI->getPaymentsForMonth(2014, 2);
	var_dump($paymentsArray);
	
?>