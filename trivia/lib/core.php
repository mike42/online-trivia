<?php
class core {
	/**
	 * Some defaults set in index.php
	 */
	public static $config;
	public static $permission;

	/**
	 * Load a class given its filename, and call FooClass::init()
	 * 
	 * @param string $fn		Filename where we expect to find this class
	 * @param string $className	Name of the class being loaded
	 * @throws Exception
	 */
	static public function loadClassFromFile($fn, $className) {
		if(!file_exists($fn)) {
			throw new Exception("The class '$className' could not be found at $fn.");
		}

		require_once($fn);
		if(is_callable($className . "::init")) {
			try {
				call_user_func($className . "::init");
			} catch(Exception $e) {
				/* JSON-encode the error message if a class fails to start */
				core::fizzle($e -> getMessage(), '500');
			}
		}
	}

	/**
	 * @param unknown_type $section
	 * @throws Exception if the section does not exist
	 * @return unknown
	 */
	static public function getConfig($section) {
		include(dirname(__FILE__) . "/../site/config.php");
		if(!isset($config[$section])) {
			throw new Exception("No configuration found for '$section'");
		}
		return $config[$section];
	}

	/**
	 * Load a class by name
	 *
	 * @param string $className The name of the class to load.
	 */
	static public function loadClass($className) {
		if(!class_exists($className)) {
			$sp = explode("_", $className);

			if(count($sp) == 1) {
				/* If there are no underscores, it should be in misc */
				$sp[0] = self::alphanumeric($sp[0]);
				$fn = dirname(__FILE__)."/util/".$sp[0].".php";
			} else {
				/* Otherwise look in the folder suggested by the name */
				$folder = self::alphanumeric(array_pop($sp));
				$classfile = self::alphanumeric($className);
				$fn = dirname(__FILE__)."/$folder/$classfile.php";
			}

			self::loadClassFromFile($fn, $className);
		}
	}

	static public function constructURL($controller, $action, $arg, $fmt) {
		$config = self::$config;
		$part = array();

		if(count($arg) == 1 && $action == $config['default']['action']) {
			/* We can abbreviate if there is only one argument and we are using the default view */
			if($controller != $config['default']['controller'] ) {
				/* The controller isn't default, need to add that */
				array_push($part, urlencode($arg[0]));
				array_unshift($part, urlencode($controller));
			} else {
				/* default controller and action. Check for default args */
				if($arg[0] != $config['default']['arg'][0]) {
					array_push($part, urlencode($arg[0]));
				}
			}
		} else {
			/* urlencode all arguments */
			foreach($arg as $a) {
				array_push($part, urlencode($a));
			}

			/* Nothing is default: add controller and view */
			array_unshift($part, urlencode($controller), urlencode($action));
		}

		/* Only add format suffix if the format is non-default (ie, strip .html) */
		$fmt_suff = (($fmt != $config['default']['format'])? "." . urlencode($fmt) : "");
		return $config['webroot'] . implode("/", $part) . $fmt_suff;
	}

	/**
	 * Escape user-provided string for safe inclusion in HTML code
	 * 
	 * @param string $inp
	 * @return string
	 */
	public static function escapeHTML($inp) {
		return htmlentities($inp, null, 'UTF-8');
	}

	/**
	 * Clear anything other than alphanumeric characters from a string (to prevent arbitrary inclusion)
	 *
	 * @param string $inp	An input string to be sanitised.
	 * @return string		The input string containing alphanumeric characters only
	 */
	static public function alphanumeric($inp) {
		return preg_replace("#[^-a-zA-Z0-9]+#", "_", $inp);
	}

	public static function fizzle($message, $code=500) {
		switch($code) {
			case "403":
				header("HTTP/1.1 403 Forbidden");
				break;
			case "404":
				header("HTTP/1.1 404 Not Found");
				break;
			case "500":
			default:
				header("HTTP/1.1 500 Internal Server Error");
		}
		echo json_encode(array("error" => $message));
		exit(0);
	}

	public function redirect($location) {
		header("location: $location");
	}
	
	public static function init() {
		/* Load permissions */
		include(dirname(__FILE__) . "/../site/permissions.php");
		self::$permission = $permission;
	}
}
core::init();
