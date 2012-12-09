<?php

interface ApplicationHandler {

	/**
	 * Determines the first executed forward by the given request
	 * 
	 * @param Request $request
	 * @return Forward
	 */
	public function parseInitialForward(Request $request);
	
}
