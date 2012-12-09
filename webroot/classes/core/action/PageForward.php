<?php

class PageForward implements Forward {

	private $page;
	
	public function __construct($page) {
		$this->page = $page;
	}
	
	public function getPage() {
		return $this->page;
	}
	
}
