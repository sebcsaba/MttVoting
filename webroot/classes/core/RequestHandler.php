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
	 * @var DI
	 */
	private $di;
	
	public function __construct(AuthenticationService $authenticationService, ApplicationHandler $applicationHandler, Database $database, DI $di) {
		$this->authenticationService = $authenticationService;
		$this->applicationHandler = $applicationHandler;
		$this->database = $database;
		$this->di = $di;
	}
	
	public function run() {
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
			header('X-Error: '.$this->encodeHeader($e->getMessage()));
			throw $e;
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
			$action = $this->di->create($className);
			return $action->serve($request);
		} else if ($forward instanceof ErrorForward) {
			header('X-Error: '.$this->encodeHeader($forward->getMessage()));
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
	
}
