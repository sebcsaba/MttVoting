<?php

class RedirectForward implements Forward {
	
	private $location;
	
	public function __construct($location) {
		$this->location = $location;
	}

	public function getLocation() {
		return $this->location;
	}
	
}
