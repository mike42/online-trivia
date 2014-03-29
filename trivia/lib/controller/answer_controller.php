<?php
class answer_controller {
	public static function init() {
		core::loadClass("session");
		core::loadClass("answer_model");
	}

	public static function create() {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['answer']['create']) || core::$permission[$role]['answer']['create'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Find fields to insert */
		$fields = array('question_id', 'team_id', 'answer_text', 'answer_is_correct', 'answer_time');
		$init = array();
		$received = json_decode(file_get_contents('php://input'), true, 2);
		foreach($fields as $field) {
			if(isset($received[$field])) {
				$init["answer.$field"] = $received[$field];
			}
		}
		$answer = new answer_model($init);

		/* Check parent tables */
		if(!question_model::get($answer -> get_question_id())) {
			return array('error' => 'answer is invalid because related question does not exist', 'code' => '400');
		}
		if(!team_model::get($answer -> get_team_id())) {
			return array('error' => 'answer is invalid because related team does not exist', 'code' => '400');
		}
		if(!session::is_team_member($answer -> get_team_id())) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}

		/* Insert new row */
		try {
			$answer -> insert();
			return $answer -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($question_id = null,$team_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['answer']['read']) || count(core::$permission[$role]['answer']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load answer */
		$answer = answer_model::get($question_id,$team_id);
		if(!$answer) {
			return array('error' => 'answer not found', 'code' => '404');
		}
		if(!session::is_game_master($answer -> team -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}
		return $answer -> to_array_filtered($role);
	}

	public static function update($question_id = null,$team_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['answer']['update']) || count(core::$permission[$role]['answer']['update']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load answer */
		$answer = answer_model::get($question_id,$team_id);
		if(!$answer) {
			return array('error' => 'answer not found', 'code' => '404');
		}
		if(!session::is_game_master($answer -> team -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}

		/* Find fields to update */
		$update = false;
		$received = json_decode(file_get_contents('php://input'), true);
		if(isset($received['answer_text']) && in_array('answer_text', core::$permission[$role]['answer']['update'])) {
			$answer -> set_answer_text($received['answer_text']);
		}
		if(isset($received['answer_is_correct']) && in_array('answer_is_correct', core::$permission[$role]['answer']['update'])) {
			$answer -> set_answer_is_correct($received['answer_is_correct']);
		}
		if(isset($received['answer_time']) && in_array('answer_time', core::$permission[$role]['answer']['update'])) {
			$answer -> set_answer_time($received['answer_time']);
		}

		/* Check parent tables */
		if(!question_model::get($answer -> get_question_id())) {
			return array('error' => 'answer is invalid because related question does not exist', 'code' => '400');
		}
		if(!team_model::get($answer -> get_team_id())) {
			return array('error' => 'answer is invalid because related team does not exist', 'code' => '400');
		}

		/* Update the row */
		try {
			$answer -> update();
			return $answer -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to update row', 'code' => '500');
		}
	}

	public static function delete($question_id = null,$team_id = null) {
		/* Check permission */
		$role = session::getRole();
		if(!isset(core::$permission[$role]['answer']['delete']) || core::$permission[$role]['answer']['delete'] != true) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}

		/* Load answer */
		$answer = answer_model::get($question_id,$team_id);
		if(!$answer) {
			return array('error' => 'answer not found', 'code' => '404');
		}
		if(!session::is_game_master($answer -> team -> get_game_id())) {
			return array('error' => 'Your permissions do not extend to other games.', 'code' => '403');
		}

		/* Delete it */
		try {
			$answer -> delete();
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}

	public static function list_all($page = 1, $itemspp = 20) {
		/* Check permission */
		$role = session::getRole();
		if(true || !isset(core::$permission[$role]['answer']['read']) || count(core::$permission[$role]['answer']['read']) == 0) {
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
			$answer_list = answer_model::list_all($start, $limit);
			$ret = array();
			foreach($answer_list as $answer) {
				$ret[] = $answer -> to_array_filtered($role);
			}
			return $ret;
		} catch(Exception $e) {
			return array('error' => 'Failed to list', 'code' => '500');
		}
	}
	
	public static function list_by_question_id($question_id, $page = 0, $itemspp = 0) {
		/* Check permission */
		$role = session::getRole();
		if(true || !isset(core::$permission[$role]['answer']['read']) || count(core::$permission[$role]['answer']['read']) == 0) {
			return array('error' => 'You do not have permission to do that', 'code' => '403');
		}
		if(!$question = question_model::get($question_id)) {
			return array('error' => 'Question not found.', 'code' => '404');
		}
		if(!session::is_game_master($question -> round -> get_round_id())) {
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
			$answer_list = answer_model::list_all($start, $limit);
			$ret = array();
			foreach($answer_list as $answer) {
				$ret[] = $answer -> to_array_filtered($role);
			}
			return $ret;
		} catch(Exception $e) {
			return array('error' => 'Failed to list', 'code' => '500');
		}
	}
}
?>