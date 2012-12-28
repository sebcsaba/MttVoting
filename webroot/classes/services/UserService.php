<?php

interface UserService {

	/**
	 * Find users who contains the given search string in theirs name
	 * 
	 * @param string $name
	 * @return User[]
	 */
	public function findUsersByName($name);
	
	/**
	 * Loads the user by the given id, or return null if not found
	 * 
	 * @param int $id
	 * @return User or null
	 */
	public function findUserById($id);
	
	/**
	 * Returns the users with theirs id in the given array
	 * 
	 * @param array[int] $ids
	 * @return User[]
	 */
	public function findUsersByIds(array $ids);
	
}
