<?php

/**
 * SQL lekérdezést felépítő osztály. Fluent interfacet használ, így lehet a következő módon kezelni:
 * 
 * $qb = QueryBuilder::create()->select('field0')->from('table1')->where('field2=?',42);
 *
 * Ha select clause-t nem adunk meg, akkor alapértelmezésként * kerül bele.
 * A paraméterek helyére ? kerüljön.
 * 
 */
class QueryBuilder extends SQLBuilder {
	
	/**
	 * A lekérdezés SELECT clause-ai
	 *
	 * @var array
	 */
	private $fieldSql = array();
	
	/**
	 * A lekérdezés SELECT clause-ainak adatai
	 *
	 * @var array
	 */
	private $fieldData = array();
	
	/**
	 * A lekérdezés FROM clause-ai
	 *
	 * @var array
	 */
	private $fromSql = array();
	
	/**
	 * A lekérdezés FROM clause-ainak adatai
	 *
	 * @var array
	 */
	private $fromData = array();
	
	/**
	 * A lekérdezés GROUP BY clause-ai
	 *
	 * @var array
	 */
	private $groupSql = array();

	/**
	 * A lekérdezés ORDER BY clause-ai
	 *
	 * @var array
	 */
	private $orderSql = array();
	
	/**
	 * A lekérdezés limitje.
	 *
	 * @var integer
	 */
	private $limit;
	
	/**
	 * A lekérdezés offsetje.
	 *
	 * @var integer
	 */
	private $offset;
	
	/**
	 * @return QueryBuilder
	 */
	public static function create() {
		return new self();
	}
	
	/**
	 * Egy WHERE clause-t ad a lekérdezéshez. (Ezek konjunkciója lesz a teljes feltétel.)
	 *
	 * @param string $where A feltételkifejezés
	 * @param mixed... $data A táblakifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return QueryBuilder $this
	 */
	public function where($where, $data=null) {
		return parent::where($where, $data);
	}
	
	/**
	 * Egy SELECT clause-t ad a lekérdezéshez.
	 *
	 * @param string $field A mező neve, vagy mezőkifejezés
	 * @param mixed... $data A mezőkifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return QueryBuilder $this
	 */
	public function select($field, $data=null) {
		$this->fieldSql []= $field;
		$this->fieldData = array_merge($this->fieldData, func_get_args_but_first());
		return $this;
	}
	
	/**
	 * Egy SELECT COUNT(...) clause-t ad a lekérdezéshez.
	 *
	 * @param string $field A mező neve, vagy mezőkifejezés. Ha null, akkor COUNT(*) lesz hozzáadva.
	 * @param mixed... $data A mezőkifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return QueryBuilder $this
	 */
	public function count($field=null, $data=null) {
		if (is_null($field)) {
			$field = '*';
		}
		$this->fieldSql []= 'COUNT('.$field.')';
		$this->fieldData = array_merge($this->fieldData, func_get_args_but_first());
		return $this;
	}

	/**
	 * Egy FROM clause-t ad a lekérdezéshez.
	 *
	 * @param string $from A tábla neve, vagy táblakifejezés
	 * @param mixed... $data A táblakifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return QueryBuilder $this
	 */
	public function from($from, $data=null) {
		$this->fromSql []= $from;
		$this->fromData = array_merge($this->fromData, func_get_args_but_first());
		return $this;
	}

	/**
	 * Egy JOIN clause-t ad a lekérdezéshez. Ez a from() függvény kiterjesztése a könnyebb szintaxis kedvéért.
	 *
	 * @param string $from A tábla neve, vagy táblakifejezés
	 * @param mixed... $data A táblakifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return QueryBuilder $this
	 */
	public function join($join, $data=null) {
		$this->fromSql []= 'JOIN '.$join;
		$this->fromData = array_merge($this->fromData, func_get_args_but_first());
		return $this;
	}
	
