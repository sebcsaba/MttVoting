<?php

/**
 * Represents a user of this application.
 * The user data comes from some external database, such as joomla db.
 */
class User {

	/**
	 * @var int
	 */
	private $userId;
	
	/**
	 * @var string
	 */
	private $loginName;
	
	public function __construct($userId, $loginName) {
		$this->userId = $userId;
		$this->loginName = $loginName;
	}
	
	public function getUserId() {
		return $this->userId;
	}
	
	public function getLoginName() {
		return $this->loginName;
	}
	
}
