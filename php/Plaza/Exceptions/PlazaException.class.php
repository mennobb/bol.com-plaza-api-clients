<?php

namespace Bol\Plaza\API\Exceptions;

class PlazaException extends \Exception {
	
	/**
	 * @param curlStatus array as provided by cURL
	 */
	protected $HTTPClientStatus;
	
	/**
	 * @param rawBody contains the unparsed http call response body 
	 */
	protected $rawBody;
	
	/**
	 * @param httpError contains the error provided by cURL
	 */
	protected $httpError;
	
	// Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
	
	/**
	 * @param curlStatus array as provided by cURL
	 */
	public function setHTTPClientStatus($HTTPClientStatus) {
		$this -> HTTPClientStatus = $HTTPClientStatus;
	}
	
	/**
	 * Get curlStatus array as provided by cURL
	 */
	public function getHTTPClientStatus() {
		return $this -> HTTPClientStatus;
	}

	/**
	 * @param curlStatus array as provided by cURL
	 */
	public function setRawBody($rawBody) {
		$this -> rawBody = $rawBody;
	}
	
	/**
	 * Get curlStatus array as provided by cURL
	 */
	public function getRawBody() {
		return $this -> rawBody;
	}
		
	/**
	 * @param httpError array as provided by cURL
	 */
	public function setHTTPError($httpError) {
		$this -> httpError = $httpError;
	}
	
	/**
	 * Get curlStatus array as provided by cURL
	 */
	public function getHTTPError() {
		return $this -> httpError;
	}
	
}

?>