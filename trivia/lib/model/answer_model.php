<?php
class answer_model {
	/**
	 * @var int question_id
	 */
	private $question_id;

	/**
	 * @var int team_id
	 */
	private $team_id;

	/**
	 * @var string answer_text
	 */
	private $answer_text;

	/**
	 * @var int answer_is_correct
	 */
	private $answer_is_correct;

	/**
	 * @var string answer_time
	 */
	private $answer_time;

	private $model_variables_changed; // Only variables which have been changed
	private $model_variables_set; // All variables which have been set (initially or with a setter)

	/* Parent tables */
	public $question;
	public $team;

	/* Sort clause to add when listing rows from this table */
	const SORT_CLAUSE = " ORDER BY `answer`.`answer_time` DESC, `answer`.`team_id`";

	/**
	 * Initialise and load related tables
	 */
	public static function init() {
		core::loadClass("database");
		core::loadClass("question_model");
		core::loadClass("team_model");
	}

	/**
	 * Construct new answer from field list
	 * 
	 * @return array
	 */
	public function __construct(array $fields = array()) {
		/* Initialise everything as blank to avoid tripping up the permissions fitlers */
		$this -> question_id = '';
		$this -> team_id = '';
		$this -> answer_text = '';
		$this -> answer_is_correct = '';
		$this -> answer_time = '';

		if(isset($fields['answer.question_id'])) {
			$this -> set_question_id($fields['answer.question_id']);
		}
		if(isset($fields['answer.team_id'])) {
			$this -> set_team_id($fields['answer.team_id']);
		}
		if(isset($fields['answer.answer_text'])) {
			$this -> set_answer_text($fields['answer.answer_text']);
		}
		if(isset($fields['answer.answer_is_correct'])) {
			$this -> set_answer_is_correct($fields['answer.answer_is_correct']);
		}
		if(isset($fields['answer.answer_time'])) {
			$this -> set_answer_time($fields['answer.answer_time']);
		}

		$this -> model_variables_changed = array();
		$this -> question = new question_model($fields);
		$this -> team = new team_model($fields);
	}

	/**
	 * Convert answer to shallow associative array
	 * 
	 * @return array
	 */
	private function to_array() {
		$values = array(
			'question_id' => $this -> question_id,
			'team_id' => $this -> team_id,
			'answer_text' => $this -> answer_text,
			'answer_is_correct' => $this -> answer_is_correct,
			'answer_time' => $this -> answer_time);
		return $values;
	}

	/**
	 * Convert answer to associative array, including only visible fields,
	 * parent tables, and loaded child tables
	 * 
	 * @param string $role The user role to use
	 */
	public function to_array_filtered($role = "anon") {
		if(core::$permission[$role]['answer']['read'] === false) {
			return false;
		}
		$values = array();
		$everything = $this -> to_array();
		foreach(core::$permission[$role]['answer']['read'] as $field) {
			if(!isset($everything[$field])) {
				throw new Exception("Check permissions: '$field' is not a real field in answer");
			}
			$values[$field] = $everything[$field];
		}
		$values['question'] = $this -> question -> to_array_filtered($role);
		$values['team'] = $this -> team -> to_array_filtered($role);
		return $values;
	}

	/**
	 * Convert retrieved database row from numbered to named keys, including table name
	 * 
	 * @param array $row ror retrieved from database
	 * @return array row with indices
	 */
	private static function row_to_assoc(array $row) {
		$values = array(
			"answer.question_id" => $row[0],
			"answer.team_id" => $row[1],
			"answer.answer_text" => $row[2],
			"answer.answer_is_correct" => $row[3],
			"answer.answer_time" => $row[4],
			"question.question_id" => $row[5],
			"question.round_id" => $row[6],
			"question.question_text" => $row[7],
			"question.question_sortkey" => $row[8],
			"question.question_state" => $row[9],
			"question.question_answer" => $row[10],
			"team.team_id" => $row[11],
			"team.team_code" => $row[12],
			"team.game_id" => $row[13],
			"team.team_name" => $row[14],
			"round.round_id" => $row[15],
			"round.name" => $row[16],
			"round.game_id" => $row[17],
			"round.round_sortkey" => $row[18],
			"round.round_state" => $row[19],
			"game.game_id" => $row[20],
			"game.game_name" => $row[21],
			"game.game_state" => $row[22],
			"game.game_code" => $row[23]);
		return $values;
	}

