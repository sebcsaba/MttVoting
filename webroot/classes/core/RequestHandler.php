<?php

class RequestHandler {
	
	/**
	 * @var DI
	 */
	private $di;
	
	public function __construct(DI $di) {
		$this->di = $di;
	}
	
	public function run() {
		$request = $this->parseRequest();
		$forward = $this->parseInitialForward($request);
		do {
			$forward = $this->processActionForward($request, $forward);
		} while (!is_null($forward));
	}
	
	/**
	 * @return Forward
	 */
	private function parseInitialForward(Request $request) {
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
	private function parseRequest() {
		return new Request($_REQUEST);
	}
	
}
