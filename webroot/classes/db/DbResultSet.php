<?php

/**
 * Adatábázis lekérdezés eredményhalmaza mint iterálható objektum. Az iterációs lépésekben az eredmény adatsorait
 * asszociatív tömbként kell visszaadni.
 *
 * @author sebcsaba
 */
interface DbResultSet extends Iterator {

	/**
	 * Lezárja az objektumot, felszabadítja a benne használt erőforrást.
	 *
	 */
	public function close();
	
}
