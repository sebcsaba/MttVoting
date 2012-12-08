<?php

/**
 * A különböző adatbázisokon végrehajtandó SQL utasítások pontos formáját határozza meg.
 * 
 * @author sebcsaba
 */
interface DbDialect {
	
	/**
	 * Azt az SQL stringet adja vissza, amelyet tranzakció elején kell futtatnunk.
	 *
	 * @return string
	 */
	public function getSqlStartTransaction();
	
	/**
	 * Azt az SQL stringet adja vissza, amelyet tranzakció lezárásakor kell futtatnunk.
	 *
	 * @return string
	 */
	public function getSqlCommitTransaction();
	
	/**
	 * Azt az SQL stringet adja vissza, amelyet tranzakció hibája esetén kell futtatnunk.
	 *
	 * @return string
	 */
	public function getSqlRollbackTransaction();
	
	/**
	 * Azt az SQL stringet adja vissza, amelyet tábla kiürítése esetén kell futtatnunk. Ha a táblához szekvencia
	 * tartozik, azt is nullázza le az utasítás.
	 * 
	 * @param string $tableName
	 */
	public function getSqlTruncateTable($tableName);
	
	/**
	 * Azt az SQL stringet adja vissza, amelyet a kapcsolat inicializálásakor kell futtatnunk.
	 *
	 * @return string
	 */
	public function getConnectionInitializerQuery();
	
	/**
	 * Azt az SQL záradékot adja vissza, amely az eredményhalmazon limitet és offsetet állít be.
	 *
	 * @param integer $limit
	 * @param integer $offset
	 */
	public function getLimitClause($limit,$offset);
	
	/**
	 * Előkészíti a lekérdezést az adatbázis számára használhatóvá.
	 *
	 * @param Query $query Itt még tartalmazhat al-lekérdezéseket, valamint a paraméterek helyén kérdőjelnek kell
	 * 		szerepelnie.
	 * @return PreparedQuery Ez már csak olyan elemeket tartalmaz, amit az adatbázismotor ténylegesen elfogad.
	 */
	public function prepareQuery(Query $query);
	
	/**
	 * Átalakítja az eredeti (adatbázisból kapott) formátumú értéket a megadott típusjelzés szerint 
	 *
	 * @param string $type Értéke a primitív adattípusok lehetséges értékei: boolean, integer, float, string, enum, datetime
	 * @param mixed $value Az eredeti érték, amit az adatbáziscellából kiolvastunk
	 * @return mixed A megfelelő típusra átalakított érték (rendre: php bool, int, float, string, string, DateTime)
	 * @throws IncompatibleClassException Ha az érték nem megfelelő a megadott típusnak
	 * @throws DbException Ha ismeretlen típusra kellene konvertálni
	 * @see PrimitiveTypeConverter->convertPrimitive()
	 */
	public function convertPrimitive($type, $value);
	
}
