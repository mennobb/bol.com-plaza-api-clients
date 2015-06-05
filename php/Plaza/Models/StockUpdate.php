<?php

namespace Bol\Plaza\Models;

use Bol\Plaza\Exceptions\PlazaException;

class StockUpdate {
	
	protected $OfferId;
	protected $QuantityInStock;
	
	/**
	 * Constructor accepts an associative array with the keys OfferId, EAN, Condition, Price, DeliveryCode, QuantityInStock, Publish, ReferenceCode, Description 
	 */
	public function __construct($offer = false) {
		$errorMessage = 'Invalid input while instantiating stock update: ';

		if (!is_array($offer)) 
			throw new PlazaException($errorMessage.'Array expected.');
		
		if ($offer === false) 
			throw new PlazaException($errorMessage.'No $offer parameter specified.');

		if (!isset($offer['OfferId']))
			throw new PlazaException($errorMessage.'OfferId not set.');

		if (!isset($offer['QuantityInStock']))
			throw new PlazaException($errorMessage.'QuantityInStock not set.');
		
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
		if (strlen($OfferId) > 0 && strlen($OfferId) < 65) {
			$this->OfferId = $OfferId;
		} else {
			throw new PlazaException('Invalid OfferId "'.$OfferId.'"');
		}
	}
	
	
	public function SetQuantityInStock($QuantityInStock) {
		if (is_numeric($QuantityInStock) && $QuantityInStock > -1 ) {
			$this->QuantityInStock = $QuantityInStock;
		} else {
			throw new PlazaException('Invalid QuantityInStock "'.$QuantityInStock.'"');
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