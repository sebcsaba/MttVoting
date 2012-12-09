<?php

class MySqlDbResultSet implements DbResultSet {

	private $result;
	private $position;
	private $row_data;
	private $autoClose;
	 
	public function __construct($result, $autoClose) {
		$this->result = $result;
		$this->position = 0;
		$this->autoClose = $autoClose;
	}
	 
	public function current() {
		return $this->row_data;
	}
	 
	public function key() {
		return $this->position;
	}
	 
	public function next() {
		$this->position++;
		$this->row_data = mysql_fetch_assoc($this->result);
	}
	
	public function rewind() {
		$this->position = 0;
		if (mysql_num_rows($this->result)>0) {
			mysql_data_seek($this->result, 0);
			$this->row_data = mysql_fetch_assoc($this->result);
		} else {
			$this->row_data = false;
		}
	}
	
	public function valid() {
		$result = $this->row_data !== false;
		if (!$result && $this->autoClose) {
			$this->close();
		}
		return $result;
	}
	
	public function close() {
		if (!is_null($this->result)) {
			mysql_free_result($this->result);
			$this->result = null;
		}
	}

}
