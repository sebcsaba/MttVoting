<?php

/**
 * Ez a kivétel akkot lép fel, ha egy adott nevű osztályt nem sikerült betölteni.
 * 
 * @author sebcsaba
 */
class ClassNotFoundException extends Exception {
	
	private $className;
	
	public function __construct($className){
		parent::__construct(sprintf('Unable to load class: %s',$className));
		$this->className = $className;
	}
	
	public function getClassName(){
		return $this->className;
	}

}

?>