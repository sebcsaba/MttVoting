<?php

class UpdateBuilder {
	
	/**
	 * Az utasítás típusa: 'update' vagy 'delete'
	 * 
	 * @var string('update','delete')
	 */
	private $mode;
	
	/**
	 * Az utasítás által érintett tábla
	 *
	 * @var string
	 */
	private $table;
	
	/**
	 * Az utasítás SET clause-ai
	 *
	 * @var array
	 */
	private $setSql = array();
	
	/**
	 * Az utasítás SET clause-ainak adatai
	 *
	 * @var array
	 */
	private $setData = array();
	
	/**
	 * Az utasítás WHERE clause-ai
	 *
	 * @var array
	 */
	private $whereSql = array();
	
	/**
	 * Az utasítás WHERE clause-ainak adatai
	 *
	 * @var array
	 */
	private $whereData = array();
	
	public function __construct() {}
	
	/**
	 * @return UpdateBuilder
	 */
	public static function create() {
		return new self();
	}
	
	/**
	 * Beállítja hogy melyik tábla kerül frissítére.
	 * 
	 * @param string $table
	 * @return UpdateBuilder
	 */
	public function update($table) {
		$this->mode = 'update';
		$this->table = $table;
		return $this;
	}
	
	/**
	 * Beállítja hogy melyik táblából fogunk törölni
	 * 
	 * @param string $table
	 * @return UpdateBuilder
	 */
	public function deleteFrom($table) {
		$this->mode = 'delete';
		$this->table = $table;
		return $this;
	}
	
	/**
	 * Egy SET clause-t ad az utasításhez.
	 *
	 * @param string $where A beállítandó mező
	 * @param mixed $data A beállított érték
	 * @return UpdateBuilder $this
	 */
	public function set($field, $value) {
		$this->setSql []= $field.'=?';
		$this->setData []= $value;
		return $this;
	}

	/**
	 * Egy SET clause-t ad az utasításhez.
	 *
	 * @param string $where A beállítandó mező
	 * @param string $value A beállítandó SQL kifejezés
	 * @return UpdateBuilder $this
	 */
	public function setNative($field, $value) {
		$this->setSql []= $field.'='.$value;
		return $this;
	}

	/**
	 * Egy WHERE clause-t ad az utasításhez. (Ezek konjunkciója lesz a teljes feltétel.)
	 *
	 * @param string $where A feltételkifejezés
	 * @param mixed... $data A táblakifejezések előállításához szükséges paraméterek, vararg paraméterként
	 * @return UpdateBuilder $this
	 */
	public function where($where, $data=null) {
		$this->whereSql []= $where;
		$this->whereData = array_merge($this->whereData, func_get_args_but_first());
		return $this;
	}

}
