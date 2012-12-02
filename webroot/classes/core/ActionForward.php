<?php

class ActionForward implements Forward {

	private $className;
	
	public function __construct($className) {
		$this->className = $className;
	}
	
	public function getClassName() {
		return $this->className;
	}
	
}
