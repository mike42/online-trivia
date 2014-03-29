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
		if(!$round = round_model::get($question -> get_round_id())) {
			return array('error' => 'question is invalid because related round does not exist', 'code' => '400');
		}
		if(!session::is_game_master($round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}

		/* Find suitable sortkey */
		$question_sortkey = $question -> get_question_sortkey();
		if($question_sortkey == 0) {
			do {
				$question_sortkey++;
			} while($test = question_model::get_by_question_sort($question -> get_round_id(), $question_sortkey));
			$question -> set_question_sortkey($question_sortkey);
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
		if(!session::is_game_member($question -> round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}
		// $question -> populate_list_answer();
		return $question -> to_array_filtered($role);
	}

	public static function status($question_id = null) {
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
		if(!session::is_game_master($question -> round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}
		
		/* Figure out who has answered */
		$question -> round -> game -> populate_list_team();
		$answered = array();
		foreach($question -> round -> game -> list_team as $team) {
			$answered[$team -> team_id] = 0;
		}
		
		$question -> populate_list_answer();
		foreach($question -> list_answer as $answer) {
			$answered[$answer -> team_id] = 1;
		}
		$ret = $question -> to_array_filtered($role);
		$ret['status'] = $answered;
		return $ret;
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
		if(!session::is_game_master($question -> round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}

		/* Find fields to update */
		$update = false;
		$received = json_decode(file_get_contents('php://input'), true);
		if(isset($received['question_text']) && in_array('question_text', core::$permission[$role]['question']['update'])) {
			$question -> set_question_text($received['question_text']);
		}
		if(isset($received['question_sortkey']) && in_array('question_sortkey', core::$permission[$role]['question']['update'])) {
			$temp = "0";
			$old = $question -> get_question_sortkey();
			if($received['question_sortkey'] == $old) {
				$new = $old;
			} else if($received['question_sortkey'] == "down") {
				$new = $old + 1;
			} else if($received['question_sortkey'] == "up") {
				$new = $old - 1;
			} else {
				return array('error' => 'Can only move questions up or down', 'code' => '400');
			}

			if($new != $old) {
				/* Perform swap */
				if(!$replace = question_model::get_by_question_sort($question -> get_round_id(), $new)) {
					print_r($question);
					return array('error' => 'Cannot move the question any further', 'code' => '400');
				}
				$replace -> set_question_sortkey($temp);
				$replace -> update();
				$question -> set_question_sortkey($new);
				$question -> update();
				$replace -> set_question_sortkey($old);
				$replace -> update();
			}
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
		if(!session::is_game_master($question -> round -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games', 'code' => '403');
		}

		/* Check for child rows */
		$question -> populate_list_answer(0, 1);
		if(count($question -> list_answer) > 0) {
			return array('error' => 'Cannot delete question because of a related answer entry', 'code' => '400');
		}

		/* Delete it */
		try {
			$round = $question -> round;
			$question -> delete();
			self::correct_sortkeys($round);
			
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}

	public static function list_all($page = 1, $itemspp = 20) {
		/* Check permission */
		$role = session::getRole();
		if(true || !isset(core::$permission[$role]['question']['read']) || count(core::$permission[$role]['question']['read']) == 0) {
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
	
	private static function correct_sortkeys(round_model $round) {
		$round -> populate_list_question();
		foreach($round -> list_question as $id => $question) {
			if($id + 1 < $question -> get_question_sortkey()) {
				$question -> set_question_sortkey($id + 1);
				$question -> update();
			}
		}
	}
}
?>