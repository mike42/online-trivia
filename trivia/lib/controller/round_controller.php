<?php
class round_controller {
	public static function init() {
		core::loadClass("game_model");
		core::loadClass("round_model");
		core::loadClass("team_model");
		core::loadClass("person_model");
		core::loadClass("answer_model");
		core::loadClass("question_model");
		core::loadClass("person_table_model");
	}

	public static function create() {
	}

	public static function read() {
	}

	public static function update() {
	}

	public static function delete() {
	}
	
	public static function teams($round_id) {
		$team = array();
		$round = round_model::get($round_id);
		if($round === false) {
			return array('error' => "No such round");
		}
		$round -> game -> populate_list_team();
		foreach($round -> game -> list_team as $t) {
			$team[$t -> get_team_id()] = 0; // Make sure all teams are included
		}
		
		// teams
		$pt = person_table_model::count_by_round($round_id);
		foreach($pt as $t) {
			$team[$t['team_id']] = $t['people'];
		}
		
		return array($team);
	}

	public static function responses($round_id, $question_id) {
		$team = array();
		$round = round_model::get($round_id);
		if($round === false) {
			return array('error' => "No such round");
		}
		$round -> game -> populate_list_team();
		foreach($round -> game -> list_team as $t) {
			$team[$t -> get_team_id()] = 0; // Make sure all teams are included
		}
	
		// responses
		$r = answer_model::list_by_question_id($question_id);
		foreach($r as $t) {
			if(isset($team[$t -> get_team_id()])) {
				$team[$t -> get_team_id()] = '1';
			}
		}
		return array($team);
		
	}
	
	private static function blankTeams($round_id) {
	
	}
}
?>
