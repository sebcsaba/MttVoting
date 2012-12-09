<?php

class DeleteBuilder extends SQLBuilder {
	
	/**
	 * Az utasítás által érintett tábla
	 *
	 * @var string
	 */
	private $table;
	
	/**
	 * @return DeleteBuilder
	 */
	public static function create() {
		return new self();
	}
	
	/**
	 * Beállítja hogy melyik táblából fogunk törölni
	 * 
	 * @param string $table
	 * @return DeleteBuilder
	 */
	public function from($table) {
		$this->table = $table;
		return $this;
	}
	
	/**
	 * Returns the string representation of this SQL
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return string
	 */
	public function convertToString(DbDialect $dialect = null) {
		return 'DELETE FROM ' . $this->table . $this->getWhereClause();
	}

	/**
	 * Returns the array of the parameters of this SQL
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return array
	 */
	public function convertToParamsArray(DbDialect $dialect = null) {
		return $this->getWhereData();
	}
	
}
