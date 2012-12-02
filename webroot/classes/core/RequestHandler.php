<?php

class RequestHandler {
	
	public function run() {
		$forward = $this->parseForwardFromRequest();
		do {
			$forward = $this->processActionForward($forward);
		} while (!is_null($forward));
	}
	
	/**
	 * @return Forward
	 */
	private function parseForwardFromRequest() {
		// TODO implement: parse $_REQUEST to initial Forward
		return new PageForward('index');
	}
	
	/**
	 * @param Forward $forward
	 * @return Forward
	 */
	private function processActionForward(Forward $forward) {
		if ($forward instanceof RedirectForward) {
			header('Location: '.$forward->getLocation());
			return null;
		} else if ($forward instanceof PageForward) {
			require_once('pages/'.$forward->getPage().'.tpl.php');
			return null;
		} else if ($forward instanceof ActionForward) {
			$className = $forward->getClassName();
			$action = new $className(); // TODO implement DI here
			return $action->serve($_REQUEST);
		}
	}
	
}
