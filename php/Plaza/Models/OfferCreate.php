<?php

	namespace Bol\Plaza\API\Models;
	
	use Bol\Plaza\API\Exceptions\PlazaException;
	
	class OfferCreate extends OfferUpdate {
		
		protected $EAN;
		protected $Condition;
		protected $QuantityInStock;
		
		protected $_xmlRootElement = 'OfferCreate';
		
		
		/**
		 * Constructor accepts an associative array with the keys OfferId, EAN, Condition, Price, DeliveryCode, QuantityInStock, Publish, ReferenceCode, Description 
		 */
		public function __construct($offer = false) {
			parent::__construct($offer);
			
			if (!isset($offer['EAN']) || !isset($offer['Condition'])) {
				throw new PlazaException('Invalid input instantiating new offer create model');
			}
			$this->SetEAN($offer['EAN']);
			$this->SetCondition($offer['Condition']);
			$this->SetQuantityInStock($offer['QuantityInStock']);
			
		}
		
		public function SetQuantityInStock($QuantityInStock) {
			if (is_numeric($QuantityInStock) && $QuantityInStock > -1 ) {
				$this->QuantityInStock = $QuantityInStock;
			} else {
				throw new \Exception('Invalid QuantityInStock "'.$QuantityInStock.'"');
			}
		}
		
				
		public function SetEAN($EAN) {
			if (is_numeric($EAN) && (strlen($EAN)==13 || strlen($EAN) == 10 )) {
				$this->EAN = $EAN;
			} else {
				throw new \Exception('Invalid length for EAN/ISBN "'.$EAN.'"');
			}
		}
		
		
		public function SetCondition($Condition) {
			$Condition = trim(strtoupper($Condition));
			if (in_array($Condition, array('NEW','AS_NEW','GOOD','REASONABLE','MODERATE'))) {
				$this->Condition = $Condition;				
			} else {
				throw new \Exception('Invalid Condition "'.$Condition.'"');
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
		
	}
?>