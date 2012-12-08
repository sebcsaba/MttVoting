<?php

class MySqlDbDialect implements DbDialect {

	/**
	 * Azt az SQL stringet adja vissza, amelyet tranzakció elején kell futtatnunk.
	 *
	 * @return string
	 */
	public function getSqlStartTransaction() {
		return 'START TRANSACTION';
	}
	
	/**
	 * Azt az SQL stringet adja vissza, amelyet tranzakció lezárásakor kell futtatnunk.
	 *
	 * @return string
	 */
	public function getSqlCommitTransaction() {
		return 'COMMIT';
	}
	
	/**
	 * Azt az SQL stringet adja vissza, amelyet tranzakció hibája esetén kel futtatnunk.
	 *
	 * @return string
	 */
	public function getSqlRollbackTransaction() {
		return 'ROLLBACK';
	}
	
	/**
	 * Azokat az SQL stringeket adja vissza, amelyet a kapcsolat inicializálásakor kell futtatnunk.
	 *
	 * @return array of strings
	 */
	public function getConnectionInitializerQueries() {
		return array(
			'SET NAMES utf8',
			'SET AUTOCOMMIT=0',
		);
	}
	
	/**
	 * Azt az SQL záradékot adja vissza, amely az eredményhalmazon limitet és offsetet állít be.
	 *
	 * @param integer $limit
	 * @param integer $offset
	 */
	public function getLimitClause($limit,$offset) {
		if (is_null($offset)){
			return sprintf(' LIMIT %d ', $limit);
		} else {
			return sprintf(' LIMIT %d,%d ', $offset, $limit);
		}
	}
	
	/**
	 * Előkészíti a lekérdezést az adatbázis számára használhatóvá.
	 * - A paraméterek között talált belső SQL-eket beilleszti a lekérdezésbe
	 * - A paraméterek között a boolean típust 1/0 számmá alakítja
	 * - Kicseréli az SQL-ben a kérődjeleket a megadott paraméterre
	 * 
	 * @param SQL $query Itt még tartalmazhat al-lekérdezéseket, valamint a paraméterek helyén kérdőjelnek kell szerepelnie.
	 * @return NativeSQL Ez már csak olyan elemeket tartalmaz, amit az adatbázismotor elfogad.
	 */
	public function prepareQuery(SQL $query) {
		$srcString = $query->convertToString($this);
		$srcParams = array_values($query->convertToParamsArray($this));
		$resultString = '';
		
		$srcParamIndex = 0;
		for ($i=0; $i<strlen($srcString); ++$i) {
			if ($srcString[$i]=='?') {
				$param = $srcParams[$srcParamIndex];
				++$srcParamIndex;
				if ($param instanceof SQL) {
					$preparedInnerQuery = $this->prepareQuery($param);
					$resultString .= '('.$preparedInnerQuery->convertToString($this).')';
				} else {
					$resultString .= $this->preparePrimitiveParam($param);
				}
			} else {
				$resultString .= $srcString[$i];
			}
		}
		return new NativeSQL($resultString,array());
	}
	
	/**
	 * Primitív értékeket készíti elő a MySQL adatbázis számára.
	 * - A paraméterek között a boolean típust 1/0 számmá alakítja
	 * - Dátumot stringgé alakítja
	 * 
	 * @param mixed $param
	 * @throws DbException Ha tömböt kellene kellene konvertálni
	 * @return mixed
	 */
	private function preparePrimitiveParam($param) {
		if (is_null($param)) {
			return 'null';
		} else if (is_bool($param)) {
			return ($param ? 1 : 0);
		} else if ($param instanceof DateTime) {
			return "'".$param->format('Y-m-d H:i:s')."'";
		} else if (is_string($param)) {
			return "'".mysql_real_escape_string($param)."'";
		} else if (is_int($param) || is_float($param)) {
			return $param;
		} else {
			throw new DbException('cannot convert php variable to mysql primitive');
		}
	}

	/**
	 * Átalakítja az eredeti (adatbázisból kapott) formátumú értéket a megadott típusjelzés szerint 
	 *
	 * @param string $type Értéke a primitív adattípusok lehetséges értékei: boolean, integer, float, string, enum, datetime
	 * @param mixed $value Az eredeti érték, amit az adatbáziscellából kiolvastunk
	 * @return mixed A megfelelő típusra átalakított érték (rendre: php bool, int, float, string, string, DateTime)
	 * @throws DbException Ha az érték nem megfelelő a megadott típusnak
	 * @throws DbException Ha ismeretlen típusra kellene konvertálni
	 */
		public function convertPrimitive($type, $value) {
		if (is_null($value)){
			return $value;
		}
		switch ($type){
			case 'boolean':
				if ($value===1 || $value===true) return true;
				if ($value===0 || $value===false) return false;
				throw new DbException('cannot convert mysql boolean value ['.$value.'] for php');
			case 'integer':
				return intval($value);
			case 'float':
				return floatval($value);
			case 'datetime':
				return DateTime::createFromFormat('Y-m-d H:i:s',$value);
			case 'string':
			case 'enum':
				return strval($value);
			default:
				throw new DbException('unknown field type: '.$type);
		}
	}
	
}
