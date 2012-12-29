<?php

class TohuUserService extends DbServiceBase implements UserService {
	
	const USERS_TABLE = 'jos_users';
	const USERS_NAME_FIELD = 'name';
	const USERS_ID_FIELD = 'id';
	
	public function __construct(ToHuDatabase $db) {
		parent::__construct($db);
	}
	
	/**
	 * Find users who contains the given search string in theirs name
	 * 
	 * @param string $name
	 * @return User[]
	 */
	public function findUsersByName($name) {
		$query = QueryBuilder::create()->from(self::USERS_TABLE)->where(self::USERS_NAME_FIELD.' LIKE ? COLLATE utf8_general_ci', '%'.$name.'%');
		return $this->loadUsers($query);
	}
	
	/**
	 * Loads the user by the given id, or return null if not found
	 * 
	 * @param int $id
	 * @return User or null
	 */
	public function findUserById($id) {
		$query = QueryBuilder::create()->from(self::USERS_TABLE)->where(self::USERS_ID_FIELD.'=?', $id);
		$row = $this->db->queryRow($query, true);
		if (is_null($row)) {
			return null;
		} else {
			return new User($row[self::USERS_ID_FIELD], $row[self::USERS_NAME_FIELD]);
		}
	}
	
	/**
	 * @param array[int] $ids
	 * @return User[]
	 */
	public function findUsersByIds(array $ids) {
		$query = QueryBuilder::create()->from(self::USERS_TABLE)->where(self::USERS_ID_FIELD.' in (?)', join(',', $ids));
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
			$result []= new User($row[self::USERS_ID_FIELD], $row[self::USERS_NAME_FIELD]);
		}
		return $result;
	}
	
}
