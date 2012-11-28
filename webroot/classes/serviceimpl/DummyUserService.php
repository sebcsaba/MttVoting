<?php

class DummyUserService implements UserService {
	
	private $users;
	
	public function __construct() {
		$this->users = array(
			new User(1, 'alpha'),
			new User(2, 'beta'),
			new User(3, 'gamma'),
		);
	}
	
	/**
	 * @return User or null
	 */
	public function authenticate() {
		return $this->users[0];
	}
	
	/**
	 * @param string $name
	 * @return User[]
	 */
	public function findUsersByName($name) {
		return $this->findUsers(function(User $user){
			return FALSE !== strpos($user->getLoginName(), $name);
		});
	}
	
	/**
	 * @param array[int] $ids
	 * @return User[]
	 */
	public function findUsersByIds(array $ids) {
		return $this->findUsers(function(User $user){
			return in_array($user->getUserId(), $ids);
		});
	}
	
	private function findUsers($callback) {
		$result = array();
		foreach ($this->users as $user) {
			if ($callback($user)) {
				$result[] = $user;
			}
		}
		return $result;
		
	}
	
}
