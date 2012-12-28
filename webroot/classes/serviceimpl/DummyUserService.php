<?php

class DummyUserService implements UserService, AuthenticationService {
	
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
		/*if (I(getallheaders(),'X-Requested-With')=='XMLHttpRequest') {
			return null;
		}*/
		//return null;
		return $this->users[0];
	}
	
	/**
	 * Find users who contains the given search string in theirs name
	 * 
	 * @param string $name
	 * @return User[]
	 */
	public function findUsersByName($name) {
		return $this->findUsers(function(User $user, $name){
			return FALSE !== strpos($user->getLoginName(), $name);
		}, $name);
	}
	
	/**
	 * Loads the user by the given id, or return null if not found
	 * 
	 * @param int $id
	 * @return User or null
	 */
	public function findUserById($id) {
		foreach ($this->users as $user) {
			if ($user->getUserId()==$id) {
				return $user;
			}
		}
		return null;
	}
	
	/**
	 * Returns the users with theirs id in the given array
	 * 
	 * @param array[int] $ids
	 * @return User[]
	 */
	public function findUsersByIds(array $ids) {
		return $this->findUsers(function(User $user, array $ids){
			return in_array($user->getUserId(), $ids);
		}, $ids);
	}
	
	private function findUsers($callback, $param) {
		$result = array();
		foreach ($this->users as $user) {
			if ($callback($user, $param)) {
				$result[] = $user;
			}
		}
		return $result;
		
	}
	
}
