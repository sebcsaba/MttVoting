<?php

class DbException extends Exception {
	
	private $errorMessage;
	private $queryString;
	private $params;
	
	public function __construct($errorMessage, $queryString = '--', array $params = array()) {
		parent::__construct(sprintf('%s on executing %s with parameters %s', $errorMessage, $queryString, implode_assoc($params,'=','',';')));
		$this->errorMessage = $errorMessage;
		$this->queryString = $queryString;
		$this->params = $params;
	}
	
	public function getErrorMessage() {
		return $this->errorMessage;
	}

	public function getQueryString() {
		return $this->queryString;
	}

	public function getParams() {
		return $this->params;
	}
	
}
