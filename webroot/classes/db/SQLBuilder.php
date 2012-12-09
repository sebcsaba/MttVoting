<?php

abstract class SQLBuilder implements SQL {
	
	/**
	 * A lekérdezés WHERE clause-ai
	 *
	 * @var array
	 */
	private $whereSql = array();
	
	/**
	 * A lekérdezés WHERE clause-ainak adatai
	 *
	 * @var array
	 */
	private $whereData = array();
	
	/**
	 * Egy WHERE clause-t ad a lekérdezéshez. (Ezek konjunkciója lesz a teljes feltétel.)
	 *
	 * @param string $where A feltételkifejezés
	 * @param mixed... $data A táblakifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return SQLBuilder $this
	 */
	public function where($where, $data=null) {
		$this->whereSql []= $where;
		$this->whereData = array_merge($this->whereData, func_get_args_but_first());
		return $this;
	}

	/**
	 * Return the string format of the WHERE clause
	 * 
	 * @return string
	 */
	protected function getWhereClause() {
		if (!empty($this->whereSql)) {
			return ' WHERE (' . implode(') AND (',$this->whereSql) . ')';
		} else {
			return '';
		}
	}
	
	/**
	 * Returns the collected data for the WHERE clause
	 * 
	 * @return array
	 */
	protected function getWhereData() {
		return $this->whereData;
	}

}
