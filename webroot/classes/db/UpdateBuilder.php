<?php

class UpdateBuilder extends SQLBuilder {
	
	/**
	 * Az utasítás által érintett tábla
	 *
	 * @var string
	 */
	private $table;
	
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
	 * @return UpdateBuilder
	 */
	public static function create() {
		return new self();
	}
	
	/**
	 * Egy WHERE clause-t ad a lekérdezéshez. (Ezek konjunkciója lesz a teljes feltétel.)
	 *
	 * @param string $where A feltételkifejezés
	 * @param mixed... $data A táblakifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return UpdateBuilder $this
	 */
	public function where($where, $data=null) {
		return parent::where($where, $data);
	}
	
	/**
	 * Beállítja hogy melyik tábla kerül frissítére.
	 * 
	 * @param string $table
	 * @return UpdateBuilder
	 */
	public function update($table) {
		$this->table = $table;
		return $this;
	}
	
	/**
	 * Egy SET clause-t ad az utasításhez.
	 *
	 * @param string $where A beállítandó mező
	 * @param mixed $data A beállított érték
	 * @return UpdateBuilder $this
	 */
	public function set($field, $value) {
		$this->setSql []= $field.'=?';
		$this->setData []= $value;
		return $this;
	}

	/**
	 * Egy SET clause-t ad az utasításhez.
	 *
	 * @param string $where A beállítandó mező
	 * @param string $value A beállítandó SQL kifejezés
	 * @return UpdateBuilder $this
	 */
	public function setNative($field, $value) {
		$this->setSql []= $field.'='.$value;
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
		$sql = 'UPDATE ' . $this->table;
		if (!empty($this->setSql)) {
			$sql .= ' SET ' . implode(', ',$this->setSql);
		}
		$sql .= $this->getWhereClause();
		return $sql;
	}

	/**
	 * Returns the array of the parameters of this SQL
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return array
	 */
	public function convertToParamsArray(DbDialect $dialect = null) {
		return array_merge($this->setData, $this->getWhereData());
	}
	
}
