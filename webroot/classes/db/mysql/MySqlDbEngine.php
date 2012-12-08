<?php

class MySqlDbEngine extends DbEngine {

	/**
	 * Adatbáziskapcsolat erőforrás
	 *
	 * @var resource
	 */
	private $conn;

	/**
	 * A támogatott protokollok listája
	 *
	 * @return array
	 */
	protected function getSupportedProtocols() {
		return array('mysql');
	}
	
	/**
	 * Felépíti a kapcsolatot
	 *
	 * @param DbConnectionParameters $params
	 * @throws DbException ha nem sikerült a kapcsolódás
	 */
	protected function connect(DbConnectionParameters $params) {
		$host = sprintf("%s:%d", $params->getHost(), coalesce($params->getPort(), 3306));
		$this->conn = mysql_connect($host, $params->getUsername(), $params->getPassword());
		mysql_select_db($params->getDatabase());
	}
	
	/**
	 * A paraméterben megadott stringet előkészíti SQL-be illesztéshez
	 *
	 * @param string $string A bemeneti string
	 * @return string Az eredmény, amelyben escape-elve vannak a kritikus karakterek (aposztróf, stb)
	 */
	public function escape($string) {
		return mysql_real_escape_string($string, $this->conn);
	}
	
	/**
	 * Lezárja az adatbázis-kapcsolatot
	 *
	 */
	public function close() {
		if (!is_null($this->conn)) {
			mysql_close($this->conn);
			$this->conn = null;
		}
	}
	
	/**
	 * SQL parancs futtatása. Paraméterek nem használhatók, viszont több SQL parancs is állhat a stringben, pontosvesszővel elválasztva.
	 *
	 * @param string $sql_string A végrehajtandó SQL parancs(ok)
	 * @return numeric Az érintett sorok száma.
	 * @throws DbException Ha hibás parancsot akarunk futtatni.
	 */
	public function execPrimitive($sql_string) {
		$result = @mysql_query($sql_string, $this->conn);
		if ($result) {
			if ($result===true) {
				return 0;
			}
			$affected_rows = mysql_affected_rows($result);
			mysql_free_result($result);
			return $affected_rows;
		} else{
			throw new DbException(mysql_error($this->conn).' when executing primitive '.$sql_string);
		}
	}
	
	/**
	 * SQL parancs futtatása a megadott paraméterekkel.
	 *
	 * @param NativeSQL $query A végrehajtandó SQL parancs, már előkészítve az engine-nek.
	 * @return numeric Az érintett sorok száma.
	 */
	public function execNative(NativeSQL $query) {
		$result = @mysql_query($query->convertToString($this->getDialect()), $this->conn);
		if ($result) {
			if ($result===true) {
				return 0;
			}
			$affected_rows = mysql_affected_rows($result);
			mysql_free_result($result);
			return $affected_rows;
		} else {
			throw new DbException(mysql_error($this->conn), $query);
		}
	}
	
	/**
	 * A kapott, engine-függővé alakított SQL lekérdezés végrehajtása
	 *
	 * @param NativeSQL $query A végrehajtandó lekérdezés, már előkészítve az engine-nek.
	 * @return resource Az eredményt tartalmazó objektum. Engine-függő, általában valamilyen resource
	 * @throws DbException ha nem sikerült a végrehajtani a lekérdezést
	 */
	public function queryNative(NativeSQL $query) {
		return @mysql_query($query->convertToString($this->getDialect()), $this->conn);
	}
	
	/**
	 * Ellenőrzi a kapott eredményt, és ha használható akkor visszaadja
	 *
	 * @param resource $result Engine-függő eredményhalmaz, amit a queryNative() adott
	 * @param SQL $query Az eredményt adó lekérdezés, hibakészítéshez
	 * @return resource A kapott paraméter
	 * @throws DbException Ha hibás az eredmény
	 */
	public function testResult($result, SQL $query) {
		if ($result) {
			return $result;
		} else {
			throw new DbException(mysql_error($this->conn), $query);
		}
	}
	
	/**
	 * SQL lekérdezés első sorát adja vissza (mint asszociatív tömöt)
	 *
	 * @param resource $result Az eredményhalmaz
	 * @return array Az eredmény első sora, vagy null.
	 */
	public function fetchFirstRowOnly($result) {
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		return ($row===false) ? null : $row;
	}
	
	/**
	 * SQL lekérdezés eredményét tartalmazó iterátort ad vissza (ami asszociatív tömböket tartalmaz)
	 *
	 * @param resource $result Engine-függő eredményhalmaz, amit a queryNative() adott
	 * @param boolean $autoClose Ha végigért az iterátoron, lezárhatja-e a DbResultSet-et.
	 * @return DbResultSet
	 * @throws DbException
	 */
	public function getResultSet($result, $autoClose) {
		return new MySqlDbResultSet($result, $autoClose);
	}
	
}
