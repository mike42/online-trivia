<?php
class game_controller {
	public static function init() {
		core::loadClass("game_model");
		core::loadClass("round_model");
		core::loadClass("team_model");
		core::loadClass("person_model");
		core::loadClass("question_model");
	}

	public static function create() {
		return array('error' => "Feature does not exist");
	}

	public static function read($game_code) {
		$game = game_model::get_by_game_code($game_code);
		if($game === false) {
			return array('error' => "Game does not exist");
		}
		$game -> populate_list_team();
		$game -> populate_list_round();
		$game -> populate_list_person();
		foreach($game -> list_round as $id => $round) {
			$game -> list_round[$id]-> populate_list_question();
		}
		return array('game' => $game);
	}
	
	public static function zen($game_code) {
		return self::read($game_code);
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
