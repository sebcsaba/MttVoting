<?php

class RequestHandler {
	
	/**
	 * @var UserService
	 */
	private $userService;
	
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
	
	public function __construct(UserService $userService, ApplicationHandler $applicationHandler, Database $database, DI $di) {
		$this->userService = $userService;
		$this->applicationHandler = $applicationHandler;
		$this->database = $database;
		$this->di = $di;
	}
	
	public function run() {
		try {
			$this->database->startTransaction();
			$user = $this->userService->authenticate();
			$request = $this->parseRequest($user);
			$forward = $this->applicationHandler->parseInitialForward($request);
			do {
				$forward = $this->processActionForward($request, $forward);
			} while (!is_null($forward));
			$this->database->commit();
		} catch (Exception $e) {
			$this->database->rollback();
			header('X-Error: '.$e->getMessage());
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
		}
	}
	
	/**
	 * @return Requets
	 */
	private function parseRequest(User $user = null) {
		return new Request(getallheaders(), $_REQUEST, $user);
	}
	
}
