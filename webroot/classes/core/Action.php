<?php

interface Action {

	/**
	 * @param requets data $request
	 * @return Forward
	 */
	public function serve(array $request);
	
}
