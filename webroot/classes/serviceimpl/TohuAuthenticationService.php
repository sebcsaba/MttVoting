<?php

// TODO check db names: tables, fields
class TohuAuthenticationService extends DbServiceBase implements AuthenticationService {
	
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
	
}
