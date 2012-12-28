<?php

class DI {
	
	private $implementationClasses;
	private $singletons;

	/**
	 * @param array $config Configuration parameters, containing the following items:
	 *     'impl': array(interfaceClassName => implementationClassName)
	 *     'singletons': array(? => implementationClassName)
	 */
	public function __construct(array $config) {
		$this->implementationClasses = I($config,'impl');
		$this->singletons = I($config,'singletons');
	}
	
	private function initSingletonArray($singletonClasses) {
		$result = array();
		foreach ($singletonClasses as $className) {
			$result[$className] = null;
		}
		return $result;
	}
	
	public function setSingleton($instance, $interfaceName = null) {
		if (is_null($interfaceName)) {
			$interfaceName = get_class($instance);
		}
		$this->singletons[$interfaceName] = $instance;
	}
	
	public function create($interfaceOrClassName) {
		if ($interfaceOrClassName=='DI') {
			return $this;
		}
		$className = I($this->implementationClasses, $interfaceOrClassName, $interfaceOrClassName);
		$singleton = I($this->singletons, $className);
		if (!is_null($singleton)) {
			return $singleton;
		}
		$instance = $this->instantiate($className);
		if (array_key_exists($className, $this->singletons)) {
			$this->singletons[$className] = $instance;
		}
		return $instance;
	}
	
	private function instantiate($className) {
		$class = new ReflectionClass($className);
		$constructor = $class->getConstructor();
		$args = array();
		if (!is_null($constructor)) {
			foreach ($constructor->getParameters() as $param) {
				$args []= $this->create($param->getClass()->name);
			}
		}
		return $class->newInstanceArgs($args);
	}
	
}
