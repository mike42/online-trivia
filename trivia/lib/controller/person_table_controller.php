<?php
class person_table_controller {
	public static function init() {
		core::loadClass("session");
		core::loadClass("person_table_model");
	}

	public static function create() {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['person_table']['create']) || core::$permission[$role]['person_table']['create'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Find fields to insert */
		$fields = array('round_id', 'person_id', 'team_id');
		$init = array();
		$received = json_decode(file_get_contents('php://input'), true, 2);
		foreach($fields as $field) {
			if(isset($received[$field])) {
				$init["person_table.$field"] = $received[$field];
			}
		}
			$person_table = new person_table_model($init);

		/* Check parent tables */
		if(!$round = round_model::get($person_table -> get_round_id())) {
			return array('error' => 'person_table is invalid because related round does not exist', 'code' => '400');
		}
		if(!$person = person_model::get($person_table -> get_person_id())) {
			return array('error' => 'person_table is invalid because related person does not exist', 'code' => '400');
		}
		if(!$team = team_model::get($person_table -> get_team_id())) {
			return array('error' => 'person_table is invalid because related team does not exist', 'code' => '400');
		}
		if($round -> get_game_id() != $person -> get_game_id() || $person -> get_game_id() != $team -> get_game_id()) {
			return array('error' => 'Cannot mix members of different games', 'code' => '403');
		}
		if(!session::is_game_member($round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}

		/* Insert new row */
		try {
			$person_table -> insert();
			return $person_table -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($round_id = null,$person_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['person_table']['read']) || count(core::$permission[$role]['person_table']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load person_table */
		$person_table = person_table_model::get($round_id,$person_id);
		if(!$person_table) {
			return array('error' => 'person_table not found', 'code' => '404');
		}
		if(!session::is_game_member($round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}
		
		return $person_table -> to_array_filtered($role);
	}

	public static function update($round_id = null,$person_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['person_table']['update']) || count(core::$permission[$role]['person_table']['update']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load person_table */
		$person_table = person_table_model::get($round_id,$person_id);
		if(!$person_table) {
			return array('error' => 'person_table not found', 'code' => '404');
		}

		/* Find fields to update */
		$update = false;
		$received = json_decode(file_get_contents('php://input'), true);
		if(isset($received['team_id']) && in_array('team_id', core::$permission[$role]['person_table']['update'])) {
			$person_table -> set_team_id($received['team_id']);
		}

		/* Check parent tables */
		if(!team_model::get($person_table -> get_team_id())) {
			return array('error' => 'person_table is invalid because related team does not exist', 'code' => '400');
		}
		if(!session::is_team_member($team -> get_team_id()) || !session::is_game_member($person_table -> round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}

		/* Update the row */
		try {
			$person_table -> update();
			return $person_table -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to update row', 'code' => '500');
		}
	}

	public static function delete($round_id = null,$person_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['person_table']['delete']) || core::$permission[$role]['person_table']['delete'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load person_table */
		$person_table = person_table_model::get($round_id,$person_id);
		if(!$person_table) {
			return array('error' => 'person_table not found', 'code' => '404');
		}
		if(!session::is_team_member($person_table -> team -> get_team_id()) || !session::is_game_member($person_table -> round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}

		/* Delete it */
		try {
			$person_table -> delete();
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}

	public static function list_all($page = 1, $itemspp = 20) {
		/* Check permission */
		$role = session::getRole();
		if(true || !isset(core::$permission[$role]['person_table']['read']) || count(core::$permission[$role]['person_table']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}
		if((int)$page < 1 || (int)$itemspp < 1) {
			$start = 0;
			$limit = -1;
		} else {
			$start = ($page - 1) * $itemspp;
			$limit = $itemspp;
		}

		/* Retrieve and filter rows */
		try {
			$person_table_list = person_table_model::list_all($start, $limit);
			$ret = array();
			foreach($person_table_list as $person_table) {
				$ret[] = $person_table -> to_array_filtered($role);
			}
			return $ret;
		} catch(Exception $e) {
			return array('error' => 'Failed to list', 'code' => '500');
		}
	}
}
?>