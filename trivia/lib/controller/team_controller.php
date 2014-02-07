<?php
class team_controller {
	public static function init() {
		core::loadClass("team_model");
		core::loadClass("game_model");
		core::loadClass("round_model");
		core::loadClass("person_model");
		core::loadClass("question_model");
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
		foreach($team -> game -> list_round as $round) {
			$round -> populate_list_question();
		}
		$team -> game -> populate_list_person();
		return array('team' => $team);
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
