<?php

interface AuthenticationService {
	
	/**
	 * @return User or null
	 */
	public function authenticate();
	
}
