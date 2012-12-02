<?php

class Request {
	
	private $request;
	
	private $data;
	
	public function __construct(array $request) {
		$this->request = $request;
		$this->data = array();
	}
	
	public function has($key) {
		return array_key_exists($key, $this->request);
	}
	
	public function get($key) {
		return I($this->request, $key);
	}
	
	public function hasData($key) {
		return array_key_exists($key, $this->data);
	}
	
	public function getData($key) {
		return I($this->data, $key);
	}
	
	public function setData($key, $value) {
		$this->data[$key] = $value;
	}
	
}