	/**
	 * Egy LEFT JOIN clause-t ad a lekérdezéshez. Ez a from() függvény kiterjesztése a könnyebb szintaxis kedvéért.
	 *
	 * @param string $from A tábla neve, vagy táblakifejezés
	 * @param mixed... $data A táblakifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return QueryBuilder $this
	 */
	public function leftJoin($join, $data=null) {
		$this->fromSql []= 'LEFT JOIN '.$join;
		$this->fromData = array_merge($this->fromData, func_get_args_but_first());
		return $this;
	}

	/**
	 * Egy GROUP BY clause-t ad a lekérdezéshez.
	 *
	 * @param string $groupBy A mező neve
	 * @return QueryBuilder $this
	 */
	public function groupBy($groupBy) {
		$this->groupSql []= $groupBy;
		return $this;
	}

	/**
	 * Egy ORDER BY clause-t ad a lekérdezéshez.
	 *
	 * @param string $orderBy A mező neve
	 * @return QueryBuilder $this
	 */
	public function orderBy($orderBy) {
		$this->orderSql []= $orderBy;
		return $this;
	}
	
	/**
	 * Egy ORDER BY ... ASC clause-t ad a lekérdezéshez.
	 *
	 * @param string $orderBy A mező neve
	 * @return QueryBuilder $this
	 */
	public function orderByAsc($orderBy) {
		$this->orderSql []= $orderBy.' ASC';
		return $this;
	}
	
	/**
	 * Egy ORDER BY ... DESC clause-t ad a lekérdezéshez.
	 *
	 * @param string $orderBy A mező neve
	 * @return QueryBuilder $this
	 */
	public function orderByDesc($orderBy) {
		$this->orderSql []= $orderBy.' DESC';
		return $this;
	}
	
	/**
	 * A limitet és opcionálisan az offsetet adja meg
	 *
	 * @param integer $limit
	 * @param integer $offset
	 * @return QueryBuilder $this
	 */
	public function limit($limit,$offset=null) {
		$this->limit = $limit;
		$this->offset = $offset;
		return $this;
	}
	
	/**
	 * Azt az SQL záradékot adja vissza, amely az eredményhalmazon limitet és offsetet állít be.
	 * Ez nem engine-függő, és nem is szabványos!!! Ez csak akkor kerülhet végrehajtásra,
	 * ha nem áll DbDialect rendelkezése, például hibajelzésnél.
	 */
	private function getDefaultLimitClause() {
		$ret = sprintf(' LIMIT %d ', $this->limit);
		if (!is_null($this->offset)){
			$ret .= sprintf(' OFFSET %d ', $this->offset);
		}
		return $ret;
	}
	
	/**
	 * Returns the string representation of this SQL
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return string
	 */
	public function convertToString(DbDialect $dialect = null) {
		$sql = 'SELECT ';
		if (!empty($this->fieldSql)) {
			$sql .= implode(', ',$this->fieldSql);
		} else {
			$sql .= '*';
		}
		if (!empty($this->fromSql)) {
			$sql .= ' FROM ' . implode(' ',$this->fromSql);
		}
		$sql .= $this->getWhereClause();
		if (!empty($this->groupSql)) {
			$sql .= ' GROUP BY ' . implode(', ',$this->groupSql);
		}
		if (!empty($this->orderSql)) {
			$sql .= ' ORDER BY ' . implode(', ',$this->orderSql);
		}
		if (!is_null($this->limit)) {
			if (is_null($dialect)) {
				$sql .= $this->getLimitClause();
			} else {
				$sql .= $dialect->getLimitClause($this->limit, $this->offset);
			}
		}
		return $sql;
	}

	/**
	 * Returns the array of the parameters of this SQL
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 		dialect-dependent is needed.
	 * @return array
	 */
	public function convertToParamsArray(DbDialect $dialect = null) {
		return array_merge($this->fieldData, $this->fromData, $this->getWhereData());
	}
	
}
