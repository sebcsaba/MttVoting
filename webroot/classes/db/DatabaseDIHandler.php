<?php

class DatabaseDIHandler implements DISpecialHandler {
	
	private $configFieldName;
	
	public function __construct($configFieldName = 'db') {
		$this->configFieldName = $configFieldName;
	}
	
	public function create(DI $di, $interfaceName, $className) {
		$config = $di->get('Config')->get($this->configFieldName);
		$params = DbConnectionParameters::createFromArray($config);
		$engine = $di->get('DbEngine', array('DbConnectionParameters'=>$params));
		return $di->get($className, array('DbEngine'=>$engine), false);
	}
	
}
