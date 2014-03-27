<?php
require_once(dirname(__FILE__)."/lib/core.php");
core::loadClass("database");

/* Set up some basic web things */
$config['host']						= isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : 'localhost';
$config['webroot']					= isset($_SERVER['HTTP_HOST'])? 'http://'.$_SERVER['HTTP_HOST'].'/' : '';
$config['default']['controller']	= 'Page';

/* Map HTTP reuest types to methods */
switch($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		$config['default']['action']		= 'read';
		break;
	case 'POST':
		$config['default']['action']		= 'create';
		break;
	case 'PUT':
		$config['default']['action']		= 'update';
		break;
	case 'DELETE':
		$config['default']['action']		= 'delete';
		break;	
}
$config['default']['arg']		= array('home');
$config['default']['format']		= 'html';
Core::$config = $config;

/* Get page (or go to default if none is specified) */
if(isset($_GET['p']) && $_GET['p'] != '') {
	$arg = explode('/', $_REQUEST['p']);
} else {
	$arg = $config['default']['arg'];
}

/* Get any extension appearing at the end of the request: */
$tail = count($arg) - 1;
$fmtsplit = explode('.', $arg[$tail]);
if(count($fmtsplit) >= 2) {
	/* One or more extensions on word, eg .rss, .tar.gz */
	$arg[$tail] = array_shift($fmtsplit);
	$fmt = implode('.', $fmtsplit);
} else {
	/* No extensions at all */
	$fmt = $config['default']['format'];
}

/* Switch for number of arguments */
if(count($arg) > 2) {
	/* $controller/$action/{foo/bar/baz}.quux */
	$controller = array_shift($arg);
	$action = array_shift($arg);
} elseif(count($arg) == 2) {
	/* No action specified - $controller/(default action)/{foo}.quux */
	$controller = array_shift($arg);
	$action = $config['default']['action'];
} elseif(count($arg) == 1) {
	/* No action or controller */
	$controller = array_shift($arg);
	$action = $config['default']['action'];
}

/* Figure out class and method name */
try {
	$controllerClassName = $controller.'_controller';
	$controllerMethodName = $action;
	core::loadClass($controllerClassName);
	if(!is_callable($controllerClassName . "::" . $controllerMethodName)) {
		core::fizzle("Controller '$controllerClassName' does not have method '$controllerMethodName'", '404');
	}
	$ret = call_user_func_array(array($controllerClassName, $controllerMethodName), $arg);
	if(isset($ret['error'])) {
		/* Something went wrong */
		core::fizzle($ret['error'], isset($ret['code']) ? $ret['code'] : '500');
	} else {
		echo json_encode($ret);
	}
} catch(Exception $e) {
	core::fizzle("Failed to run controller: " . $e -> getMessage(), '500');
}