<?php
class question_model {
	/**
	 * @var int question_id
	 */
	private $question_id;

	/**
	 * @var int round_id
	 */
	private $round_id;

	/**
	 * @var string question_text
	 */
	private $question_text;

	/**
	 * @var int question_sortkey
	 */
	private $question_sortkey;

	/**
	 * @var int question_state
	 */
	private $question_state;

	/**
	 * @var string question_answer
	 */
	private $question_answer;

	private $model_variables_changed; // Only variables which have been changed
	private $model_variables_set; // All variables which have been set (initially or with a setter)

	/* Parent tables */
	public $round;

	/* Child tables */
	public $list_answer;

	/* Sort clause to add when listing rows from this table */
	const SORT_CLAUSE = " ORDER BY `question`.`question_id`";

	/**
	 * Initialise and load related tables
	 */
	public static function init() {
		core::loadClass("database");
		core::loadClass("round_model");

		/* Child tables */
		core::loadClass("answer_model");
	}

	/**
	 * Construct new question from field list
	 * 
	 * @return array
	 */
	public function __construct(array $fields = array()) {
		/* Initialise everything as blank to avoid tripping up the permissions fitlers */
		$this -> question_id = '';
		$this -> round_id = '';
		$this -> question_text = '';
		$this -> question_sortkey = '';
		$this -> question_state = '';
		$this -> question_answer = '';

		if(isset($fields['question.question_id'])) {
			$this -> set_question_id($fields['question.question_id']);
		}
		if(isset($fields['question.round_id'])) {
			$this -> set_round_id($fields['question.round_id']);
		}
		if(isset($fields['question.question_text'])) {
			$this -> set_question_text($fields['question.question_text']);
		}
		if(isset($fields['question.question_sortkey'])) {
			$this -> set_question_sortkey($fields['question.question_sortkey']);
		}
		if(isset($fields['question.question_state'])) {
			$this -> set_question_state($fields['question.question_state']);
		}
		if(isset($fields['question.question_answer'])) {
			$this -> set_question_answer($fields['question.question_answer']);
		}

		$this -> model_variables_changed = array();
		$this -> round = new round_model($fields);
		$this -> list_answer = array();
	}

	/**
	 * Convert question to shallow associative array
	 * 
	 * @return array
	 */
	private function to_array() {
		$values = array(
			'question_id' => $this -> question_id,
			'round_id' => $this -> round_id,
			'question_text' => $this -> question_text,
			'question_sortkey' => $this -> question_sortkey,
			'question_state' => $this -> question_state,
			'question_answer' => $this -> question_answer);
		return $values;
	}

