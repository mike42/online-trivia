<?php
class team_controller {
	public static function init() {
		core::loadClass("session");
		core::loadClass("team_model");
	}

	public static function create() {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['team']['create']) || core::$permission[$role]['team']['create'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Find fields to insert */
		$fields = array('team_id', 'team_code', 'game_id', 'team_name');
		$init = array();
		$received = json_decode(file_get_contents('php://input'), true, 2);
		foreach($fields as $field) {
			if(isset($received[$field])) {
				$init["team.$field"] = $received[$field];
			}
		}
			$team = new team_model($init);

		/* Check parent tables */
		if(!game_model::get($team -> get_game_id())) {
			return array('error' => 'team is invalid because related game does not exist', 'code' => '400');
		}

		/* Insert new row */
		try {
			$team -> insert();
			return $team -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($team_id) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['team']['read']) || count(core::$permission[$role]['team']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load team */
		$team = team_model::get($team_id);
		if(!$team) {
			return array('error' => 'team not found', 'code' => '404');
		}
		// $team -> populate_list_answer();
		// $team -> populate_list_person_table();
		// $team -> populate_list_team_round();
		return $team -> to_array_filtered($role);
	}

	public static function update($team_id) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['team']['update']) || count(core::$permission[$role]['team']['update']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load team */
		$team = team_model::get($team_id);
		if(!$team) {
			return array('error' => 'team not found', 'code' => '404');
		}

		/* Find fields to update */
		$update = false;
		$received = json_decode(file_get_contents('php://input'), true);
		if(isset($received['team_code']) && in_array('team_code', core::$permission[$role]['team']['update'])) {
			$team -> set_team_code($received['team_code']);
		}
		if(isset($received['game_id']) && in_array('game_id', core::$permission[$role]['team']['update'])) {
			$team -> set_game_id($received['game_id']);
		}
		if(isset($received['team_name']) && in_array('team_name', core::$permission[$role]['team']['update'])) {
			$team -> set_team_name($received['team_name']);
		}

		/* Check parent tables */
		if(!game_model::get($team -> get_game_id())) {
			return array('error' => 'team is invalid because related game does not exist', 'code' => '400');
		}

		/* Update the row */
		try {
			$team -> update();
			return $team -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to update row', 'code' => '500');
		}
	}

	public static function delete($team_id) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['team']['delete']) || core::$permission[$role]['team']['delete'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load team */
		$team = team_model::get($team_id);
		if(!$team) {
			return array('error' => 'team not found', 'code' => '404');
		}

		/* Check for child rows */
		$team -> populate_list_answer(0, 1);
		if(count($team -> list_answer) > 0) {
			return array('error' => 'Cannot delete team because of a related answer entry', 'code' => '400');
		}
		$team -> populate_list_person_table(0, 1);
		if(count($team -> list_person_table) > 0) {
			return array('error' => 'Cannot delete team because of a related person_table entry', 'code' => '400');
		}
		$team -> populate_list_team_round(0, 1);
		if(count($team -> list_team_round) > 0) {
			return array('error' => 'Cannot delete team because of a related team_round entry', 'code' => '400');
		}

		/* Delete it */
		try {
			$team -> delete();
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}
}
?>