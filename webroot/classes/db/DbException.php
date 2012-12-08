<?php

class DbException extends Exception {

	/**
	 * @var string
	 */
	private $errorMessage;
	
	/**
	 * @var SQL
	 */
	private $query;
	
	public function __construct($errorMessage, SQL $query = null) {
		parent::__construct(self::fmtMessage($errorMessage, $query));
		$this->errorMessage = $errorMessage;
		$this->query = $query;
	}
	
	private static function fmtMessage($errorMessage, SQL $query = null) {
		if (is_null($query)) {
			return $errorMessage;
		} else {
			$params = implode_assoc($query->convertToParamsArray(),'=','',';');
			return sprintf('%s on executing %s with parameters %s', $errorMessage, $query->convertToString(), $params);
		}
	}

	/**
	 * @return string
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}

	/**
	 * @return SQL
	 */
	public function getQuery() {
		return $this->query;
	}

}
