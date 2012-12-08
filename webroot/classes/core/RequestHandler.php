<?php

class RequestHandler {
	
	/**
	 * @var UserService
	 */
	private $userService;
	
	/**
	 * @var DI
	 */
	private $di;
	
	public function __construct(UserService $userService, DI $di) {
		$this->userService = $userService;
		$this->di = $di;
	}
	
	public function run() {
		$user = $this->userService->authenticate();
		$request = $this->parseRequest($user);
		$forward = $this->parseInitialForward($request);
		do {
			$forward = $this->processActionForward($request, $forward);
		} while (!is_null($forward));
	}
	
	/**
	 * @return Forward
	 */
	private function parseInitialForward(Request $request) {
		if (is_null($request->getUser())) {
			$return = Url::create('http://www.tolkien.hu/privatevoting/');
			$location = Url::create('http://www.tolkien.hu/index.php')
				->option('com_user')->view('login')->return(base64_encode($return));
			return new RedirectForward($location);
		}
		$do = $request->get('do');
		if (is_null($do)) {
			$do = 'Init';
		}
		return new ActionForward($do.'Action');
	}
	
	/**
	 * @param Forward $forward
	 * @return Forward
	 */
	private function processActionForward(Request $request, Forward $forward) {
		if ($forward instanceof RedirectForward) {
			header('Location: '.$forward->getLocation());
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
		return new Request($_REQUEST, $user);
	}
	
}
