<?php

class ApplicationHandlerImpl implements ApplicationHandler {
	
	/**
	 * @var Config
	 */
	private $config;
	
	public function __construct(Config $config) {
		$this->config = $config;
	}
	
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
		$return = Url::create($this->config->get('url'));
		$location = Url::create($this->config->get('authentication_url'))->return(base64_encode($return));
		return new RedirectForward($location);
	}
	
}
