<?php

	namespace Bol\Plaza\API\Models;
	
	use Bol\Plaza\API\Exceptions\PlazaException;
	
	class OfferUpdate {
		
		protected $OfferId;
		protected $Price;
		protected $DeliveryCode;
		protected $Publish;
		protected $ReferenceCode;
		protected $Description;
		
		protected $_xmlRootElement = 'OfferUpdate';
		
		/**
		 * Constructor accepts an associative array with the keys OfferId, EAN, Condition, Price, DeliveryCode, QuantityInStock, Publish, ReferenceCode, Description 
		 */
		public function __construct($offer = false) {
			if (!is_array($offer) 
				|| $offer === false
				|| !isset($offer['OfferId'])
				|| !isset($offer['Price'])
				|| !isset($offer['DeliveryCode'])
				|| !isset($offer['Publish'])
				|| !isset($offer['ReferenceCode'])
				|| !isset($offer['Description'])) {
				throw new \Exception('Invalid input creating new offer');
			}
			
			$this->SetOfferId($offer['OfferId']); 
			$this->SetPrice($offer['Price']); 
			$this->SetDeliveryCode($offer['DeliveryCode']); 
			$this->SetPublish($offer['Publish']); 
			$this->SetReferenceCode($offer['ReferenceCode']); 
			$this->SetDescription($offer['Description']);
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
		
		
		public function SetPrice($Price) {
			$newPrice = floatval($Price);
			if ((string)$newPrice == (string)$newPrice && $newPrice > 0) {
				$this->Price = $newPrice;
			} else {
				throw new \Exception('Invalid Price "'.$Price.'"');
			}
		}
		
		
		public function SetDeliveryCode($DeliveryCode) {
			if (strlen($DeliveryCode) > 0) {
				$this->DeliveryCode = $DeliveryCode;
			} else {
				throw new \Exception('Invalid Delivery Code "'.$DeliveryCode.'"');
			}
			
		}
		
		
		public function SetPublish($Publish = false) {
			$Publish = filter_var($Publish, FILTER_VALIDATE_BOOLEAN);
			$this->Publish = $Publish?'true':'false';
		}
		
		
		public function SetReferenceCode($ReferenceCode) {
			if (strlen($ReferenceCode) < 21) {
				$this->ReferenceCode = (string)$ReferenceCode;
			} else {
				throw new \Exception('Invalid ReferenceCode "'.$ReferenceCode.'"');
			}
		}
		
		
		public function SetDescription($Description = '') {
			if (strlen($Description) < 2000) {
				$this->Description = (string)$Description;
			} else {
				throw new \Exception('Description too long: "'.$Description.'"');
			}
		}
		
		public function toXML() {
			$offerProperties = get_object_vars($this);

			$xml = array();
			$xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
			$xml[] = '<'.$this->_xmlRootElement.' xmlns="http://plazaapi.bol.com/offers/xsd/api-1.0.xsd">';
			foreach ($offerProperties as $key=>$value) {
				if ($key !== 'OfferId' && $key[0] !== '_') {
					$xml[]="\t<".$key.'>'.htmlspecialchars($value).'</'.$key.'>';
				}
			}		
			$xml[] = '</'.$this->_xmlRootElement.'>';
			
			return implode("\n", $xml);
		}
	}

?>