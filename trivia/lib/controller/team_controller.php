<?php
class team_controller {
	public static function init() {
		core::loadClass("team_model");
		core::loadClass("game_model");
	}

	function create() {
		return array('error' => "Feature does not exist");
	}

	function read($team_code) {
		$team = team_model::get_by_team_code($team_code);
		if($team === false) {
			return array('error' => "Team does not exist");
		}
		return array('team' => $team);
	}

	function update() {
		return array('error' => "Feature does not exist");
	}

	function delete() {
		return array('error' => "Feature does not exist");
	}
	
	function qr($team_code) {
		$data = self::read($team_code);
		$data['url'] = core::constructURL('team', 'read', array($team_code), 'html');
		$data['png'] = core::constructURL('team', 'qr', array($team_code), 'png');
		return $data;
	}
}
?>