	/**
	 * Convert question to associative array, including only visible fields,
	 * parent tables, and loaded child tables
	 * 
	 * @param string $role The user role to use
	 */
	public function to_array_filtered($role = "anon") {
		if(core::$permission[$role]['question']['read'] === false) {
			return false;
		}
		$values = array();
		$everything = $this -> to_array();
		foreach(core::$permission[$role]['question']['read'] as $field) {
			if(!isset($everything[$field])) {
				throw new Exception("Check permissions: '$field' is not a real field in question");
			}
			$values[$field] = $everything[$field];
		}
		$values['round'] = $this -> round -> to_array_filtered($role);

		/* Add filtered versions of everything that's been loaded */
		$values['answer'] = array();
		foreach($this -> list_answer as $answer) {
			$values['answer'][] = $answer -> to_array_filtered($role);
		}
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
			"question.question_id" => $row[0],
			"question.round_id" => $row[1],
			"question.question_text" => $row[2],
			"question.question_sortkey" => $row[3],
			"question.question_state" => $row[4],
			"question.question_answer" => $row[5],
			"round.round_id" => $row[6],
			"round.name" => $row[7],
			"round.game_id" => $row[8],
			"round.round_sortkey" => $row[9],
			"round.round_state" => $row[10],
			"game.game_id" => $row[11],
			"game.game_name" => $row[12],
			"game.game_state" => $row[13],
			"game.game_code" => $row[14]);
		return $values;
	}

	/**
	 * Get question_id
	 * 
	 * @return int
	 */
	public function get_question_id() {
		if(!isset($this -> model_variables_set['question_id'])) {
			throw new Exception("question.question_id has not been initialised.");
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
			throw new Exception("question.question_id must be numeric");
		}
		$this -> question_id = $question_id;
		$this -> model_variables_changed['question_id'] = true;
		$this -> model_variables_set['question_id'] = true;
	}

	/**
	 * Get round_id
	 * 
	 * @return int
	 */
	public function get_round_id() {
		if(!isset($this -> model_variables_set['round_id'])) {
			throw new Exception("question.round_id has not been initialised.");
		}
		return $this -> round_id;
	}

	/**
	 * Set round_id
	 * 
	 * @param int $round_id
	 */
	public function set_round_id($round_id) {
		if(!is_numeric($round_id)) {
			throw new Exception("question.round_id must be numeric");
		}
		$this -> round_id = $round_id;
		$this -> model_variables_changed['round_id'] = true;
		$this -> model_variables_set['round_id'] = true;
	}

	/**
	 * Get question_text
	 * 
	 * @return string
	 */
	public function get_question_text() {
		if(!isset($this -> model_variables_set['question_text'])) {
			throw new Exception("question.question_text has not been initialised.");
		}
		return $this -> question_text;
	}

	/**
	 * Set question_text
	 * 
	 * @param string $question_text
	 */
	public function set_question_text($question_text) {
		// TODO: Add TEXT validation to question.question_text
		$this -> question_text = $question_text;
		$this -> model_variables_changed['question_text'] = true;
		$this -> model_variables_set['question_text'] = true;
	}

	/**
	 * Get question_sortkey
	 * 
	 * @return int
	 */
	public function get_question_sortkey() {
		if(!isset($this -> model_variables_set['question_sortkey'])) {
			throw new Exception("question.question_sortkey has not been initialised.");
		}
		return $this -> question_sortkey;
	}

	/**
	 * Set question_sortkey
	 * 
	 * @param int $question_sortkey
	 */
	public function set_question_sortkey($question_sortkey) {
		if(!is_numeric($question_sortkey)) {
			throw new Exception("question.question_sortkey must be numeric");
		}
		$this -> question_sortkey = $question_sortkey;
		$this -> model_variables_changed['question_sortkey'] = true;
		$this -> model_variables_set['question_sortkey'] = true;
	}

	/**
	 * Get question_state
	 * 
	 * @return int
	 */
	public function get_question_state() {
		if(!isset($this -> model_variables_set['question_state'])) {
			throw new Exception("question.question_state has not been initialised.");
		}
		return $this -> question_state;
	}

	/**
	 * Set question_state
	 * 
	 * @param int $question_state
	 */
	public function set_question_state($question_state) {
		if(!is_numeric($question_state)) {
			throw new Exception("question.question_state must be numeric");
		}
		$this -> question_state = $question_state;
		$this -> model_variables_changed['question_state'] = true;
		$this -> model_variables_set['question_state'] = true;
	}

	/**
	 * Get question_answer
	 * 
	 * @return string
	 */
	public function get_question_answer() {
		if(!isset($this -> model_variables_set['question_answer'])) {
			throw new Exception("question.question_answer has not been initialised.");
		}
		return $this -> question_answer;
	}

	/**
	 * Set question_answer
	 * 
	 * @param string $question_answer
	 */
	public function set_question_answer($question_answer) {
		// TODO: Add TEXT validation to question.question_answer
		$this -> question_answer = $question_answer;
		$this -> model_variables_changed['question_answer'] = true;
		$this -> model_variables_set['question_answer'] = true;
	}

	/**
	 * Update question
	 */
	public function update() {
		if(count($this -> model_variables_changed) == 0) {
			throw new Exception("Nothing to update");
		}

		/* Compose list of changed fields */
		$fieldset = array();
		$everything = $this -> to_array();
		$data['question_id'] = $this -> get_question_id();
		foreach($this -> model_variables_changed as $col => $changed) {
			$fieldset[] = "`$col` = :$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);

		/* Execute query */
		$sth = database::$dbh -> prepare("UPDATE `question` SET $fields WHERE `question`.`question_id` = :question_id");
		$sth -> execute($data);
	}

	/**
	 * Add new question
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
		$sth = database::$dbh -> prepare("INSERT INTO `question` ($fields) VALUES ($vals);");
		$sth -> execute($data);
		$this -> set_question_id(database::$dbh->lastInsertId());
	}

	/**
	 * Delete question
	 */
	public function delete() {
		$sth = database::$dbh -> prepare("DELETE FROM `question` WHERE `question`.`question_id` = :question_id");
		$data['question_id'] = $this -> get_question_id();
		$sth -> execute($data);
	}

	/**
	 * List associated rows from answer table
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public function populate_list_answer($start = 0, $limit = -1) {
		$question_id = $this -> get_question_id();
		$this -> list_answer = answer_model::list_by_question_id($question_id, $start, $limit);
	}

	/**
	 * Retrieve by primary key
	 */
	public static function get($question_id) {
		$sth = database::$dbh -> prepare("SELECT `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM question JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `round`.`game_id` = `game`.`game_id` WHERE `question`.`question_id` = :question_id;");
		$sth -> execute(array('question_id' => $question_id));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new question_model($assoc);
	}

	/**
	 * Retrieve by question_sort
	 */
	public static function get_by_question_sort($round_id, $question_sortkey) {
		$sth = database::$dbh -> prepare("SELECT `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM question JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `round`.`game_id` = `game`.`game_id` WHERE `question`.`round_id` = :round_id AND `question`.`question_sortkey` = :question_sortkey;");
		$sth -> execute(array('round_id' => $round_id, 'question_sortkey' => $question_sortkey));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new question_model($assoc);
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
		$sth = database::$dbh -> prepare("SELECT `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `question` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `round`.`game_id` = `game`.`game_id`" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute();
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new question_model($assoc);
		}
		return $ret;
	}

	/**
	 * List rows by round_id index
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function list_by_round_id($round_id, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `question` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `round`.`game_id` = `game`.`game_id` WHERE question.round_id = :round_id" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('round_id' => $round_id));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new question_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within question_text field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_question_text($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `question` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `round`.`game_id` = `game`.`game_id` WHERE question_text LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new question_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within question_answer field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_question_answer($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `question`.`question_id`, `question`.`round_id`, `question`.`question_text`, `question`.`question_sortkey`, `question`.`question_state`, `question`.`question_answer`, `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `question` JOIN `round` ON `question`.`round_id` = `round`.`round_id` JOIN `game` ON `round`.`game_id` = `game`.`game_id` WHERE question_answer LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new question_model($assoc);
		}
		return $ret;
	}
}
?>