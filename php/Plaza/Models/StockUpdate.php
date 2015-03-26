<?php

	namespace Bol\Plaza\Models;
	
	class StockUpdate {
		
		protected $OfferId;
		protected $QuantityInStock;
		
		/**
		 * Constructor accepts an associative array with the keys OfferId, EAN, Condition, Price, DeliveryCode, QuantityInStock, Publish, ReferenceCode, Description 
		 */
		public function __construct($offer = false) {
			if (!is_array($offer) 
				|| $offer === false
				|| !isset($offer['OfferId'])
				|| !isset($offer['QuantityInStock'])) {
				throw new \Exception('Invalid input creating new offer');
			}
			
			$this->SetOfferId($offer['OfferId']); 
			$this->SetQuantityInStock($offer['QuantityInStock']);
		}

		/**
		 * Generic getter
		 */
		public function __get($property) {
			if (property_exists($this, $property)) {
				return $this->$property;
			}
		}
		
		
		public function setOfferId($OfferId) {
			if (strlen($OfferId) > 0 && strlen($OfferId) < 64) {
				$this->OfferId = $OfferId;
			} else {
				throw new \Exception('Invalid OfferId "'.$OfferId.'"');
			}
		}
		
		
		public function SetQuantityInStock($QuantityInStock) {
			if (is_numeric($QuantityInStock) && $QuantityInStock > -1 ) {
				$this->QuantityInStock = $QuantityInStock;
			} else {
				throw new \Exception('Invalid QuantityInStock "'.$QuantityInStock.'"');
			}
		}
		
		public function toXML() {
			$xml = array();
			$xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
			$xml[] = '<StockUpdate xmlns="http://plazaapi.bol.com/offers/xsd/api-1.0.xsd">';
			$xml[] = '	<QuantityInStock>'.$this->QuantityInStock.'</QuantityInStock>';
			$xml[] = '</StockUpdate>';
			
			return implode("\n", $xml);
		}
	}

?>