<?php

/**
 * Cache
 * @author Kalashnikov Igor <igor.kalashnikov@gmail.com>
 * @license Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported
 */ 
class Cache {

	public static function set($key, $value, $ttl = 60) {
		if (function_exists('apc_store') === false) {
			return false;
		}
		apc_store($key, $value, $ttl);
	}

	public static function get($key) {
		if (function_exists('apc_fetch') === false) {
			return false;
		}

		$success = false;

		$val = apc_fetch($key, $success);

		if (!$success) {
			return false;
		} else {
			return $val;
		}
	}

}
