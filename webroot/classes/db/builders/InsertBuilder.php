<?php

class InsertBuilder implements SQL {
	
	/**
	 * Az utasítás által érintett tábla
	 *
	 * @var string
	 */
	private $table;
	
	/**
	 * Az utasítás által érintett mezők nevei
	 * 
	 * @var array
	 */
	private $setFields = array();
	
	/**
	 * Az utasítás SET clause-ai
	 *
	 * @var array
	 */
	private $setSql = array();
	
	/**
	 * Az utasítás SET clause-ainak adatai
	 *
	 * @var array
	 */
	private $setData = array();
	
	/**
	 * @return InsertBuilder
	 */
	public static function create() {
		return new self();
	}
	
	/**
	 * Beállítja hogy melyik táblába szúrunk be adatot.
	 * 
	 * @param string $table
	 * @return InsertBuilder
	 */
	public function into($table) {
		$this->table = $table;
		return $this;
	}
	
	/**
	 * Egy mező értékét állítja be az utasításban.
	 *
	 * @param string $where A beállítandó mező
	 * @param mixed $data A beállított érték
	 * @return UpdateBuilder $this
	 */
	public function set($field, $value) {
		$this->setFields []= $field;
		$this->setSql []= '?';
		$this->setData []= $value;
		return $this;
	}

	/**
	 * Egy mező értékét állítja be az utasításban.
	 *
	 * @param string $where A beállítandó mező
	 * @param string $value A beállítandó SQL kifejezés
	 * @return UpdateBuilder $this
	 */
	public function setSQL($field, $value) {
		$this->setFields []= $field;
		$this->setSql []= $value;
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
		$sql = 'INSERT INTO ' . $this->table;
		$sql .= ' ( ' . implode(', ', $this->setFields) . ' ) ';
		$sql .= ' VALUES ( ' . implode(', ',$this->setSql) . ' ) ';
		return $sql;
	}

	/**
	 * Returns the array of the parameters of this SQL
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return array
	 */
	public function convertToParamsArray(DbDialect $dialect = null) {
		return $this->setData;
	}
	
}
