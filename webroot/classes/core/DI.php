<?php

/**
 * Dependency Injector tool.
 * When creating instance (by the get() method), the following steps are used:
 *   1) If the required class is DI, return this object.
 *   2) If there's some special handling defined for the requested class or interface, use that.
 *   3) Lookup for the implementation classname for the given interface.
 *   4) If there's some special handling defined for the requested implementation class, use that.
 *   5) If the implementation class is marked non-singleton, or no previous instance created, create a new one.
 *   6) If the implementation class is marked non-singleton, store the created implementation instance.
 *   7) Return the instance
 *   
 * @author sebcsaba
 */
class DI {
	
	/**
	 * Instantiated objects
	 * @var array (string classname => object instance)
	 */
	private $instances;
	
	/**
	 * Implementation class - interface name mappings 
	 * @var array (string interface name => string classname)
	 */
	private $implementationClassNames;
	
	/**
	 * Non-singleton implementation class names
	 * @var array (string classname)
	 */
	private $nonsingletonClassNames;
	
	/**
	 * Special case handlers. Handler can be:
	 *   - instance of DISpecialHandler
	 *   - classname for az implementation class of DISpecialHandler
	 *   - function that has the same signature as DISpecialHandler.create()
	 * @var array (string classname => handler)
	 */
	private $specialHandlers;

	/**
	 * @param array $config Configuration parameters, containing the following items:
	 *     'impl': array for $implementationClassNames
	 *     'nonsingletons': array for $nonsingletonClassNames
	 *     'specials': array for $specialHandlers
	 */
	public function __construct(array $config) {
		$this->instances = array();
		$this->implementationClassNames = I($config,'impl',array());
		$this->nonsingletonClassNames = I($config,'nonsingletons',array());
		$this->specialHandlers = I($config,'specials',array());
	}
	
	public function setInstance($instance, $interfaceName = null) {
		if (is_null($interfaceName)) {
			$interfaceName = get_class($instance);
		}
		$this->instances[$interfaceName] = $instance;
	}
	
	public function get($interfaceOrClassName) {
		// 1) If the required class is DI, return this object.
		if ($interfaceOrClassName=='DI') {
			return $this;
		}
		// 2) If there's some special handling defined for the requested class or interface, use that.
		$instance = $this->checkSpecial($interfaceOrClassName);
		if (!is_null($instance)) {
			$this->setInstance($instance, $interfaceOrClassName);
			return $instance;
		}
		// 3) Lookup for the implementation classname for the given interface.
		$className = I($this->implementationClassNames, $interfaceOrClassName, $interfaceOrClassName);
		// 4) If there's some special handling defined for the requested implementation class, use that.
		$instance = $this->checkSpecial($className);
		if (!is_null($instance)) {
			$this->setInstance($instance, $interfaceOrClassName);
			return $instance;
		}
		$instance = I($this->instances, $className);
		// 5) If the implementation class is marked non-singleton, or no previous instance created, create a new one.
		if (is_null($instance) || array_key_exists($className, $this->nonsingletonClassNames)) {
			$instance = $this->instantiate($className);
		}
		// 6) If the implementation class is marked non-singleton, store the created implementation instance.
		if (!array_key_exists($className, $this->nonsingletonClassNames)) {
			$this->setInstance($instance, $interfaceOrClassName);
		}
		// 7) Return the instance
		return $instance;
	}
	
	private function checkSpecial($className) {
		$handler = I($this->specialHandlers, $className);
		if (is_null($handler)) {
			return null;
		}
		if (is_string($handler)) {
			$handler = $this->create($handler);
		}
		if (is_callable($handler)) {
			return call_user_func($handler, $this, $className);
		}
		if ($handler instanceof DISpecialHandler) {
			return $handler->create($this, $className);
		}
		throw new Exception('unknown special handler for '.$className);
	}
	
	private function instantiate($className) {
		$class = new ReflectionClass($className);
		$constructor = $class->getConstructor();
		$args = array();
		if (!is_null($constructor)) {
			foreach ($constructor->getParameters() as $param) {
				$args []= $this->get($param->getClass()->name);
			}
		}
		return $class->newInstanceArgs($args);
	}
	
}
