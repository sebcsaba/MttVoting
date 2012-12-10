<?php

class ValidationException extends Exception {
	
	public function __construct($message) {
		parent::__construct($message);
	}
	
}
