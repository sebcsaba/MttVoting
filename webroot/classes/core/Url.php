<?php

/**
 * Paramétereket tartalmazó URL előállítására szolgáló osztály. Használata: metódusnévként kell megadni a paraméter
 * nevét. A metódus eredménye az alap objektum, így láncolható a dolog. Például:
 * 
 * print Url::create('ajax.php')->page('foo')->process('bar');
 * 
 * @author sebcsaba
 */
class Url {

	/**
	 * Az URL alapja, vagyis a paraméterek előtti teljes része
	 * 
	 * @var string
	 */
	private $base;
	
	/**
	 * Az URL paraméterei
	 * 
	 * @var array
	 */
	private $params;
	
	/**
	 * Létrehozás a bázis és a paraméterek megadásával. Ha a bázisban szerepel a ? karakter, akkor fel lesz parse-olva
	 * és a benne szereplő paraméterek is feldolgozásra kerülnek.
	 * 
	 * @param string $base
	 * @param array $params
	 */
	public function __construct($base=null, array $params = array()) {
		if (is_null($base)) {
			$base = $_SERVER['PHP_SELF'];
		} else if (strpos($base,'?')>0) {
			$base = self::parse($base,$params);
		}
		$this->base = $base;
		$this->params = $params;
	}
	
	/**
	 * A bázis felparse-olását végzi. A benne talált paramétereket a második argumentumként kapott tömbbe teszi, és a
	 * letisztított bázissal tér vissza.
	 * 
	 * @param string $base
	 * @param array $params
	 * @return string
	 */
	private static function parse($base, &$params) {
		$q = strpos($base,'?');
		$p = substr($base,$q+1);
		foreach (explode('&',$p) as $part) {
			if (strlen($part)>0) {
				@list($name,$value) = explode('=',$part);
				$params[urldecode($name)] = urldecode($value);
			}
		}
		$base = substr($base,0,$q);
		return $base;
	}

	/**
	 * Statikus létrehozó függvény, hogy könnyen tudjuk példányosítás után használni. Mindezt azért kell, mert a php-ban
	 * nem lehet new-val frissen példányosított objektumon kapásból metódust hívni, vagyis ez a kód szintaktikai hibát
	 * eredményezne: new Url()->foo(); Ez helyett használható az Url::create()->foo();
	 * 
	 * @param string $base
	 * @param array $params
	 */
	public static function create($base=null, array $params = array()) {
		return new Url($base,$params);
	}
	
	/**
	 * A nem definiált metódushívások paraméterbeállításként lesznek kezelve: a metódus nevéből jön a paraméter neve, és
	 * az első paraméterből az értéke.
	 * 
	 * @param string $name
	 * @param array $args
	 */
	public function __call($name,$args) {
		$value = array_key_exists(0,$args) ? $args[0] : null;
		return $this->setParam($name,$value);
	}
	
	/**
	 * Adott nevű és értékű paraméter beállítása
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name,$value) {
		$this->params[$name] = $value;
		return $this;
	}
	
	/**
	 * Adott nevű paraméter lekérdezése. Ha nincs, akkor null-t ad.
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function getParam($name){
		return I($this->params,$name);
	}

	/**
	 * Az URL-t stringgé alakítja. Így egy URL objektumot simán ki lehet írni a print művelettel, és megfelelően
	 * escape-elt URL-t kapunk a kimeneten.
	 * 
	 * (Megfelelően = urlencode meg lesz hívva minden paraméter nevén és értékén. Természetesen ha HTML-ben akarjuk
	 * használni, akkor még egy htmlspecialchars-t kell rajta hívnunk.)
	 * 
	 * @return string
	 */
	public function __toString() {
		$result = '';
		foreach ($this->params as $name=>$value) {
			$result .= '&'.urlencode($name).'='.urlencode($value);
		}
		return $this->base.'?'.substr($result,1);
	}

}
