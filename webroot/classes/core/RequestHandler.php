<?php

class RequestHandler {
	
	/**
	 * @var AuthenticationService
	 */
	private $authenticationService;
	
	/**
	 * @var ApplicationHandler
	 */
	private $applicationHandler;
	
	/**
	 * @var Database
	 */
	private $database;
	
	/**
	 * @var Config
	 */
	private $config;
	
	/**
	 * @var DI
	 */
	private $di;
	
	public function __construct(AuthenticationService $authenticationService, ApplicationHandler $applicationHandler, Database $database, Config $config, DI $di) {
		$this->authenticationService = $authenticationService;
		$this->applicationHandler = $applicationHandler;
		$this->database = $database;
		$this->config = $config;
		$this->di = $di;
	}
	
	public function run() {
		$this->initializePhpEnvironment();
		try {
			$this->database->startTransaction();
			$user = $this->authenticationService->authenticate();
			$request = $this->parseRequest($user);
			$forward = $this->applicationHandler->parseInitialForward($request);
			do {
				$forward = $this->processActionForward($request, $forward);
			} while (!is_null($forward));
			$this->database->commit();
		} catch (Exception $e) {
			$this->database->rollback();
			$this->processActionForward($request, new ErrorForward($e->getMessage()));
		}
	}
	
	/**
	 * @param Forward $forward
	 * @return Forward
	 */
	private function processActionForward(Request $request, Forward $forward) {
		if ($forward instanceof RedirectForward) {
			if ($request->isAjax()) {
				header('X-Location: '.$forward->getLocation());
			} else {
				header('Location: '.$forward->getLocation());
			}
			return null;
		} else if ($forward instanceof PageForward) {
			require_once('pages/'.$forward->getPage().'.tpl.php');
			return null;
		} else if ($forward instanceof ActionForward) {
			$className = $forward->getClassName();
			$action = $this->di->get($className);
			return $action->serve($request);
		} else if ($forward instanceof ErrorForward) {
			$message = $this->handleError($forward->getMessage());
			if ($request->isAjax()) {
				header('X-Error: '.$this->encodeHeader($message));
			} else {
				$request->setData('message', $message);
				require_once('pages/error.tpl.php');
			}
			return null;
		}
	}
	
	/**
	 * @return Requets
	 */
	private function parseRequest(User $user = null) {
		return new Request(getallheaders(), $_REQUEST, $user);
	}
	
	/**
	 * Encodes the given string to quoted-printable
	 * 
	 * @param string $string
	 * @return string
	 */
	private function encodeHeader($string) {
		$string = preg_replace('/\s/', ' ',$string);
		$string = quoted_printable_encode($string);
		$string = preg_replace('/=\s+/','',$string);
		return '=?UTF-8?Q?'.$string.'?=';
	}
	
	/**
	 * Handler the given error message, depending on the current lifecycle
	 * 
	 * @param string $errorMessage
	 * @return string The message to show
	 */
	private function handleError($errorMessage) {
		if ('development'==$this->config->get('lifecycle')) {
			return $errorMessage;
		} else {
			$errorLine = sprintf("[%s] from [%s] accessing [%s]: %s\n", date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $errorMessage);
			error_log($errorLine, 3, 'temp/error.log');
			return 'Sajnáljuk, valami hiba történt. Keresd meg vele sebcsabát!';
		}
	}
	
	private function initializePhpEnvironment() {
		if ('development'==$this->config->get('lifecycle')) {
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		} else {
			ini_set('display_errors', 0);
		}
	}
	
}
