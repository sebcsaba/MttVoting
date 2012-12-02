<?php

class RequestHandler {
	
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
		// TODO implement: parse $_REQUEST to initial Forward
		return new ActionForward('InitAction');
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
			$action = new $className(); // TODO implement DI here
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
