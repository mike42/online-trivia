<?php
require_once(dirname(__FILE__)  . "/lib/core.php");
core::loadClass("game_model");

if(isset($_REQUEST['p']) && $_REQUEST['p'] != "") {
	$game_code = $_REQUEST['p'];
	if($game = game_model::get_by_game_code($game_code)) {
		if(isset($_GET['projector'])) {
			core::showHTML(array('layout' => 'htmlLayout', 'template' => 'projector/main', 'game' => $game));
		} else if(isset($_GET['zen'])) {
			core::showHTML(array('layout' => 'htmlLayout', 'template' => 'zen/main', 'game' => $game));
		} else {
			$action = "";
			if(isset($_POST['action'])) {
				$action = $_POST['action'];
			}
			switch($action) {
				case 'rename':
					if(isset($_POST['game_name']) && trim($_POST['game_name']) != "") {
						$game -> set_game_name(trim($_POST['game_name']));
						$game -> update();
					}
					break;
				case 'reset':
					$game -> reset();
					break;				
			}
			
			core::showHTML(array('layout' => 'htmlLayout', 'template' => 'admin/main', 'game' => $game));
		}
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
