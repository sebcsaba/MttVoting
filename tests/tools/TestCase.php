<?php

class TestCase extends PHPUnit_Framework_TestCase {
	
	/**
	 * @return PHPUnit_Framework_MockObject_MockObject 
	 */
	protected function buildMock($className, array $methods) {
		$allMethods = $this->getAllMethodNames($className);
		$mockBuilder = $this->getMockBuilder($className);
		$mockBuilder->setMethods($allMethods);
		$mock = $mockBuilder->getMock();
		foreach (array_diff($allMethods, $methods) as $method) {
			$mock->expects($this->never())->method($method);
		}
		return $mock;
	}
	
	protected function getAllMethodNames($className) {
		$class = new ReflectionClass($className);
		$methods = $class->getMethods();
		return array_map(function($m){
			return $m->getName();
		}, $methods);
	}
	
}
