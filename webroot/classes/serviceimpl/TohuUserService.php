<?php

// TODO check db names: tables, fields
class TohuUserService extends DbServiceBase implements UserService {
	
	/**
	 * @return User or null
	 */
	public function authenticate() {
		if (!isset($_COOKIE['phpbb3_1pabk_sid'])) return null;
		$sessionId = $_COOKIE['phpbb3_1pabk_sid'];
		// TODO
		$query = QueryBuilder::create()->from('session');
		$userId = $this->db->queryCell($query);
		
		$queryUser = QueryBuilder::create()->from('users')->where('id = ?', $userId);
		$row = $this->db->queryRow($query);
		return new User($row['id'], $row['name']);
	}
	
	/**
	 * Find users who contains the given search string in theirs name
	 * 
	 * @param string $name
	 * @return User[]
	 */
	public function findUsersByName($name) {
		$query = QueryBuilder::create()->from('users')->where('name ilike ?', '%'.$name.'%');
		return $this->loadUsers($query);
	}
	
	/**
	 * Loads the user by the given id, or return null if not found
	 * 
	 * @param int $id
	 * @return User or null
	 */
	public function findUserById($id) {
		$query = QueryBuilder::create()->from('users')->where('id=?', $id);
		$row = $this->db->queryRow($query, true);
		if (is_null($row)) {
			return null;
		} else {
			return new User($row['id'], $row['name']);
		}
	}
	
	/**
	 * @param array[int] $ids
	 * @return User[]
	 */
	public function findUsersByIds(array $ids) {
		$query = QueryBuilder::create()->from('users')->where('id in (?)', join(',', $ids));
		return $this->loadUsers($query);
	}
	
	/**
	 * Returns the users with theirs id in the given array
	 * 
	 * @param QueryBuilder $query
	 * @return User[]
	 */
	private function loadUsers(QueryBuilder $query) {
		$result = array();
		foreach ($this->db->query($query) as $row) {
			$result []= new User($row['id'], $row['name']);
		}
		return $result;
	}
	
}
