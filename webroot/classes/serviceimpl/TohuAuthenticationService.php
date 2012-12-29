<?php

class TohuAuthenticationService extends DbServiceBase implements AuthenticationService {
	
	const SESSIONS_TABLE = 'jos_session';
	const SESSIONS_ID_FIELD = 'session_id';
	const SESSIONS_USERID_FIELD = 'userid';
	
	/**
	 * @var UserService
	 */
	private $userService;
	
	/**
	 * @var Config
	 */
	private $config;
	
	public function __construct(ToHuDatabase $db, UserService $userService, Config $config) {
		parent::__construct($db);
		$this->userService = $userService;
		$this->config = $config;
	}
	
	/**
	 * @return User or null
	 */
	public function authenticate() {
		$sessionId = $this->getJoomlaSessionId();
		if (is_null($sessionId)) {
			return null;
		}
		
		$userId = $this->db->queryCell(QueryBuilder::create()
			->from(self::SESSIONS_TABLE)
			->where(self::SESSIONS_ID_FIELD.'=?', $sessionId)
			->select(self::SESSIONS_USERID_FIELD), null, true);
		if (is_null($userId) || $userId==0) {
			return null;
		}
		
		return $this->userService->findUserById($userId);
	}
	
	private function getJoomlaSessionId() {
		require_once($this->config->get('joomla_config_file'));
		$config = new JConfig();
		$sessionName = md5(md5($config->secret.'site'));
		if (array_key_exists($sessionName, $_COOKIE)) {
			return $_COOKIE[$sessionName];
		} else {
			return null;
		}
	}

}
