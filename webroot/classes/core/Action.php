<?php

interface Action {

	/**
	 * @param Request $request
	 * @return Forward
	 */
	public function serve(Request $request);
	
}
