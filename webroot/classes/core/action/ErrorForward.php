<?php

class ErrorForward implements Forward {
	
	/**
	 * @var string
	 */
	private $message;
	
	public function __construct($message) {
		$this->message = $message;
	}
	
	public function getMessage() {
		return $this->message;
	}
	
}
