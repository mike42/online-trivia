<?php
class person_controller {
	public static function init() {
		core::loadClass("session");
		core::loadClass("person_model");
	}

	public static function create() {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['person']['create']) || core::$permission[$role]['person']['create'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Find fields to insert */
		$fields = array('person_id', 'person_name', 'game_id');
		$init = array();
		$received = json_decode(file_get_contents('php://input'), true, 2);
		foreach($fields as $field) {
			if(isset($received[$field])) {
				$init["person.$field"] = $received[$field];
			}
		}
		$person = new person_model($init);

		/* Check parent tables */
		if(!game_model::get($person -> get_game_id())) {
			return array('error' => 'person is invalid because related game does not exist', 'code' => '400');
		}
		if(!session::is_game_master($person -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}
		
		/* Insert new row */
		try {
			/* If a list of names is given, insert everything and just return the last person */
			$p2 = $person;
			$names = explode("\n", $person -> get_person_name());
			foreach($names as $name) {
				if(trim($name) != "") {
					$p2 = new person_model();
					try {
						$p2 -> set_game_id($person -> get_game_id());
						$p2 -> set_person_name(trim($name));
						$p2 -> insert();
					} catch(Exception $e) {
						// Ignore anything invalid
					}
				}
			}
			return $p2 -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($person_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['person']['read']) || count(core::$permission[$role]['person']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load person */
		$person = person_model::get($person_id);
		if(!$person) {
			return array('error' => 'person not found', 'code' => '404');
		}
		if(!session::is_game_member($person -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}
		
		// $person -> populate_list_person_table();
		return $person -> to_array_filtered($role);
	}

	public static function update($person_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['person']['update']) || count(core::$permission[$role]['person']['update']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load person */
		$person = person_model::get($person_id);
		if(!$person) {
			return array('error' => 'person not found', 'code' => '404');
		}
		if(!session::is_game_master($person -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}

		/* Find fields to update */
		$update = false;
		$received = json_decode(file_get_contents('php://input'), true);
		if(isset($received['person_name']) && in_array('person_name', core::$permission[$role]['person']['update'])) {
			$person -> set_person_name($received['person_name']);
		}
		if(isset($received['game_id']) && in_array('game_id', core::$permission[$role]['person']['update'])) {
			$person -> set_game_id($received['game_id']);
		}

		/* Check parent tables */
		if(!game_model::get($person -> get_game_id())) {
			return array('error' => 'person is invalid because related game does not exist', 'code' => '400');
		}

		/* Update the row */
		try {
			$person -> update();
			return $person -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to update row', 'code' => '500');
		}
	}

	public static function delete($person_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['person']['delete']) || core::$permission[$role]['person']['delete'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load person */
		$person = person_model::get($person_id);
		if(!$person) {
			return array('error' => 'person not found', 'code' => '404');
		}
		if(!session::is_game_master($person -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}

		/* Check for child rows */
		$person -> populate_list_person_table(0, 1);
		if(count($person -> list_person_table) > 0) {
			return array('error' => 'Cannot delete person because of a related person_table entry', 'code' => '400');
		}

		/* Delete it */
		try {
			$person -> delete();
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}

	public static function list_all($page = 1, $itemspp = 20) {
		/* Check permission */
		$role = session::getRole();
		if(true || !isset(core::$permission[$role]['person']['read']) || count(core::$permission[$role]['person']['read']) == 0) {
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
			$person_list = person_model::list_all($start, $limit);
			$ret = array();
			foreach($person_list as $person) {
				$ret[] = $person -> to_array_filtered($role);
			}
			return $ret;
		} catch(Exception $e) {
			return array('error' => 'Failed to list', 'code' => '500');
		}
	}
	
	public static function list_by_game_id($game_id, $page = 0, $itemspp = 0) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['person']['read']) || count(core::$permission[$role]['person']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}
		
		if(!session::is_game_master($game_id)) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
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
			$person_list = person_model::list_by_game_id($game_id, $start, $limit);
			$ret = array();
			foreach($person_list as $person) {
				$ret[] = $person -> to_array_filtered($role);
			}
			return $ret;
		} catch(Exception $e) {
			return array('error' => 'Failed to list', 'code' => '500');
		}
	}
}
?>