<?php

class DbConnectionParameters {

	private $protocol;
	private $host;
	private $port;
	private $username;
	private $password;
	private $database;
	
	public function __construct($protocol, $host, $port, $username, $password, $database) {
		$this->protocol = $protocol;
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
	}

	public function getProtocol() {
		return $this->protocol;
	}

	public function getHost() {
		return $this->host;
	}

	public function getPort() {
		return $this->port;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getPassword() {
		return $this->password;
	}

	public function getDatabase() {
		return $this->database;
	}
	
}
