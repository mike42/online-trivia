<?php
require_once(dirname(__FILE__)  . "/lib/core.php");
core::loadClass("team_model");
core::loadClass("session");

if(!isset($_REQUEST['p']) || trim($_REQUEST['p']) == "") {
	core::redirect('/trivia/game/');
	exit(0);
}

$team_code = $_REQUEST['p'];
if(!$team = team_model::get_by_team_code($team_code)) {
	fizzle('Team does not exist. Check the code and try again.', '404');
	exit(0);
}

session::team_login($team_code);
core::showHTML(array('layout' => 'htmlLayout', 'template' => 'team/main', 'team' => $team));

function fizzle($message) {
	header("HTTP/1.1 500 Internal Server Error");
	core::showHTML(array('layout' => 'htmlLayout', 'template' => 'error', 'error' => $message));
	exit(0);
}
