<?php
class round_controller {
	public static function init() {
		core::loadClass("session");
		core::loadClass("round_model");
	}

	public static function create() {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['round']['create']) || core::$permission[$role]['round']['create'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Find fields to insert */
		$fields = array('round_id', 'name', 'game_id', 'round_sortkey', 'round_state');
		$init = array();
		$received = json_decode(file_get_contents('php://input'), true, 2);
		foreach($fields as $field) {
			if(isset($received[$field])) {
				$init["round.$field"] = $received[$field];
			}
		}
		$round = new round_model($init);
		
		/* Find suitable sortkey */
		$round_sortkey = $round -> get_round_sortkey();
		if($round_sortkey == 0) {
			do {
				$round_sortkey++;
			} while($test = round_model::get_by_round_sort($round -> get_game_id(), $round_sortkey));
			$round -> set_round_sortkey($round_sortkey);
		}
		
		/* Check parent tables */
		if(!game_model::get($round -> get_game_id())) {
			return array('error' => 'round is invalid because related game does not exist', 'code' => '400');
		}
		if(!session::is_game_master($round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}

		/* Insert new row */
		try {
			$round -> insert();
			return $round -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($round_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['round']['read']) || count(core::$permission[$role]['round']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load round */
		$round = round_model::get($round_id);
		if(!$round) {
			return array('error' => 'round not found', 'code' => '404');
		}
		if(!session::is_game_member($round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}
		$round -> populate_list_question();
		return $round -> to_array_filtered($role);
	}
	
	public static function team_counts($round_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['round']['read']) || count(core::$permission[$role]['round']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}
		
		/* Load round */
		$round = round_model::get($round_id);
		if(!$round) {
			return array('error' => 'round not found', 'code' => '404');
		}
		if(!session::is_game_master($round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}
		return $round -> get_team_counts();
	}

	public static function detailed($round_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['round']['read']) || count(core::$permission[$role]['round']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}
	
		/* Load round */
		$round = round_model::get($round_id);
		if(!$round) {
			return array('error' => 'round not found', 'code' => '404');
		}
		if(!session::is_game_master($round -> get_game_id())) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}
		
		/* Load everything and discard answers which are already marked */
		$round -> populate_list_question();
		foreach($round -> list_question as $key => $lq) {
			$round -> list_question[$key] -> populate_list_answer();
			foreach($round -> list_question[$key] -> list_answer as $k2 => $answer) {
				if($answer -> get_answer_is_correct() != "0") {
					unset($round -> list_question[$key] -> list_answer[$k2]);
				}
			}
		}
		
		$round -> list_team_round = team_round_model::list_by_round_id($round_id);
		return $round -> to_array_filtered($role);
	}
	
	public static function update($round_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['round']['update']) || count(core::$permission[$role]['round']['update']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load round */
		$round = round_model::get($round_id);
		if(!$round) {
			return array('error' => 'round not found', 'code' => '404');
		}
		if(!session::is_game_master($round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}

		/* Find fields to update */
		$update = false;
		$received = json_decode(file_get_contents('php://input'), true);
		if(isset($received['name']) && in_array('name', core::$permission[$role]['round']['update'])) {
			$round -> set_name($received['name']);
		}
		if(isset($received['round_sortkey']) && in_array('round_sortkey', core::$permission[$role]['round']['update'])) {
			$temp = "0";
			$old = $round -> get_round_sortkey();
			if($received['round_sortkey'] == $old) {
				$new = $old;
			} else if($received['round_sortkey'] == "down") {
				$new = $old + 1;
			} else if($received['round_sortkey'] == "up") {
				$new = $old - 1;
			} else {
				return array('error' => 'Can only move rounds up or down', 'code' => '400');
			}
			
			if($new != $old) {
				/* Perform swap */
				if(!$replace = round_model::get_by_round_sort($round -> get_game_id(), $new)) {
					return array('error' => 'Cannot move the round any further', 'code' => '400');
				}
				$replace -> set_round_sortkey($temp);
				$replace -> update();
				$round -> set_round_sortkey($new);
				$round -> update();
				$replace -> set_round_sortkey($old);
				$replace -> update();
			}
		}
		if(isset($received['round_state']) && in_array('round_state', core::$permission[$role]['round']['update'])) {
			$round -> set_round_state($received['round_state']);
		}

		/* Check parent tables */
		if(!game_model::get($round -> get_game_id())) {
			return array('error' => 'round is invalid because related game does not exist', 'code' => '400');
		}

		/* Update the row */
		try {
			$round -> update();
			return $round -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to update row', 'code' => '500');
		}
	}

	public static function delete($round_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['round']['delete']) || core::$permission[$role]['round']['delete'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load round */
		$round = round_model::get($round_id);
		if(!$round) {
			return array('error' => 'round not found', 'code' => '404');
		}
		if(!session::is_game_master($round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}
		
		/* Check for child rows */
		$round -> populate_list_question(0, 1);
		if(count($round -> list_question) > 0) {
			return array('error' => 'Cannot delete round because of a related question entry', 'code' => '400');
		}

		/* Delete it */
		try {
			$game = $round -> game;
			$round -> delete();
			self::correct_sortkeys($game);
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}

	public static function list_all($page = 1, $itemspp = 20) {
		/* Check permission */
		$role = session::getRole();
		if(true || !isset(core::$permission[$role]['round']['read']) || count(core::$permission[$role]['round']['read']) == 0) {
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
			$round_list = round_model::list_all($start, $limit);
			$ret = array();
			foreach($round_list as $round) {
				$ret[] = $round -> to_array_filtered($role);
			}
			return $ret;
		} catch(Exception $e) {
			return array('error' => 'Failed to list', 'code' => '500');
		}
	}
	
	public static function list_by_game_id($game_id, $page = 0, $itemspp = 0) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['round']['read']) || count(core::$permission[$role]['round']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}
		if(!session::is_game_member($game_id)) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
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
			$round_list = round_model::list_by_game_id_fk2($game_id, $start, $limit);
			$ret = array();
			foreach($round_list as $round) {
				$ret[] = $round -> to_array_filtered($role);
			}
			return $ret;
		} catch(Exception $e) {
			return array('error' => 'Failed to list', 'code' => '500');
		}
	}
	
	private static function correct_sortkeys(game_model $game) {
		$game -> populate_list_round();
		foreach($game -> list_round as $id => $round) {
			if($id + 1 < $round -> get_round_sortkey()) {
				$round -> set_round_sortkey($id + 1);
				$round -> update();
			}	
		}
	}
}
?>