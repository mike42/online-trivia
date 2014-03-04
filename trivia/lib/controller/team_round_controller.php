<?php
class team_round_controller {
	public static function init() {
		core::loadClass("session");
		core::loadClass("team_round_model");
	}

	public static function create() {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['team_round']['create']) || core::$permission[$role]['team_round']['create'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Find fields to insert */
		$fields = array('round_round_id', 'team_team_id', 'bonus_points');
		$init = array();
		$received = json_decode(file_get_contents('php://input'), true, 2);
		foreach($fields as $field) {
			if(isset($received[$field])) {
				$init["team_round.$field"] = $received[$field];
			}
		}
			$team_round = new team_round_model($init);

		/* Check parent tables */
		if(!round_model::get($team_round -> get_round_round_id())) {
			return array('error' => 'team_round is invalid because related round does not exist', 'code' => '400');
		}
		if(!team_model::get($team_round -> get_team_team_id())) {
			return array('error' => 'team_round is invalid because related team does not exist', 'code' => '400');
		}

		/* Insert new row */
		try {
			$team_round -> insert();
			return $team_round -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($round_round_id,$team_team_id) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['team_round']['read']) || count(core::$permission[$role]['team_round']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load team_round */
		$team_round = team_round_model::get($round_round_id,$team_team_id);
		if(!$team_round) {
			return array('error' => 'team_round not found', 'code' => '404');
		}
		return $team_round -> to_array_filtered($role);
	}

	public static function update($round_round_id,$team_team_id) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['team_round']['update']) || count(core::$permission[$role]['team_round']['update']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load team_round */
		$team_round = team_round_model::get($round_round_id,$team_team_id);
		if(!$team_round) {
			return array('error' => 'team_round not found', 'code' => '404');
		}

		/* Find fields to update */
		$update = false;
		$received = json_decode(file_get_contents('php://input'), true);
		if(isset($received['bonus_points']) && in_array('bonus_points', core::$permission[$role]['team_round']['update'])) {
			$team_round -> set_bonus_points($received['bonus_points']);
		}

		/* Check parent tables */
		if(!round_model::get($team_round -> get_round_round_id())) {
			return array('error' => 'team_round is invalid because related round does not exist', 'code' => '400');
		}
		if(!team_model::get($team_round -> get_team_team_id())) {
			return array('error' => 'team_round is invalid because related team does not exist', 'code' => '400');
		}

		/* Update the row */
		try {
			$team_round -> update();
			return $team_round -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to update row', 'code' => '500');
		}
	}

	public static function delete($round_round_id,$team_team_id) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['team_round']['delete']) || core::$permission[$role]['team_round']['delete'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load team_round */
		$team_round = team_round_model::get($round_round_id,$team_team_id);
		if(!$team_round) {
			return array('error' => 'team_round not found', 'code' => '404');
		}


		/* Delete it */
		try {
			$team_round -> delete();
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}
}
?>