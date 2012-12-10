<?php

/**
 * Egy SQL adatbázis-kapcsolatot valósít meg, engine-függetlenül.
 * Ha paraméterezett SQL-t használunk, akkor a paraméterek helyére ? kerüljön.
 * 
 * @author sebcsaba
 */
class LowDatabase {
	
	/**
	 * A kapcsolathoz használt adatbázis-motor
	 *
	 * @var DbEngine
	 */
	protected $engine;
	
	/**
	 * Adatbázis-kapcsolat létrehozása
	 *
	 * @param DbEngine $engine A kapcsolathoz használt adatbázis-motor
	 */
	public function __construct(DbEngine $engine) {
		$this->engine = $engine;
	}
	
	/**
	 * Lezárja az adatbázis-kapcsolatot
	 */
	public function close() {
		$this->engine->close();
	}
	
	/**
	 * Tranzakciót indít. Ha már korábban tranzakcióban voltunk, akkor növel egy számlálót,
	 * hogy számon tartsa, hogy hány "beágyazott" tranzakciót kezdtünk meg.
	 * 
	 * @throws DbException Ha nem sikerült a parancsot végrehajtani.
	 */
	public function startTransaction() {
		$this->engine->startTransaction();
	}
	
	/**
	 * Lezárja a tranzakciót. Ez csak akkor eredményez tényleges adatbázisbeli COMMIT-ot,
	 * ha az utolsó (legkülső szintű) tranzakció kerül lezárásra. Ellenkező esetben csak a tranzakciós számlálót csökkentjük.
	 * 
	 * @throws DbException Ha nem sikerült a parancsot végrehajtani.
	 */
	public function commit() {
		$this->engine->commit();
	}
	
	/**
	 * Visszavonja a tranzakciót. Ez az összes megkezdett tranzakciót hatástalanítja.
	 * 
	 * @throws DbException Ha nem sikerült a parancsot végrehajtani.
	 */
	public function rollback() {
		$this->engine->rollback();
	}
	
	/**
	 * Lezárja a tranzakciót, ha a számláló nem nulla.
	 * Siker esetén új tranzakciót nyit, és a tranzakciós számlálót nem változtatja.
	 * 
	 * @throws DbException Ha nem sikerült a parancsot végrehajtani.
	 */
	public function forceCommitAndStartTransactionAgain() {
		$this->engine->forceCommitAndStartTransactionAgain();
	}

	/**
	 * A paraméterben megadott stringet előkészíti SQL-be illesztéshez
	 *
	 * @param string $string A bemeneti string
	 * @return string Az eredmény, amelyben escape-elve vannak a kritikus karakterek (aposztróf, stb)
	 */
	public function escape($string) {
		return $this->engine->escape($string);
	}
	
	/**
	 * SQL parancs futtatása a megadott paraméterekkel.
	 *
	 * @param SQL $query A végrehajtandó SQL parancs (? jelöléssel a paraméterek helye)
	 * @return integer Az érintett sorok száma, vagy a beszúrt sor azonosítója, ha a megadott paraméter InsertBuilder
	 * @throws DbException Ha nem sikerült a parancsot végrehajtani.
	 */
	public function exec(SQL $query) {
		$preparedQuery = $this->engine->getDialect()->prepareQuery($query);
		return $this->engine->execNative($preparedQuery, $query instanceof InsertBuilder);
	}
	
	/**
	 * SQL lekérdezés futtatása a megadott paraméterekkel.
	 *
	 * @param SQL $query A lekérdezés adatai
	 * @return resource Az eredményhalmaz. Engine-től függ hogy milyen válaszobjektumot kapunk
	 * @throws DbException Ha nem sikerült a lekérdezést végrehajtani.
	 */
	protected function queryNative(SQL $query) {
		$preparedQuery = $this->engine->getDialect()->prepareQuery($query);
		$result = $this->engine->queryNative($preparedQuery);
		return $this->engine->testResult($result,$preparedQuery);
	}
	
	/**
	 * A paraméterben kapott SQL lekérdezés futtatása
	 *
	 * @param SQL $query A lekérdezés adatai
	 * @param boolean $autoClose Ha végigért az iterátoron, lezárhatja-e a DbResultSet-et.
	 * @return DbResultSet Az eredményhalmazt csomagoló objektum, amellyel a foreach szerkezetet használhatjuk.
	 * @throws DbException Ha nem sikerült a lekérdezést végrehajtani.
	 */
	public function query(SQL $query, $autoClose=true) {
		return $this->engine->getResultSet($this->queryNative($query), $autoClose);
	}

}
