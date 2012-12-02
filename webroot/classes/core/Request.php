<?php

class Request {
	
	private $request;
	
	public function __construct(array $request) {
		$this->request = $request;
	}
	
	public function has($key) {
		return array_key_exists($key, $this->request);
	}
	
	public function get($key) {
		if ($this->has($key)) {
			return $this->request[$key];
		} else {
			return null;
		}
	}
	
}