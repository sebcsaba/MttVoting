<?php

/**
 * Egy adatbázis-motorral való kapcsolat biztosítására szolgáló osztály
 * 
 * @author sebcsaba
 */
abstract class DbEngine {
	
	/**
	 * Az adatbázis-kapcsolaton használt dialektus
	 *
	 * @var DbDialect
	 */
	private $dialect;
	
	/**
	 * A megkezdett (egymásba ágyazott) tranzakciók száma
	 *
	 * @var integer
	 */
	private $transactions = 0;
	
	/**
	 * Adatbázis-kapcsolat létrehozása
	 *
	 * @param DbConnectionParameters $params A kapcsolat paraméterei
	 * @param DbDialect $dialect A kapcsolat dialektusa
	 * @throws DbException ha nem megfelelő a kapcsolat típusa
	 */
	public function __construct(DbConnectionParameters $params, DbDialect $dialect) {
		$this->dialect = $dialect;
		$supported = $this->getSupportedProtocols();
		if (!in_array($params->getProtocol(),$supported)) {
			throw new DbException('Cannot connect because only '.implode(',',$supported).' protocol'.(count($supported)>1 ? 's are' : ' is').' supported.',$params->__toString());
		}
		$this->connect($params);
		$initializer = $this->dialect->getConnectionInitializerQuery();
		if (!empty($initializer)) {
			$this->execPrimitive($initializer);
		}
	}
	
	/**
	 * A támogatott protokollok listája
	 *
	 * @return array(strings)
	 */
	protected abstract function getSupportedProtocols();
	
	/**
	 * Felépíti a kapcsolatot
	 *
	 * @param DbConnectionParameters $params
	 * @throws DbException ha nem sikerült a kapcsolódás
	 */
	protected abstract function connect(DbConnectionParameters $params);
	
	/**
	 * Az adatbázis-kapcsolaton használt dialektust adja vissza
	 *
	 * @return DbDialect
	 */
	public function getDialect() {
		return $this->dialect;
	}
	
	/**
	 * A paraméterben megadott stringet előkészíti SQL-be illesztéshez
	 *
	 * @param string $string A bemeneti string
	 * @return string Az eredmény, amelyben escape-elve vannak a kritikus karakterek (aposztróf, stb)
	 */
	public abstract function escape($string);
	
	/**
	 * Lezárja az adatbázis-kapcsolatot
	 *
	 * @throws DbException
	 */
	public abstract function close();
	
	/**
	 * SQL parancs futtatása. Paraméterek nem használhatók, viszont több SQL parancs is állhat a stringben, pontosvesszővel elválasztva.
	 *
	 * @param string $sql_string A végrehajtandó SQL parancs(ok)
	 * @return numeric Az érintett sorok száma.
	 * @throws DbException Ha hibás parancsot akarunk futtatni.
	 */
	public abstract function execPrimitive($sql_string);
	
	/**
	 * SQL parancs futtatása a megadott paraméterekkel.
	 *
	 * @param NativeSQL $query A végrehajtandó SQL parancs, már előkészítve az engine-nek.
	 * @return numeric Az érintett sorok száma.
	 * @throws DbException ha nem sikerült a végrehajtani a parancsot
	 */
	public abstract function execNative(NativeSQL $query);
	
	/**
	 * A kapott, engine-függővé alakított SQL lekérdezés végrehajtása
	 *
	 * @param NativeSQL $query A végrehajtandó lekérdezés, már előkészítve az engine-nek.
	 * @return resource Az eredményt tartalmazó objektum. Engine-függő, általában valamilyen resource
	 * @throws DbException ha nem sikerült a végrehajtani a lekérdezést
	 */
	public abstract function queryNative(NativeSQL $query);
	
	/**
	 * Ellenőrzi a kapott eredményt, és ha használható akkor visszaadja
	 *
	 * @param resource $result Engine-függő eredményhalmaz, amit a queryNative() adott
	 * @param SQL $query Az eredményt adó lekérdezés, hibakészítéshez
	 * @return resource A kapott paraméter
	 * @throws DbException Ha hibás az eredmény
	 */
	public abstract function testResult($result, SQL $query);
	
	/**
	 * SQL lekérdezés első sorát adja vissza (mint asszociatív tömöt)
	 *
	 * @param resource $result Engine-függő eredményhalmaz, amit a queryNative() adott
	 * @return array Az eredmény első sora, vagy null.
	 */
	public abstract function fetchFirstRowOnly($result);
	
	/**
	 * SQL lekérdezés eredményét tartalmazó iterátort ad vissza (ami asszociatív tömböket tartalmaz)
	 *
	 * @param resource $result Engine-függő eredményhalmaz, amit a queryNative() adott
	 * @param boolean $autoClose Ha végigért az iterátoron, lezárhatja-e a DbResultSet-et.
	 * @return DbResultSet
	 * @throws DbException
	 */
	public abstract function getResultSet($result, $autoClose);
	
	/**
	 * Tranzakciót indít. Ha már korábban tranzakcióban voltunk, akkor növel egy számlálót, hogy számon tartsa, hogy
	 * hány "beágyazott" tranzakciót kezdtünk meg.
	 *
	 * @throws DbException
	 */
	public function startTransaction() {
		if ($this->transactions == 0) {
			$this->execPrimitive($this->dialect->getSqlStartTransaction());
			$this->transactions = 1;
		} else {
			$this->transactions++;
		}
	}
	
	/**
	 * Lezárja a tranzakciót. Ez csak akkor eredményez tényleges adatbázisbeli COMMIT-ot, ha az utolsó (legkülső szintű)
	 * tranzakció kerül lezárásra. Ellenkező esetben csak a tranzakciós számlálót csökkentjük.
	 *
	 * @throws DbException
	 */
	public function commit() {
		if ($this->transactions == 1) {
			$this->execPrimitive($this->dialect->getSqlCommitTransaction());
			$this->transactions = 0;
		} else {
			$this->transactions--;
		}
	}
	
	/**
	 * Visszavonja a tranzakciót. Ez az összes megkezdett tranzakciót hatástalanítja.
	 *
	 * @throws DbException
	 */
	public function rollback() {
		$this->execPrimitive($this->dialect->getSqlRollbackTransaction());
		$this->transactions = 0;
	}
	
	/**
	 * Lezárja a tranzakciót, ha a számláló nem nulla.
	 * Siker esetén új tranzakciót nyit, és a tranzakciós számlálót nem változtatja.
	 * 
	 * @throws DbException
	 */
	public function forceCommitAndStartTransactionAgain() {
		if ($this->transactions > 0) {
			$this->execPrimitive($this->dialect->getSqlCommitTransaction());
			$this->execPrimitive($this->dialect->getSqlStartTransaction());
		}
	}
	
	/**
	 * Igaz, ha van nyitott, használható tranzakció
	 * 
	 * @return boolean
	 */
	public function isTransactionOpened() {
		return $this->transactions>0;
	}

}
