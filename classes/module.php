<?php

function php2js($a = false, $level = 0)
{
	if (is_null($a)) 
		return 'null';
	if ($a === false) 
		return 'false';
	if ($a === true) 
		return 'true';
	
	if (is_scalar($a)) {
		if (is_float($a)) {
			// Always use "." for floats.
			$a = str_replace(',', '.', strval($a));
		}

		// All scalars are converted to strings to avoid indeterminism.
		// PHP's "1" and 1 are equal for all PHP operators, but
		// JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
		// we should get the same result in the JS frontend (string).
		// Character replacements for JSON.
		static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
			array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
		return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
	}

	$isList = true;

	for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
		if (key($a) !== $i) {
			$isList = false;
			break;
		}
	}

	$result = array();

	$level++;

	if ($isList) {
		foreach ($a as $v) {
			$result[] = php2js($v, $level);
		}

		return '[' . join(',', $result) . ']';
	} else {
		foreach ($a as $k => $v) {
			$item = php2js($k, $level) . ':' . php2js($v, $level);

			if (defined('DEBUG')) {
				$item = "\n" . str_repeat("\t", $level) . $item;
			}

			$result[] = $item;
		}

		return '{' . join(',', $result) . '}';
	}
}

class Module {

	private $request;
	private $db;

	public function __construct($request) {
		$this->request = $request;
	}
	
	protected function db() {
		if (!$this->db) {
			$db = new Mongo();
			$this->db = $db->selectDB(DB);
		}
		
		return $this->db;
	}
	
	protected function runModule() {
	
	}

	public function run() {
		$object = $this->runModule();
		
		if (
			is_object($object) 
			|| is_array($object)
		) {
			header('Content-Type: application/json');
			echo php2js($object);
		}
	}

}
