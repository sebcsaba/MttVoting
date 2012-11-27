<?php

/**
 * SQL lekérdezést felépítő osztály. Fluent interfacet használ, így lehet a következő módon kezelni:
 * 
 * $qb = QueryBuilder::create($dialect)->select('field0')->from('table1')->where('field2=?',42);
 *
 * Ha select clause-t nem adunk meg, akkor alapértelmezésként * kerül bele.
 * A paraméterek helyére ? kerüljön.
 * 
 */
class QueryBuilder {
	
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
	 * A lekérdezés ORDER BY clause-ai
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
	 * WITH RECURSIVE prefixes lekérdezés rekurzív view-jának neve
	 * 
	 * @var string
	 */
	private $withRecursiveView;
	
	/**
	 * WITH RECURSIVE prefixes lekérdezés rekurzív view-jának első (gyökér) lekérdezése
	 * 
	 * @var Query
	 */
	private $withRecursiveRoot;
	
	/**
	 * WITH RECURSIVE prefixes lekérdezés rekurzív view-jának második (iteratív) lekérdezése
	 * 
	 * @var Query
	 */
	private $withRecursiveIteration;
	
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
	 * Egy WHERE clause-t ad a lekérdezéshez. (Ezek konjunkciója lesz a teljes feltétel.)
	 *
	 * @param string $where A feltételkifejezés
	 * @param mixed... $data A táblakifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return QueryBuilder $this
	 */
	public function where($where, $data=null) {
		$this->whereSql []= $where;
		$this->whereData = array_merge($this->whereData, func_get_args_but_first());
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
	 * WITH RECURSIVE prefixes lekérdezés rekurzív view-jának adatai
	 * 
	 * @param string $recursiveView
	 * @param Query $root
	 * @param Query $iteration
	 * @return QueryBuilder $this
	 */
	public function withRecursive($recursiveView, Query $root, Query $iteration) {
		$this->withRecursiveView = $recursiveView;
		$this->withRecursiveRoot = $root;
		$this->withRecursiveIteration = $iteration;
		return $this;
	}
	
	/**
	 * Leszedi az eddig összerakott SELECT mezőket, lehet helyettük újat vagy COUNT()-ot használni inkább
	 * 
	 * @return QueryBuilder
	 */
	public function removeSelectFields() {
		$this->fieldSql = array();
		$this->fieldData = array();
		return $this;
	}

	/**
	 * Előállítja a szükséges SQL kifejezést.
	 *
	 * @return string
	 */
	public function getQueryString() {
		if (is_null($this->withRecursiveRoot)) {
			$sql = 'SELECT ';
		} else {
			$sql = sprintf('WITH RECURSIVE %s AS (? UNION ALL ?) SELECT', $this->withRecursiveView);
		}
		if (!empty($this->fieldSql)) {
			$sql .= implode(', ',$this->fieldSql);
		} else {
			$sql .= '*';
		}
		if (!empty($this->fromSql)) {
			$sql .= ' FROM ' . implode(' ',$this->fromSql);
		}
		if (!empty($this->whereSql)) {
			$sql .= ' WHERE (' . implode(') AND (',$this->whereSql) . ')';
		}
		if (!empty($this->groupSql)) {
			$sql .= ' GROUP BY ' . implode(', ',$this->groupSql);
		}
		if (!empty($this->orderSql)) {
			$sql .= ' ORDER BY ' . implode(', ',$this->orderSql);
		}
		if (!is_null($this->limit)) {
			$sql .= $this->getLimitClause();
		}
		return $sql;
	}

	/**
	 * Azt az SQL záradékot adja vissza, amely az eredményhalmazon limitet és offsetet állít be.
	 */
	private function getLimitClause() {
		$ret = sprintf(' LIMIT %d', $this->limit);
		if (!is_null($this->offset)){
			$ret .= sprintf(' OFFSET %d', $this->offset);
		}
		return $ret;
	}
	
	/**
	 * Előállítja az SQL-hez szükséges adatok tömbjét.
	 *
	 * @return array
	 */
	public function getQueryParams() {
		$withQueries = array();
		if (!is_null($this->withRecursiveRoot)) {
			$withQueries []= $this->withRecursiveRoot;
			$withQueries []= $this->withRecursiveIteration;
		}
		return array_merge($withQueries, $this->fieldData,$this->fromData,$this->whereData);
	}
	
	private function getQueryDebugString($indent) {
		$innerIndent = $indent."\t";
		if (is_null($this->withRecursiveRoot)) {
			$sql = $indent . "SELECT\n";
		} else {
			$sql = $indent . sprintf( "WITH RECURSIVE %s AS (? UNION ALL ?)\n%sSELECT\n", $this->withRecursiveView, $indent );
		}
		if (!empty($this->fieldSql)) {
			$sql .= $innerIndent . implode( ",\n".$innerIndent, $this->fieldSql ) . "\n";
		} else {
			$sql .= $innerIndent . "*\n";
		}
		if (!empty($this->fromSql)) {
			$sql .= $indent . "FROM\n" . $innerIndent . implode( "\n".$innerIndent, $this->fromSql ) . "\n";
		}
		if (!empty($this->whereSql)) {
			$sql .= $indent . "WHERE\n" . $innerIndent . "(" . implode( ")\n".$innerIndent."AND (", $this->whereSql ) . ")\n";
		}
		if (!empty($this->groupSql)) {
			$sql .= $indent . "GROUP BY\n" . $innerIndent . implode( ",\n".$innerIndent, $this->groupSql ) . "\n";
		}
		if (!empty($this->orderSql)) {
			$sql .= $indent . "ORDER BY\n" . $innerIndent . implode( ",\n".$innerIndent, $this->orderSql ) . "\n";
		}
		if (!is_null($this->limit)) {
			$sql .= $indent . $this->getLimitClause() . "\n";
		}
		return $sql;
	}
	
}

?>