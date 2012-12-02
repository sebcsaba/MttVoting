<?php

/**
 * Indexing to array, and fallback if no key exists
 * @param array $x
 * @param scalar $i
 * @param mixed $default
 * @return item of $x indexed by $i if exists, $default otherwise
 */
function I($x,$i,$default=null) {
	return isset($x[$i]) ? $x[$i] : $default;
}

/**
 * Return the given string encoded to html-safe.
 * If other parameters are given, use sprintf() first to all parameters.
 * @param string $str
 * @param mixed ...
 * @return string HTML-encoded string
 */
function h($str) {
	if (func_num_args()>1) {
		$str = call_user_func_array('sprintf', func_get_args());
	}
	return htmlspecialchars($str);
}

/**
 * Like func_get_args(), returns its invoker function call's parameters, but without the first item
 * @return array
 */
function func_get_args_but_first() {
	$st = debug_backtrace();
	$args = $st[1]['args'];
	array_shift($args);
	return $args;
}
