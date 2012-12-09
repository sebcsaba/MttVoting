<?php

class Participant {

	/**
	 * @var int
	 */
	private $id;
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @var bool
	 */
	private $voted;
	
	public function __construct($id, User $user, $voted) {
		$this->id = $id;
		$this->user = $user;
		$this->voted = $voted;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getUser() {
		return $this->user;
	}
	
	public function getVoted() {
		return $this->voted;
	}
	
}