	/**
	 * Get question_id
	 * 
	 * @return int
	 */
	public function get_question_id() {
		if(!isset($this -> model_variables_set['question_id'])) {
			throw new Exception("answer.question_id has not been initialised.");
		}
		return $this -> question_id;
	}

	/**
	 * Set question_id
	 * 
	 * @param int $question_id
	 */
	private function set_question_id($question_id) {
		if(!is_numeric($question_id)) {
			throw new Exception("answer.question_id must be numeric");
		}
		$this -> question_id = $question_id;
		$this -> model_variables_changed['question_id'] = true;
		$this -> model_variables_set['question_id'] = true;
	}

	/**
	 * Get team_id
	 * 
	 * @return int
	 */
	public function get_team_id() {
		if(!isset($this -> model_variables_set['team_id'])) {
			throw new Exception("answer.team_id has not been initialised.");
		}
		return $this -> team_id;
	}

	/**
	 * Set team_id
	 * 
	 * @param int $team_id
	 */
	private function set_team_id($team_id) {
		if(!is_numeric($team_id)) {
			throw new Exception("answer.team_id must be numeric");
		}
		$this -> team_id = $team_id;
		$this -> model_variables_changed['team_id'] = true;
		$this -> model_variables_set['team_id'] = true;
	}

	/**
	 * Get answer_text
	 * 
	 * @return string
	 */
	public function get_answer_text() {
		if(!isset($this -> model_variables_set['answer_text'])) {
			throw new Exception("answer.answer_text has not been initialised.");
		}
		return $this -> answer_text;
	}

	/**
	 * Set answer_text
	 * 
	 * @param string $answer_text
	 */
	public function set_answer_text($answer_text) {
		// TODO: Add TEXT validation to answer.answer_text
		$this -> answer_text = $answer_text;
		$this -> model_variables_changed['answer_text'] = true;
		$this -> model_variables_set['answer_text'] = true;
	}

	/**
	 * Get answer_is_correct
	 * 
	 * @return int
	 */
	public function get_answer_is_correct() {
		if(!isset($this -> model_variables_set['answer_is_correct'])) {
			throw new Exception("answer.answer_is_correct has not been initialised.");
		}
		return $this -> answer_is_correct;
	}

	/**
	 * Set answer_is_correct
	 * 
	 * @param int $answer_is_correct
	 */
	public function set_answer_is_correct($answer_is_correct) {
		if(!is_numeric($answer_is_correct)) {
			throw new Exception("answer.answer_is_correct must be numeric");
		}
		$this -> answer_is_correct = $answer_is_correct;
		$this -> model_variables_changed['answer_is_correct'] = true;
		$this -> model_variables_set['answer_is_correct'] = true;
	}

	/**
	 * Get answer_time
	 * 
	 * @return string
	 */
	public function get_answer_time() {
		if(!isset($this -> model_variables_set['answer_time'])) {
			throw new Exception("answer.answer_time has not been initialised.");
		}
		return $this -> answer_time;
	}

	/**
	 * Set answer_time
	 * 
	 * @param string $answer_time
	 */
	public function set_answer_time($answer_time) {
		// TODO: Add validation to answer.answer_time
		$this -> answer_time = $answer_time;
		$this -> model_variables_changed['answer_time'] = true;
		$this -> model_variables_set['answer_time'] = true;
	}

