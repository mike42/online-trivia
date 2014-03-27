<?php
class question_controller {
	public static function init() {
		core::loadClass("session");
		core::loadClass("question_model");
	}

	public static function create() {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['question']['create']) || core::$permission[$role]['question']['create'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Find fields to insert */
		$fields = array('question_id', 'round_id', 'question_text', 'question_sortkey', 'question_state', 'question_answer');
		$init = array();
		$received = json_decode(file_get_contents('php://input'), true, 2);
		foreach($fields as $field) {
			if(isset($received[$field])) {
				$init["question.$field"] = $received[$field];
			}
		}
			$question = new question_model($init);

		/* Check parent tables */
		if(!round_model::get($question -> get_round_id())) {
			return array('error' => 'question is invalid because related round does not exist', 'code' => '400');
		}

		/* Insert new row */
		try {
			$question -> insert();
			return $question -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($question_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['question']['read']) || count(core::$permission[$role]['question']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load question */
		$question = question_model::get($question_id);
		if(!$question) {
			return array('error' => 'question not found', 'code' => '404');
		}
		// $question -> populate_list_answer();
		return $question -> to_array_filtered($role);
	}

	public static function update($question_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['question']['update']) || count(core::$permission[$role]['question']['update']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load question */
		$question = question_model::get($question_id);
		if(!$question) {
			return array('error' => 'question not found', 'code' => '404');
		}

		/* Find fields to update */
		$update = false;
		$received = json_decode(file_get_contents('php://input'), true);
		if(isset($received['round_id']) && in_array('round_id', core::$permission[$role]['question']['update'])) {
			$question -> set_round_id($received['round_id']);
		}
		if(isset($received['question_text']) && in_array('question_text', core::$permission[$role]['question']['update'])) {
			$question -> set_question_text($received['question_text']);
		}
		if(isset($received['question_sortkey']) && in_array('question_sortkey', core::$permission[$role]['question']['update'])) {
			$question -> set_question_sortkey($received['question_sortkey']);
		}
		if(isset($received['question_state']) && in_array('question_state', core::$permission[$role]['question']['update'])) {
			$question -> set_question_state($received['question_state']);
		}
		if(isset($received['question_answer']) && in_array('question_answer', core::$permission[$role]['question']['update'])) {
			$question -> set_question_answer($received['question_answer']);
		}

		/* Check parent tables */
		if(!round_model::get($question -> get_round_id())) {
			return array('error' => 'question is invalid because related round does not exist', 'code' => '400');
		}

		/* Update the row */
		try {
			$question -> update();
			return $question -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to update row', 'code' => '500');
		}
	}

	public static function delete($question_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['question']['delete']) || core::$permission[$role]['question']['delete'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load question */
		$question = question_model::get($question_id);
		if(!$question) {
			return array('error' => 'question not found', 'code' => '404');
		}

		/* Check for child rows */
		$question -> populate_list_answer(0, 1);
		if(count($question -> list_answer) > 0) {
			return array('error' => 'Cannot delete question because of a related answer entry', 'code' => '400');
		}

		/* Delete it */
		try {
			$question -> delete();
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}

	public static function list_all($page = 1, $itemspp = 20) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['question']['read']) || count(core::$permission[$role]['question']['read']) == 0) {
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
			$question_list = question_model::list_all($start, $limit);
			$ret = array();
			foreach($question_list as $question) {
				$ret[] = $question -> to_array_filtered($role);
			}
			return $ret;
		} catch(Exception $e) {
			return array('error' => 'Failed to list', 'code' => '500');
		}
	}
}
?>