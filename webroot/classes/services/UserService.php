<?php

interface UserService {

	/**
	 * @return User or null
	 */
	public function authenticate();
	
	/**
	 * @param string $name
	 * @return User[]
	 */
	public function findUsersByName($name);
	
	/**
	 * @param array[int] $ids
	 * @return User[]
	 */
	public function findUsersByIds(array $ids);
	
}