	/**
	 * Update answer
	 */
	public function update() {
		if(count($this -> model_variables_changed) == 0) {
			throw new Exception("Nothing to update");
		}

		/* Compose list of changed fields */
		$fieldset = array();
		$everything = $this -> to_array();
		$data['question_id'] = $this -> get_question_id();
		$data['team_id'] = $this -> get_team_id();
		foreach($this -> model_variables_changed as $col => $changed) {
			$fieldset[] = "`$col` = :$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);

		/* Execute query */
		$sth = database::$dbh -> prepare("UPDATE `answer` SET $fields WHERE `answer`.`question_id` = :question_id AND `answer`.`team_id` = :team_id");
		$sth -> execute($data);
	}

	/**
	 * Add new answer
	 */
	public function insert() {
		if(count($this -> model_variables_set) == 0) {
			throw new Exception("No fields have been set!");
		}

		/* Compose list of set fields */
		$fieldset = array();
		$data = array();
		$everything = $this -> to_array();
		foreach($this -> model_variables_set as $col => $changed) {
			$fieldset[] = "`$col`";
			$fieldset_colon[] = ":$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);
		$vals = implode(", ", $fieldset_colon);

		/* Execute query */
		$sth = database::$dbh -> prepare("INSERT INTO `answer` ($fields) VALUES ($vals);");
		$sth -> execute($data);
	}

	/**
	 * Delete answer
	 */
	public function delete() {
		$sth = database::$dbh -> prepare("DELETE FROM `answer` WHERE `answer`.`question_id` = :question_id AND `answer`.`team_id` = :team_id");
		$data['question_id'] = $this -> get_question_id();
		$data['team_id'] = $this -> get_team_id();
		$sth -> execute($data);
	}

	/**
	 * Retrieve by primary key
	 */
	public static function get($question_id, $team_id) {
		$sth = database::$dbh -> prepare("SELECT `answer`.`question_id`, `answer`.`team_id`, `answer`.`answer_text`, `answer`.`answer_is_correct`, `answer`.`answer_time`, `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM answer JOIN `question` ON `answer`.`question_id` = `question`.`question_id` JOIN `team` ON `answer`.`team_id` = `team`.`team_id` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE `answer`.`question_id` = :question_id AND `answer`.`team_id` = :team_id;");
		$sth -> execute(array('question_id' => $question_id, 'team_id' => $team_id));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new answer_model($assoc);
	}

	/**
	 * List all rows
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function list_all($start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `answer`.`question_id`, `answer`.`team_id`, `answer`.`answer_text`, `answer`.`answer_is_correct`, `answer`.`answer_time`, `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `answer` JOIN `question` ON `answer`.`question_id` = `question`.`question_id` JOIN `team` ON `answer`.`team_id` = `team`.`team_id` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `team`.`game_id` = `game`.`game_id`" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute();
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new answer_model($assoc);
		}
		return $ret;
	}

	/**
	 * List rows by team_id index
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function list_by_team_id($team_id, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `answer`.`question_id`, `answer`.`team_id`, `answer`.`answer_text`, `answer`.`answer_is_correct`, `answer`.`answer_time`, `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `answer` JOIN `question` ON `answer`.`question_id` = `question`.`question_id` JOIN `team` ON `answer`.`team_id` = `team`.`team_id` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE answer.team_id = :team_id" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('team_id' => $team_id));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new answer_model($assoc);
		}
		return $ret;
	}

	/**
	 * List rows by question_id index
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function list_by_question_id($question_id, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `answer`.`question_id`, `answer`.`team_id`, `answer`.`answer_text`, `answer`.`answer_is_correct`, `answer`.`answer_time`, `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `answer` JOIN `question` ON `answer`.`question_id` = `question`.`question_id` JOIN `team` ON `answer`.`team_id` = `team`.`team_id` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE answer.question_id = :question_id" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('question_id' => $question_id));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new answer_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within answer_text field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_answer_text($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `answer`.`question_id`, `answer`.`team_id`, `answer`.`answer_text`, `answer`.`answer_is_correct`, `answer`.`answer_time`, `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `answer` JOIN `question` ON `answer`.`question_id` = `question`.`question_id` JOIN `team` ON `answer`.`team_id` = `team`.`team_id` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE answer_text LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new answer_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within answer_time field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_answer_time($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `answer`.`question_id`, `answer`.`team_id`, `answer`.`answer_text`, `answer`.`answer_is_correct`, `answer`.`answer_time`, `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `answer` JOIN `question` ON `answer`.`question_id` = `question`.`question_id` JOIN `team` ON `answer`.`team_id` = `team`.`team_id` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE answer_time LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new answer_model($assoc);
		}
		return $ret;
	}
}
?>