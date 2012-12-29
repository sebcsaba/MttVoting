<?php

/**
 * Dependency Injector tool.
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
	
	/**
	 * Sets the given instance - just as the objects created by the DI itself. The object will be available
	 * by it's class name, and, if given, the interface name specified as the second argument.
	 * 
	 * @param object $instance
	 * @param string $interfaceName optional 
	 */
	public function setInstance($instance, $interfaceName = null) {
		$className = get_class($instance);
		$this->instances[$className] = $instance;
		if (!is_null($interfaceName)) {
			$this->instances[$interfaceName] = $instance;
		}
	}
	
	/**
	 * Returns an instance for the given interface or class name. The following steps are used for lookup:
	 *   1) If the required class is DI, return this object. If the DI is needed somewhere, this object with the
	 *         current configuration will be available there. This rule cannot be overridden by any special case.
	 *   2) Get the implementation classname. If the given interface name is not defined in the
	 *         implementationClassNames mapping, the classname will be equal to the interface name given as parameter.
	 *         This is used when not interface but instantiatable classname is given as a parameter.
	 *   3) If the instance is available in the additional instances array, return that. This feature can used when
	 *         special configuration object is required in some cases. When looking for the instance, search for the
	 *         interface name first, and then the classname.
	 *   4) If the class name is not marked as non-singleton, and an instance is available in the instances array,
	 *         return that. In PHP most DI-related implementation objects can be singleton, therefore we list only the
	 *         exceptions, the non-singletons. As above, when looking for the instance, search for the interface name
	 *         first, and then the classname.
	 *   5) If there's some special handling defined for the requested class or interface, use that for the next steps.
	 *         This step will be skipped, if the $useSpecialHandlers parameter is false. When the additional instances
	 *         gives some declarative override feature, this gives a programmatic way. As above, when looking for the
	 *         instance, search for the interface name first, and then the classname. The object returned by the given
	 *         handler will be used for the next steps.
	 *   6) If no instance found at the previous step, instantiate now. This is the only point when new object is
	 *         instantiated. If the implementation class needs parameters for the constructor, goes recursively to
	 *         step 1. Transfers the additional instances, but not the value of the $useSpecialHandlers parameter.
	 *   7) If the class name is not marked as non-singleton, store the created implementation instance. As above, when
	 *         looking for the instance, search for the interface name first, and then the classname. When storing, use
	 *         both the interface and the class name. This functionality is the same as setInstance().
	 *   8) Return the instance.
	 * 
	 * @param string $interface Name of the interface that the returned object must be instance of.
	 * @param array $additionalInstances array(classname=>instance) Additional instances to prevent creating new ones.
	 * @param boolean $useSpecialHandlers If true, use step 5.
	 * @return object
	 */
	public function get($interface, array $additionalInstances = array(), $useSpecialHandlers = true) {
		// 1) If the required class is DI, return this object.
		if ($interface=='DI') {
			return $this;
		}
		
		// 2) Get the implementation classname.
		$class = I($this->implementationClassNames, $interface, $interface);
		
		// 3) If the instance is available in the additional instances array, return that.
		$instance = $this->lookup($interface, $class, $additionalInstances);
		if (!is_null($instance)) { return $instance; }
		
		// 4) If the class name is not marked as non-singleton, and an instance is available in the instances array, return that.
		if (!$this->isNonsingleton($interface, $class)) {
			$instance = $this->lookup($interface, $class, $this->instances);
			if (!is_null($instance)) { return $instance; }
		}
		
		// 5) If there's some special handling defined for the requested class or interface, use that for the next steps.
		if ($useSpecialHandlers) {
			$handler = $this->lookup($interface, $class, $this->specialHandlers);
			if (!is_null($handler)) {
				$instance = $this->applyHandler($handler, $interface, $class);
			}
		}
		
		// 6) If no instance found at the previous step, instantiate now.
		if (is_null($instance)) {
			$instance = $this->instantiate($interface, $class, $additionalInstances);
		}
		
		// 7) If the class name is not marked as non-singleton, store the created implementation instance.
		if (!$this->isNonsingleton($interface, $class)) {
			$this->setInstance($instance, $interface);
		}
		
		// 8) Return the instance.
		return $instance;
	}
	
	/**
	 * Instantiates the given class. If the forst parameter differs from the second, an isntanceof check will be
	 * performed for that.
	 * 
	 * @param string $interfaceName The name what the instantiation was requested for.
	 * @param string $className The classname to instantiate.
	 * @param array $additionalInstances
	 */
	private function instantiate($interfaceName, $className, array $additionalInstances) {
		$class = new ReflectionClass($className);
		$constructor = $class->getConstructor();
		$args = array();
		if (!is_null($constructor)) {
			foreach ($constructor->getParameters() as $i=>$param) {
				if (!($param->getClass() instanceof ReflectionClass)) {
					throw new Exception('unable to instantiate '.$className.': parameter '.$i.' has no type-constraint');
				}
				$args []= $this->get($param->getClass()->name, $additionalInstances);
			}
		}
		$instance = $class->newInstanceArgs($args);
		if ($interfaceName!=$className) {
			if (!($instance instanceof $interfaceName)) {
				throw new Exception('instantiated object of class '.$className.' does not implement the interface '.$interfaceName);
			}
		}
		return $instance;
	}

	/**
	 * Looks for the given interface and class names in the given array as key. Follows the instruction from the get()
	 * method steps 3, 4, 5, and 7: the interface name is looked first, and, if not found, then the class name.
	 * 
	 * @param string $interface
	 * @param string $class
	 * @param array $objects
	 * @return mixed
	 */
	private function lookup($interface, $class, array& $objects) {
		if (array_key_exists($interface, $objects)) {
			return $objects[$interface];
		} else if (array_key_exists($class, $objects)) {
			return $objects[$class];
		} else {
			return null;
		}
	}

	/**
	 * Returns true, if the given interface of class name is marked at non-singleton, false otherwise.
	 * 
	 * @param string $interface
	 * @param string $class
	 * @return boolean
	 */
	private function isNonsingleton($interface, $class) {
		return in_array($class, $this->nonsingletonClassNames)
			|| in_array($interface, $this->nonsingletonClassNames);
	}
	
	/**
	 * Applies the given handler to create a new instance the given class. Handler can be:
	 *   - function that has the same signature as DISpecialHandler.create()
	 *   - classname for az implementation class of DISpecialHandler. This DI will be used to instantiate.
	 *   - instance of DISpecialHandler
	 * 
	 * @param mixed $handler
	 * @param string $interfaceName
	 * @param string $className
	 * @return object
	 * @throws Exception If the handler doesn't match the conditions above.
	 */
	private function applyHandler($handler, $interfaceName, $className) {
		if (is_callable($handler)) {
			return call_user_func($handler, $this, $interfaceName, $className);
		}
		if (is_string($handler)) {
			$handler = $this->create($handler);
		}
		if ($handler instanceof DISpecialHandler) {
			return $handler->create($this, $interfaceName, $className);
		}
		throw new Exception('unknown special handler '.get_class($handler).' for '.$interfaceName.'/'.$className);
	}
	
}
