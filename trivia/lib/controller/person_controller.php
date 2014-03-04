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

		/* Insert new row */
		try {
			$person -> insert();
			return $person -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($person_id) {
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
		// $person -> populate_list_person_table();
		return $person -> to_array_filtered($role);
	}

	public static function update($person_id) {
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

	public static function delete($person_id) {
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
}
?>