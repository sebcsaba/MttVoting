<?php

class Request {
	
	/**
	 * @var array
	 */
	private $headers;
	
	/**
	 * @var array
	 */
	private $request;
	
	/**
	 * @var array
	 */
	private $data;
	
	/**
	 * @var User
	 */
	private $user;
	
	public function __construct(array $headers, array $request, User $user = null) {
		$this->headers = array();
		foreach ($headers as $name=>$value) {
			$this->headers[strtolower($name)] = $value;
		}
		$this->request = $request;
		$this->data = array();
		$this->user = $user;
	}
	
	/**
	 * @return User or null
	 */
	public function getUser() {
		return $this->user;
	}
	
	public function isAjax() {
		return strtolower(I(getallheaders(),'X-Requested-With'))==strtolower('XMLHttpRequest');
	}
	
	public function getHeader($name) {
		return I($this->headers, $name);
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
