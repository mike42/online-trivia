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

		/* Insert new row */
		try {
			$answer -> insert();
			return $answer -> to_array_filtered($role);
		} catch(Exception $e) {
			return array('error' => 'Failed to add to database', 'code' => '500');
		}
	}

	public static function read($question_id,$team_id) {
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
		return $answer -> to_array_filtered($role);
	}

	public static function update($question_id,$team_id) {
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

	public static function delete($question_id,$team_id) {
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


		/* Delete it */
		try {
			$answer -> delete();
			return array('success' => 'yes');
		} catch(Exception $e) {
			return array('error' => 'Failed to delete', 'code' => '500');
		}
	}
}
?>