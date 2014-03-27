<?php
require_once(dirname(__FILE__)  . "/lib/core.php");

fizzle('Game does not exist.', '404');

function fizzle($message) {
	header("HTTP/1.1 500 Internal Server Error");
	core::showHTML(array('layout' => 'htmlLayout', 'template' => 'error', 'error' => $message));
	exit(0);
}