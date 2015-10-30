<?php

	namespace Bol\Plaza\Classes;
	
	use Bol\Plaza\Classes\Tools;
	
	class ProcessOrder {
		
		private $OrderTransaction;
		
		public function __construct() {
			$this->OrderTransaction = Array(
				'Shipments'		=> Array(),
				'Cancellations'	=> Array()
			);
		}
		
		public function addShipmentToOrderProcessingBatch($a) {
				// The new array newItem is used to make sure the ordering of the fields is correct;
			$newItem = Array();
			
			if (!$a['OrderId']) {
				throw new \Exception('OrderId is a required field');
			}
			$newItem['OrderId'] = $a['OrderId'];
				
			if (!isset($a['DateTime'])) {
				$a['DateTime'] = Tools::getGMTDateTime();
			}			
			$newItem['DateTime'] = $a['DateTime'];

			if (!isset($a['Transporter']) || !is_array($a['Transporter'])) {
				unset($a['Transporter']);
			} elseif (!isset($a['Transporter']['Code'])) {
				throw new \Exception('When providing the Transporter Array, at least include the "Code" field');
			} else {
				$newItem['Transporter'] = $a['Transporter'];
			}

			if (!isset($a['OrderItems']) || !is_array($a['OrderItems'])) {
				throw new \Exception('Please provide a numerical array (common array) with OrderItems');
			}
			$newItem['OrderItems'] = $a['OrderItems'];
			
			if (isset($a['DateExpectedDelivery'])) {
				$newItem['DateExpectedDelivery'] = trim($a['DateExpectedDelivery']);
				if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $newItem['DateExpectedDelivery']) === 1) {
					$newItem['DateExpectedDelivery'] = $a['DateExpectedDelivery'];
				} else {
					throw new \Exception('Invalid value for the DateExpectedDelivery field. Expected format = YYYY-MM-DD');
				}
			}
				// If we made it here everything must be allright (As far as we checked... which isn't very far.).
			$this->OrderTransaction['Shipments'][] = $newItem;
		}
		
		public function addCancellationToOrderProcessingBatch($a) {
				// The new array newItem is used to make sure the ordering of the fields is correct;
			$newItem = Array();
			
			if (!$a['OrderId']) {
				throw new \Exception('OrderId is a required field');
			}
			$newItem['OrderId'] = $a['OrderId'];

			if (!isset($a['DateTime'])) {
				$a['DateTime'] = Tools::getGMTDateTime();
			}
			$newItem['DateTime'] = $a['DateTime'];
			
			if (!isset($a['ReasonCode']) || !$a['ReasonCode'] || ($a['ReasonCode'] !== 'OUT_OF_STOCK' && $a['ReasonCode'] !== 'REQUESTED_BY_CUSTOMER')) {
				throw new \Exception('A "ReasonCode" of either "OUT_OF_STOCK" or "REQUESTED_BY_CUSTOMER" is required.');
			}
			$newItem['ReasonCode'] = $a['ReasonCode'];
			
			if (!isset($a['OrderItems']) || !is_array($a['OrderItems'])) {
				throw new \Exception('Please provide a numerical array (common array) with OrderItems');
			}
			$newItem['OrderItems'] = $a['OrderItems'];
			
				// If we made it here everything must be allright (As far as we checked... which isn't very far.).
			$this->OrderTransaction['Cancellations'][] = $newItem;
		}
		
		public function toXml() {
			$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<ProcessOrders xmlns="http://plazaapi.bol.com/services/xsd/plazaapiservice-1.0.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://plazaapi.bol.com/services/xsd/plazaapiservice-1.0.xsd plazaapiservice-1.0.xsd ">'."\n";
			
				// Shipments
			if (count($this->OrderTransaction['Shipments'])>0) {
				$xml .= "	<Shipments>\n";
				foreach ($this->OrderTransaction['Shipments'] as $shipment) {
					$xml .= "		<Shipment>\n";
					$xml .= Tools::arrayToXML($shipment, 3);
					$xml .= "		</Shipment>\n";
				}
				$xml .= "	</Shipments>\n";
			}
			
				// Cancellations
			if (count($this->OrderTransaction['Cancellations'])>0) {
				$xml .= "	<Cancellations>\n";
				foreach ($this->OrderTransaction['Cancellations'] as $cancellation) {
					$xml .= "		<Cancellation>\n";
					$xml .= Tools::arrayToXML($cancellation, 3);
					$xml .= "		</Cancellation>\n";
				}
				$xml .= "	</Cancellations>\n";
			}
			
			$xml .= '</ProcessOrders>';
			return $xml;
		}
	}
?>
