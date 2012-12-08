<?php

class Request {
	
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
	
	public function __construct(array $request, User $user = null) {
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
