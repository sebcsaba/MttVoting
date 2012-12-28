<?php

/**
 * Dependency Injector tool.
 * When creating instance (by the get() method), the following steps are used:
 *   1) If the required class is DI, return this object.
 *   2) If the instance is available in the additional instances array, return that.
 *   3) If there's some special handling defined for the requested class or interface, use that.
 *   4) Lookup for the implementation classname for the given interface.
 *   5) If there's some special handling defined for the requested implementation class, use that.
 *   6) If the implementation class is marked non-singleton, or no previous instance created, create a new one. (Use the additional instances for recursion.)
 *   7) If the implementation class is marked non-singleton, store the created implementation instance.
 *   8) Return the instance
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
	
	/**
	 * Returns an instance for the given interface or class name.
	 * 
	 * @param string $interfaceOrClassName Name of the interface of class to instantiate or return the previously instantiated instance
	 * @param array $additionalInstances array(string classname => object instance) array (string classname => object instance)
	 */
	public function get($interfaceOrClassName, array $additionalInstances = array()) {
		// 1) If the required class is DI, return this object.
		if ($interfaceOrClassName=='DI') {
			return $this;
		}
		// 2) If the instance is available in the additional instances array, return that.
		if (array_key_exists($interfaceOrClassName, $additionalInstances)) {
			return $additionalInstances[$interfaceOrClassName];
		}
		// 3) If there's some special handling defined for the requested class or interface, use that.
		$instance = $this->checkSpecial($interfaceOrClassName);
		if (!is_null($instance)) {
			$this->setInstance($instance, $interfaceOrClassName);
			return $instance;
		}
		// 4) Lookup for the implementation classname for the given interface.
		$className = I($this->implementationClassNames, $interfaceOrClassName, $interfaceOrClassName);
		// 5) If there's some special handling defined for the requested implementation class, use that.
		$instance = $this->checkSpecial($className);
		if (!is_null($instance)) {
			$this->setInstance($instance, $interfaceOrClassName);
			return $instance;
		}
		$instance = I($this->instances, $className);
		// 6) If the implementation class is marked non-singleton, or no previous instance created, create a new one. (Use the additional instances for recursion.)
		if (is_null($instance) || array_key_exists($className, $this->nonsingletonClassNames)) {
			$instance = $this->instantiate($className, $additionalInstances);
		}
		// 7) If the implementation class is marked non-singleton, store the created implementation instance.
		if (!array_key_exists($className, $this->nonsingletonClassNames)) {
			$this->setInstance($instance, $interfaceOrClassName);
		}
		// 8) Return the instance
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
	
	private function instantiate($className, array $additionalInstances) {
		$class = new ReflectionClass($className);
		$constructor = $class->getConstructor();
		$args = array();
		if (!is_null($constructor)) {
			foreach ($constructor->getParameters() as $param) {
				$args []= $this->get($param->getClass()->name, $additionalInstances);
			}
		}
		return $class->newInstanceArgs($args);
	}
	
}
