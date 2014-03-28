<?php
require_once(dirname(__FILE__)  . "/lib/core.php");
core::loadClass("game_model");

if(isset($_REQUEST['p']) && $_REQUEST['p'] != "") {
	$game_code = $_REQUEST['p'];
	if($game = game_model::get_by_game_code($game_code)) {
		$data['game'] = $game;
		core::showHTML(array('layout' => 'htmlLayout', 'template' => 'admin/main', $data));
	} else {
		fizzle('Game does not exist.', '404');
	}
} elseif(isset($_POST['game_name']) && trim($_POST['game_name']) != "") {
	$game_name = trim($_POST['game_name']);
	do {
		$game_code = core::makeCode();
	} while($test = game_model::get_by_game_code($game_code));
	
	$game = new game_model();
	$game -> set_game_name($game_name);
	$game -> set_game_code($game_code);
	$game -> insert();
	core::redirect("/trivia/game/$game_code");
	exit(0);
} else {
	core::showHTML(array('layout' => 'htmlLayout', 'template' => 'admin/newgame', array()));
}


function fizzle($message) {
	header("HTTP/1.1 500 Internal Server Error");
	core::showHTML(array('layout' => 'htmlLayout', 'template' => 'error', 'error' => $message));
	exit(0);
}