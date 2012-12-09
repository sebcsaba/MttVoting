<?php

class Config {

	private $data;
	
	public function __construct(array $data) {
		$this->data = $data;
	}
	
	public function get($path) {
		$result =& $this->data;
		foreach (split('/', $path) as $index) {
			$result =& $result[$index];
		}
		return $result;
	}
	
}
