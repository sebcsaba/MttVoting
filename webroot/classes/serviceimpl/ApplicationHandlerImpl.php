<?php

class ApplicationHandlerImpl implements ApplicationHandler {
	
	/**
	 * Determines the first executed forward by the given request
	 * 
	 * @param Request $request
	 * @return Forward
	 */
	public function parseInitialForward(Request $request) {
		if (is_null($request->getUser())) {
			return $this->createAuthenticationRedirect();
		}
		$do = $request->get('do');
		if (is_null($do)) {
			$do = 'Init';
		}
		return new ActionForward($do.'Action');
	}
	
	private function createAuthenticationRedirect() {
		// TODO use $config['url'] here, and delegate auth url too
		$return = Url::create('http://www.tolkien.hu/privatevoting/');
		$location = Url::create('http://www.tolkien.hu/index.php')
			->option('com_user')->view('login')->return(base64_encode($return));
		return new RedirectForward($location);
	}
	
}
