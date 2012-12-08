<?php

/**
 * Represents an SQL query or DML statement
 * 
 * @author sebcsaba
 */
interface SQL {

	/**
	 * Returns the string representation of this SQL
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return string
	 */
	public function convertToString(DbDialect $dialect = null);
	
	/**
	 * Returns the array of the parameters of this SQL
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return array
	 */
	public function convertToParamsArray(DbDialect $dialect = null);
	
}
