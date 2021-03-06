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
	print htmlspecialchars($str);
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

/**
 * Return true, if the given parameter is empty.
 * Wrapper function for php empty predicate
 * @param mixed $data
 * @return boolean
 */
function is_empty($data) {
	return empty($data);
}

/**
 * Merges the given parameters to one associative array. When two
 * arrays found at the same position, then they will be merged, too.
 * 
 * @param array $array1
 * @param array ...
 * @return array
 */
function config_merge(array $array1) {
	$result = array();
	foreach (func_get_args() as $param) {
		foreach ($param as $key=>$value) {
			if (is_array($value) && array_key_exists($key, $result) && is_array($result[$key])) {
				$result[$key] = config_merge($result[$key], $value);
			} else {
				$result[$key] = $value;
			}
		}
	}
	return $result;
}

/**
 * Returns the first not null argument.
 * If there's no such argument, returns null
 * @param mixed ...
 * @return mixed
 */
function coalesce() {
	foreach (func_get_args() as $arg) {
		if (!is_null($arg)) {
			return $arg;
		}
	}
	return null;
}

/**
 * Implodes the content of the given associative array
 * 
 * @param array $array
 * @param string $betweenKeyAndValue
 * @param string $beforeItems
 * @param string $afterItems
 * @return string
 */
function implode_assoc(array $array, $betweenKeyAndValue, $beforeItems='', $afterItems='') {
	$result = '';
	foreach ($array as $k=>$v) {
		$result .= $beforeItems.$k.$betweenKeyAndValue.$v.$afterItems;
	}
	return $result;
}

/**
 * @see http://php.net/manual/en/function.getallheaders.php
 */
if (!function_exists('getallheaders')) {
	function getallheaders() {
		$out = array();
		foreach($_SERVER as $key=>$value) {
			if ('HTTP_' == substr($key,0,5)) {
				$key = str_replace(' ','-',ucwords(strtolower(str_replace('_',' ',substr($key,5)))));
				$out[$key] = $value;
			}
		}
		return $out;
	}
}
