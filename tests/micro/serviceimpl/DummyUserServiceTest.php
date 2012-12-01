<?php

class DummyUserServiceTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function testAuthenticate() {
		$service = new DummyUserService();
		$user = $service->authenticate();
		$this->assertInstanceOf('User', $user);
		$this->assertTrue($user->getUserId() > 0);
		$this->assertNotEmpty($user->getLoginName());
	}
	
}
