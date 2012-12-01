<?php

class Autoloader {
	
	/**
	 * A cache-elésre használt fájl
	 * 
	 * @var string
	 */
	private $cache;
	
	/**
	 * Az eddig ismert osztályok tömbje, benne:
	 * osztálynév -> fájlnév
	 *
	 * @var array
	 */
	private $classes;
	
	/**
	 * A feldolgozásra váró könyvtárak listája. Ha nem talál egy osztályt, ezekben fogja keresni.
	 *
	 * @var array
	 */
	private $directoryQueue;
	
	/**
	 * Létrehozza az autoloadert.
	 *
	 * @param string $cache A cache-elésre használható fájl
	 */
	public function __construct($cache) {
		$this->cache = $cache;
		$this->classes = $this->loadCache();
		$this->directoryQueue = array();
	}
	
	/**
	 * Egy további könyvtárat ad hozzá a keresési útvonalhoz
	 * 
	 * @param string $directory
	 */
	public function addDirectory($directory) {
		$this->directoryQueue []= $directory;
	}
	
	/**
	 * Betölti a cache-t a szerializált fájlból, és visszaadja.
	 *
	 * @return array A cache tartalma
	 */
	private function loadCache() {
		if (!is_readable($this->cache)) {
			return array();
		} else {
			return unserialize(file_get_contents($this->cache));
		}
	}

	/**
	 * Kiírja a cache-be az ismert osztálynév-fájl párok tömjét
	 */
	private function writeCache() {
		file_put_contents($this->cache, serialize($this->classes));
		@chmod($this->cache, 0664);
	}

	/**
	 * Megkeresi a megadott osztályt tartalmazó fájlt és betölti.
	 * 
	 * Ha már ismeri az osztályt ($classes-ban szerepel) akkor onnan tölti be.
	 * Különben amíg nem üres a queue, addig megy rajta végig, majd az adott könyvtárakban található fájlokon is.
	 * Ha alkönyvtárat talál, azt beteszi a queue-ba, ha fájlt talál azt beteszi az ismert osztályok közé.
	 * Ha megtalálta amit keresett, azt betölti.
	 * Végül, ha változás volt a $classes-ban, akkor kiírja a cache-be.
	 *
	 * @param string $className a keresett osztály neve
	 * @throws ClassNotFoundException ha nem talált ilyen osztályt.
	 */
	public function load($className) {
		if (array_key_exists($className,$this->classes)) {
			$file = $this->classes[$className];
			if (is_readable($file)) {
				require_once($file);
				return;
			}
		}
		$found = false;
		$modified = false;
		while (!$found && !empty($this->directoryQueue)) {
			$dir = array_shift($this->directoryQueue);
			foreach (scandir($dir) as $f) {
				$file = $dir.DIRECTORY_SEPARATOR.$f;
				if (is_dir($file)) {
					if ($f[0]!='.') { // ignore '.', '..' and '.svn'
						$this->directoryQueue []= $file;
					}
				} else if (is_readable($file)) {
					$name = basename($file,'.php');
					$this->classes[$name] = $file;
					$modified = true;
					if ($name==$className) {
						$found = true;
					}
				}
			}
		}
		if ($modified) $this->writeCache();
		if ($found) {
			require_once($this->classes[$className]);
			return;
		}
		throw new ClassNotFoundException($className);
	}
	
}

?>