<?php

/**
 * Represents a native SQL statement, where no further conversations are needed,
 * and can be executed by the engine immediately
 * 
 * @author sebcsaba
 */
class NativeSQL implements SQL {
	
	private $string;
	private $params;
	
	public function __construct($string, array $params = array()) {
		$this->string = $string;
		$this->params = $params;
	}
	
	/**
	 * Returns the string representation of this SQL
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return string
	 */
	public function convertToString(DbDialect $dialect = null) {
		return $this->string;
	}
	
	/**
	 * Returns the array of the parameters of this SQL
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return array
	 */
	public function convertToParamsArray(DbDialect $dialect = null) {
		return $this->params;
	}
	
}
