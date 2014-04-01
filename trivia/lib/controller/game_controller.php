<?php
class game_controller {
	public static function init() {
		core::loadClass("session");
		core::loadClass("game_model");
	}

	public static function create() {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['game']['create']) || core::$permission[$role]['game']['create'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Find fields to insert */
		$fields = array('game_id', 'game_name', 'game_state', 'game_code');
		$init = array();
		$received = json_decode(file_get_contents('php://input'), true, 2);
		foreach($fields as $field) {
			if(isset($received[$field])) {
				$init["game.$field"] = $received[$field];
			}
		}
			$game = new game_model($init);

		/* Insert new row */
		try {
			$game -> insert();
			return $game -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($game_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['game']['read']) || count(core::$permission[$role]['game']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}
		
		if(!session::is_game_master($game_id)) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}

		/* Load game */
		$game = game_model::get($game_id);
		if(!$game) {
			return array('error' => 'game not found', 'code' => '404');
		}
		$game -> populate_list_team();
		$game -> populate_list_round();
		$game -> populate_list_person();
		return $game -> to_array_filtered($role);
	}
	
	public static function leaderboard($game_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['game']['read']) || count(core::$permission[$role]['game']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}
	
		if(!session::is_game_master($game_id)) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}
	
		/* Load game */
		$game = game_model::get($game_id);
		return $game -> getLeaderBoard();
	}

	public static function update($game_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['game']['update']) || count(core::$permission[$role]['game']['update']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		if(!session::is_game_master($game_id)) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}
		
		/* Load game */
		$game = game_model::get($game_id);
		if(!$game) {
			return array('error' => 'game not found', 'code' => '404');
		}

		/* Find fields to update */
		$update = false;
		$received = json_decode(file_get_contents('php://input'), true);
		if(isset($received['game_name']) && in_array('game_name', core::$permission[$role]['game']['update'])) {
			$game -> set_game_name($received['game_name']);
		}
		if(isset($received['game_state']) && in_array('game_state', core::$permission[$role]['game']['update'])) {
			$game -> set_game_state($received['game_state']);
		}
		if(isset($received['game_code']) && in_array('game_code', core::$permission[$role]['game']['update'])) {
			$game -> set_game_code($received['game_code']);
		}

		/* Update the row */
		try {
			$game -> update();
			return $game -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to update row', 'code' => '500');
		}
	}

	public static function delete($game_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['game']['delete']) || core::$permission[$role]['game']['delete'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		if(!session::is_game_master($game_id)) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}
		
		/* Load game */
		$game = game_model::get($game_id);
		if(!$game) {
			return array('error' => 'game not found', 'code' => '404');
		}

		/* Check for child rows */
		$game -> populate_list_team(0, 1);
		if(count($game -> list_team) > 0) {
			return array('error' => 'Cannot delete game because of a related team entry', 'code' => '400');
		}
		$game -> populate_list_round(0, 1);
		if(count($game -> list_round) > 0) {
			return array('error' => 'Cannot delete game because of a related round entry', 'code' => '400');
		}
		$game -> populate_list_person(0, 1);
		if(count($game -> list_person) > 0) {
			return array('error' => 'Cannot delete game because of a related person entry', 'code' => '400');
		}

		/* Delete it */
		try {
			$game -> delete();
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}

	public static function list_all($page = 1, $itemspp = 20) {
		/* Check permission */
		$role = session::getRole();
		if(true || !isset(core::$permission[$role]['game']['read']) || count(core::$permission[$role]['game']['read']) == 0) {
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
			$game_list = game_model::list_all($start, $limit);
			$ret = array();
			foreach($game_list as $game) {
				$ret[] = $game -> to_array_filtered($role);
			}
			return $ret;
		} catch(Exception $e) {
			return array('error' => 'Failed to list', 'code' => '500');
		}
	}
}
?>