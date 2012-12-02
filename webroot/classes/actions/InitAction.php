<?php

class InitAction implements Action {
	
	/**
	 * @param Request $request
	 * @return Forward
	 */
	public function serve(Request $request) {
		return new PageForward('index');
	}
	
}