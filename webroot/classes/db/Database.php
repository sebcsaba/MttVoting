<?php

/**
 * Egy SQL adatbázis-kapcsolatot valósít meg, és magasabb szintű szolgáltatásokat tesz köré.
 * Ha paraméterezett SQL-t használunk, akkor a paraméterek helyére ? kerüljön.
 * 
 * @author sebcsaba
 */
class Database extends LowDatabase {
	
	/**
	 * SQL lekérdezés első sorát adja vissza (mint asszociatív tömöt)
	 *
	 * @param SQL $query A végrehajtandó SQL parancs
	 * @param boolean $nullOnEmpty Ha igaz, akkor üres eredménynél null-t ad vissza, különben kivételt dob. 
	 * @return array Az eredmény első sora, vagy null.
	 * @throws DbException Ha nem sikerült a lekérdezést végrehajtani.
	 * @throws DbException Ha üres eredményt kaptunk, és $nullOnEmpty hamis.
	 */
	public function queryFirstRow(SQL $query, $nullOnEmpty=false) {
		$result = $this->queryNative($query);
		$row = $this->engine->fetchFirstRowOnly($result);
		if (!is_null($row)) {
			return $row;
		} else if ($nullOnEmpty) {
			return null;
		} else {
			throw new DbException('the query return empty resultset', $query);
		}
	}
	
	/**
	 * SQL lekérdezés első sorának egy celláját adja vissza.
	 *
	 * @param SQL $query A végrehajtandó SQL parancs
	 * @param boolean $nullOnEmpty Ha igaz, akkor üres eredménynél null-t ad vissza, különben kivételt dob.
	 * @param string $fieldName A visszaadandó mező neve. Ha null, akkor sorrend szerint az elsőt adja.
	 * @return mixed Az eredmény első sorának adott cellája, vagy null.
	 * @throws DbException Ha nem sikerült a lekérdezést végrehajtani.
	 * @throws NoSuchDataException Ha üres eredményt kaptunk, és $nullOnEmpty hamis.
	 */
	public function queryCell(SQL $query, $fieldName=null, $nullOnEmpty=false) {
		$row = $this->queryFirstRow($query, $nullOnEmpty);
		if ($row===null) {
			return null;
		}
		return $this->getRowField($row,$fieldName);
	}
	
	/**
	 * SQL lekérdezés adott oszlopát adja vissza egy tömbként
	 *
	 * @param SQL $query A végrehajtandó SQL parancs
	 * @param string $fieldName A visszaadandó mező neve. Ha null, akkor sorrend szerint az elsőt adja.
	 * @return array Az eredmény celláit tartalmazó tömb
	 * @throws DbException Ha nem sikerült a lekérdezést végrehajtani.
	 */
	public function queryColumn(SQL $query, $fieldName = null) {
		$result = array();
		foreach ($this->query($query) as $row) {
			$result []= $this->getRowField($row,$fieldName);
		}
		return $result;
	}
	
	/**
	 * SQL lekérdezés eredményét asszociatív tömbként adja vissza 
	 *
	 * @param SQL $query A végrehajtandó SQL parancs
	 * @param string $keyFieldName A kulcsokat tartalmazó mező neve. Ha null, akkor sorrend szerint az elsőt adja.
	 * @param string $valueFieldName Az értékeket tartalmazó mező neve. Ha null, akkor sorrend szerint a másodikat adja.
	 * @return array Az eredmény asszociatív tömbként
	 * @throws DbException Ha nem sikerült a lekérdezést végrehajtani.
	 */
	public function queryMapping(SQL $query, $keyFieldName = null, $valueFieldName = null) {
		$result = array();
		foreach ($this->query($query) as $row) {
			$key = $this->getRowField($row,$keyFieldName);
			$value = $this->getRowField($row,$valueFieldName);
			$result[$key] = $value;
		}
		return $result;
	}
	
	/**
	 * SQL lekérdezés eredményét asszociatív tömbök tömbjeként adja vissza 
	 *
	 * @param SQL $query A végrehajtandó SQL parancs
	 * @return array Az eredmény tömb, amely minden eleme asszociatív tömb
	 * @throws DbException Ha nem sikerült a lekérdezést végrehajtani.
	 */
	public function queryAssocTable(SQL $query) {
		$result = array();
		foreach ($this->query($query) as $row) {
			$result []= $row;
		}
		return $result;
	}
	
	/**
	 * A kapott rekord megfelelő indexű elemét adja meg
	 *
	 * @param array $row Az adatbázis-rekord
	 * @param string $fieldName A mező neve, vagy null
	 * @return mixed A rekord adott mezőjű eleme, vagy a sorrend szerinti első, ha $fieldName null.
	 */
	private function getRowField(array &$row, $fieldName = null) {
		if (is_null($fieldName)) {
			return array_shift($row);
		} else {
			return $row[$fieldName];
		}
	}
	
}
