<?php
class game_controller {
	public static function init() {
		core::loadClass("game_model");
		core::loadClass("round_model");
		core::loadClass("team_model");
		core::loadClass("person_model");
		core::loadClass("question_model");
		core::loadClass("answer_model");
	}

	public static function create() {
		return array('error' => "Feature does not exist");
	}

	public static function read($game_code) {
		/* Load up the game */
		$game = game_model::get_by_game_code($game_code);
		if($game === false) {
			return array('error' => "Game does not exist");
		}
		
		/* Make any changes as needed */
		$action = "";
		if(isset($_POST['action'])) {
			$action = $_POST['action'];
		}
		switch($action) {
			case "rename":
				if(isset($_POST['game_name'])) {
					$game -> set_game_name($_POST['game_name']);
					$game -> update();
				}
				break;
			case "add-people":
				if(isset($_POST['people_names'])) {
					$names = explode("\n", $_POST['people_names']);
					foreach($names as $name) {
						$name = trim($name);
						if($name != "") {
							$person = new person_model();
							$person -> set_person_name($name);
							$person -> set_game_id($game -> get_game_id());
							$person -> insert();
						}
					}
				}
				break;
			case "delete-person":
				if(isset($_POST['person_id'])) {
					$person = person_model::get($_POST['person_id']);
					if($person === false) {
						return array('error' => "Person does not exist");
					}
					if($person -> get_game_id() == $game -> get_game_id()) {
						try {
							$person -> delete();
							return array('status' => 'Person deleted', 'game' => $game);
						} catch(Exception $e) {
							return array('error' => "Can't delete person. They have probably joined a table already!");
						}
					}
					return array('error' => "Person is not from this game");
				}
				return array('error' => "Person ID required");
			case "reset":
				$game -> reset();
				break;
			default:
				// Nothing particular to do
		}
		
		/* Load all related fields */
		$game -> populate_list_team();
		$game -> populate_list_round();
		$game -> populate_list_person();
		foreach($game -> list_round as $id => $round) {
			$game -> list_round[$id]-> populate_list_question();
		}
		return array('game' => $game);
	}
	
	public static function zen($game_code) {
		/* Load up the game */
		$game = game_model::get_by_game_code($game_code);
		if($game === false) {
			return array('error' => "Game does not exist");
		}
	
		if(isset($_POST['action']) && $_POST['action'] == 'correct' && isset($_POST['team_id']) && isset($_POST['question_id']) && isset($_POST['answer_is_correct'])) {
			$team_id = $_POST['team_id'];
			$question_id = $_POST['question_id'];
			$answer = answer_model::get($question_id, $team_id);
			$answer_is_correct = (int)$_POST['answer_is_correct'];
			if($answer === false || $answer -> team -> get_game_id() != $game -> get_game_id()) {
				return array('error' => "Answer not found.");
			}
			$answer -> set_answer_is_correct($answer_is_correct);
			$answer -> update();
			return array('status' => 'ok');
		}
	
		$data['answers'] = answer_model::list_unmarked();
		$data['game'] = $game;
		return $data;
	}
	
	public static function leaderboard($game_code) {
		$game = game_model::get_by_game_code($game_code);
		if($game === false) {
			return array('error' => "Game does not exist");
		}
		
		$data['leaderboard'] = answer_model::leaderboard($game -> get_game_id());
		return $data;
	}
	
	public static function mc($game_code) {
		return self::read($game_code);
	}

	public static function update() {
		return array('error' => "Feature does not exist");
	}

	public static function delete() {
		return array('error' => "Feature does not exist");
	}
}
?>
