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
			$data = add_answer($team);
			break;
		default:
			$data = array('ok' => 'no', 'error' => 'Unknown action');
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
		return array('ok' => 'no', 'error' => 'No sortkey specified');
	}
	$round_sortkey = $_REQUEST['round_sortkey'];

	$round = round_model::get_by_round_sort($team -> get_game_id(), $round_sortkey);
	if($round === false) {
		return array('ok' => 'no', 'error' => 'Round does not exist');
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
				} else { // Cannot re-register at another table, to prevent sabotage.
					//$person_table -> set_team_id($team -> get_team_id());
					//$person_table -> update();
				}
				$set = true;
			}
		}
	}


	if(!$set) {
		return array('ok' => 'no', 'error' => 'Must select somebody');
	}

	return array('ok' => 'yes');
}

function add_answer($team) {
	if(!isset($_POST['answer_text']) || !isset($_POST['question_id'])) {
		return array('ok' => 'no', 'error' => 'Not enoguh information');
	}
	$question_id = (int)$_POST['question_id'];
	$answer_text = trim($_POST['answer_text']);
	
	if(!$question = question_model::get($question_id)) {
		return array('ok' => 'no', 'error' => 'Question not found');
	}
	if($answer = answer_model::get($question_id, $team -> get_team_id())) {
		/* Will not write over old answer, but must return without error so the client can move on */
		return array('ok' => 'yes');
	}
	if($question -> round -> get_game_id() != $team -> get_game_id()) {
		return array('ok' => 'no', 'error' => 'Question is not from this game');
	}

	$answer = new answer_model(array(
			'answer.question_id' => $question -> get_question_id(),
			'answer.team_id' => $team -> get_team_id(),
	));
	$answer -> set_answer_time(date("Y-m-d H:i:s"));
	$answer -> set_answer_text($answer_text);
	$answer -> insert();
	$data = array('ok' => 'yes');
}