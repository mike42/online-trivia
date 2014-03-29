<?php
require_once(dirname(__FILE__)  . "/lib/core.php");
core::loadClass("team_model");
core::loadClass("session");

if(!isset($_REQUEST['p']) || trim($_REQUEST['p']) == "") {
	core::redirect('/game/');
	exit(0);
}

$team_code = $_REQUEST['p'];
if(!$team = team_model::get_by_team_code($team_code)) {
	fizzle('Team does not exist. Check the code and try again.', '404');
	exit(0);
}
session::team_login($team_code);

if(isset($_POST['action'])) {
	switch($_POST['action']) {
		case 'people':
			$data = add_people($team);
			break;
		case 'answer':
			// TODO
			
			$data = array('ok' => 'yes');
		default:
			fizzle('Unknown action.', '500');
	}
	echo json_encode($data);
} else {
	$team -> game -> populate_list_round();
	foreach($team -> game -> list_round as $round_id => $round) {
		$round -> populate_list_question();
		foreach($round -> list_question as $id => $question) {
			if($answer = answer_model::get($question -> get_question_id(), $team -> get_team_id())) {
				unset($round -> list_question[$id]);
			}
		}
		if(count($round -> list_question) == 0) {
			unset($team -> game -> list_round[$round_id]);
		}
	}
	
	$team -> game -> populate_list_person();

	core::showHTML(array('layout' => 'htmlLayout', 'template' => 'team/main', 'team' => $team -> to_array_filtered('team')));
}

function fizzle($message) {
	header("HTTP/1.1 500 Internal Server Error");
	core::showHTML(array('layout' => 'htmlLayout', 'template' => 'error', 'error' => $message));
	exit(0);
}

function add_people(team_model $team) {
	if(!isset($_REQUEST['round_sortkey'])) {
		fizzle('No sortkey specified');
	}
	$round_sortkey = $_REQUEST['round_sortkey'];

	$round = round_model::get_by_round_sort($team -> get_game_id(), $round_sortkey);
	if($round === false) {
		return array('error' => "Round does not exist");
	}
	$set = false;
	foreach($_POST as $key => $val) {
		$a = explode("-", $key);
		if($a[0] == "person" && count($a) == 2 && is_numeric($a[1])) {
			$person_id = $a[1];
			$person = person_model::get($person_id);
			if(!($person === false) && $person -> get_game_id() == $round -> get_game_id()) {
				$person_table = person_table_model::get($round -> get_round_id(), $person_id);
				if($person_table === false) {
					$person_table = new person_table_model(array(
							'person_table.round_id' => $round -> get_round_id(),
							'person_table.team_id' => $team -> get_team_id(),
							'person_table.person_id' => $person -> get_person_id()));
					$person_table -> insert();
				} else { // Steal the person
					$person_table -> set_team_id($team -> get_team_id());
					$person_table -> update();
				}
				$set = true;
			}
		}
	}
	
	
	if(!$set) {
		
	}
	$data = array('ok' => 'yes');
	return $data;
}