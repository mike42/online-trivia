<?php
class team_controller {
	public static function init() {
		core::loadClass("team_model");
		core::loadClass("game_model");
		core::loadClass("round_model");
		core::loadClass("person_model");
		core::loadClass("question_model");
		core::loadClass("answer_model");
		core::loadClass("person_table_model");
	}

	function create() {
		return array('error' => "Feature does not exist");
	}

	function read($team_code) {
		$team = team_model::get_by_team_code($team_code);
		if($team === false) {
			return array('error' => "Team does not exist");
		}
		
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
		return array('team' => $team);
	}

	function people($team_code, $round_id) {
		$round = round_model::get($round_id);
		if($round === false) {
			return array('error' => "Round does not exist");
		}
		$team = team_model::get_by_team_code($team_code);
		if($team === false || $team -> get_game_id() != $round -> get_game_id()) {
			return array('error' => "Team does not exist in this game");
		}
		
		$set = false;
		foreach($_POST as $key => $val) {
			$a = explode("-", $key);
			if($a[0] == "person" && count($a) == 2 && is_numeric($a[1])) {
				$person_id = $a[1];
				$person = person_model::get($person_id);
				if(!($person === false) && $person -> get_game_id() == $round -> get_game_id()) {
					$person_table = person_table_model::get($round_id, $person_id);
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
		
		if($set) {
			core::redirect(core::constructURL("team", "round", array($team -> get_team_code(), $round -> get_round_id()), 'html'));
			exit(0);
		}
		
		$people = person_model::list_by_game_id($team -> get_game_id());
		return array('team' => $team, 'round' => $round, 'people' => $people);
	}

	function round($team_code, $round_id) {
		$team = team_model::get_by_team_code($team_code);
		if($team === false) {
			return array('error' => "Team does not exist");
		}
		$round = round_model::get($round_id);
		if($round === false) {
			return array('error' => "Team does not exist");
		}
		if(isset($_POST['question_id']) && isset($_POST['answer_text'])) {
			/* Submit answer if required */
			$question = question_model::get($_POST['question_id']);
			if(!($question === false) && $question -> get_round_id() == $round -> get_round_id()) {
				$answer = answer_model::get($question -> get_question_id(), $team -> get_team_id());
				if($answer === false) {
					$answer = new answer_model(array('answer.question_id' => $question -> get_question_id(), 'answer.team_id' => $team -> get_team_id()));
					$answer -> set_answer_text(trim($_POST['answer_text']));
					$answer -> insert();
				}
			}
		}
		
		
		$round -> populate_list_question();
		foreach($round -> list_question as $id => $question) {
			if($answer = answer_model::get($question -> get_question_id(), $team -> get_team_id())) {
				unset($round -> list_question[$id]);
			}
		}

		$data['round'] = $round;
		$data['team'] = $team;
		return $data;
	}

	function update() {
		return array('error' => "Feature does not exist");
	}

	function delete() {
		return array('error' => "Feature does not exist");
	}
	
	function qr($team_code) {
		$team = team_model::get_by_team_code($team_code);
		if($team === false) {
			return array('error' => "Team does not exist");
		}
		$data = array('team' => $team);
	
		$data = self::read($team_code);
		$data['url'] = core::constructURL('team', 'read', array($team_code), 'html');
		$data['png'] = core::constructURL('team', 'qr', array($team_code), 'png');
		return $data;
	}
}
?>
