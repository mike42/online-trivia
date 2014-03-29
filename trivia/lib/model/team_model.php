<?php
class team_model {
	/**
	 * @var int team_id
	 */
	private $team_id;

	/**
	 * @var string team_code
	 */
	private $team_code;

	/**
	 * @var int game_id
	 */
	private $game_id;

	/**
	 * @var string team_name
	 */
	private $team_name;

	private $model_variables_changed; // Only variables which have been changed
	private $model_variables_set; // All variables which have been set (initially or with a setter)

	/* Parent tables */
	public $game;

	/* Child tables */
	public $list_answer;
	public $list_person_table;
	public $list_team_round;

	/* Sort clause to add when listing rows from this table */
	const SORT_CLAUSE = " ORDER BY `team`.`team_name`";

	/**
	 * Initialise and load related tables
	 */
	public static function init() {
		core::loadClass("database");
		core::loadClass("game_model");

		/* Child tables */
		core::loadClass("answer_model");
		core::loadClass("person_table_model");
		core::loadClass("team_round_model");
	}

	/**
	 * Construct new team from field list
	 * 
	 * @return array
	 */
	public function __construct(array $fields = array()) {
		/* Initialise everything as blank to avoid tripping up the permissions fitlers */
		$this -> team_id = '';
		$this -> team_code = '';
		$this -> game_id = '';
		$this -> team_name = '';

		if(isset($fields['team.team_id'])) {
			$this -> set_team_id($fields['team.team_id']);
		}
		if(isset($fields['team.team_code'])) {
			$this -> set_team_code($fields['team.team_code']);
		}
		if(isset($fields['team.game_id'])) {
			$this -> set_game_id($fields['team.game_id']);
		}
		if(isset($fields['team.team_name'])) {
			$this -> set_team_name($fields['team.team_name']);
		}

		$this -> model_variables_changed = array();
		$this -> game = new game_model($fields);
		$this -> list_answer = array();
		$this -> list_person_table = array();
		$this -> list_team_round = array();
	}

	/**
	 * Convert team to shallow associative array
	 * 
	 * @return array
	 */
	private function to_array() {
		$values = array(
			'team_id' => $this -> team_id,
			'team_code' => $this -> team_code,
			'game_id' => $this -> game_id,
			'team_name' => $this -> team_name);
		return $values;
	}

	/**
	 * Convert team to associative array, including only visible fields,
	 * parent tables, and loaded child tables
	 * 
	 * @param string $role The user role to use
	 */
	public function to_array_filtered($role = "anon") {
		if(core::$permission[$role]['team']['read'] === false) {
			return false;
		}
		$values = array();
		$everything = $this -> to_array();
		foreach(core::$permission[$role]['team']['read'] as $field) {
			if(!isset($everything[$field])) {
				throw new Exception("Check permissions: '$field' is not a real field in team");
			}
			$values[$field] = $everything[$field];
		}
		$values['game'] = $this -> game -> to_array_filtered($role);

		/* Add filtered versions of everything that's been loaded */
		$values['answer'] = array();
		$values['person_table'] = array();
		$values['team_round'] = array();
		foreach($this -> list_answer as $answer) {
			$values['answer'][] = $answer -> to_array_filtered($role);
		}
		foreach($this -> list_person_table as $person_table) {
			$values['person_table'][] = $person_table -> to_array_filtered($role);
		}
		foreach($this -> list_team_round as $team_round) {
			$values['team_round'][] = $team_round -> to_array_filtered($role);
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
			"team.team_id" => $row[0],
			"team.team_code" => $row[1],
			"team.game_id" => $row[2],
			"team.team_name" => $row[3],
			"game.game_id" => $row[4],
			"game.game_name" => $row[5],
			"game.game_state" => $row[6],
			"game.game_code" => $row[7]);
		return $values;
	}

	/**
	 * Get team_id
	 * 
	 * @return int
	 */
	public function get_team_id() {
		if(!isset($this -> model_variables_set['team_id'])) {
			throw new Exception("team.team_id has not been initialised.");
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
			throw new Exception("team.team_id must be numeric");
		}
		$this -> team_id = $team_id;
		$this -> model_variables_changed['team_id'] = true;
		$this -> model_variables_set['team_id'] = true;
	}

	/**
	 * Get team_code
	 * 
	 * @return string
	 */
	public function get_team_code() {
		if(!isset($this -> model_variables_set['team_code'])) {
			throw new Exception("team.team_code has not been initialised.");
		}
		return $this -> team_code;
	}

	/**
	 * Set team_code
	 * 
	 * @param string $team_code
	 */
	public function set_team_code($team_code) {
		if(strlen($team_code) > 6) {
			throw new Exception("team.team_code cannot be longer than 6 characters");
		}
		$this -> team_code = $team_code;
		$this -> model_variables_changed['team_code'] = true;
		$this -> model_variables_set['team_code'] = true;
	}

	/**
	 * Get game_id
	 * 
	 * @return int
	 */
	public function get_game_id() {
		if(!isset($this -> model_variables_set['game_id'])) {
			throw new Exception("team.game_id has not been initialised.");
		}
		return $this -> game_id;
	}

	/**
	 * Set game_id
	 * 
	 * @param int $game_id
	 */
	public function set_game_id($game_id) {
		if(!is_numeric($game_id)) {
			throw new Exception("team.game_id must be numeric");
		}
		$this -> game_id = $game_id;
		$this -> model_variables_changed['game_id'] = true;
		$this -> model_variables_set['game_id'] = true;
	}

	/**
	 * Get team_name
	 * 
	 * @return string
	 */
	public function get_team_name() {
		if(!isset($this -> model_variables_set['team_name'])) {
			throw new Exception("team.team_name has not been initialised.");
		}
		return $this -> team_name;
	}

	/**
	 * Set team_name
	 * 
	 * @param string $team_name
	 */
	public function set_team_name($team_name) {
		// TODO: Add TEXT validation to team.team_name
		$this -> team_name = $team_name;
		$this -> model_variables_changed['team_name'] = true;
		$this -> model_variables_set['team_name'] = true;
	}

	/**
	 * Update team
	 */
	public function update() {
		if(count($this -> model_variables_changed) == 0) {
			throw new Exception("Nothing to update");
		}

		/* Compose list of changed fields */
		$fieldset = array();
		$everything = $this -> to_array();
		$data['team_id'] = $this -> get_team_id();
		foreach($this -> model_variables_changed as $col => $changed) {
			$fieldset[] = "`$col` = :$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);

		/* Execute query */
		$sth = database::$dbh -> prepare("UPDATE `team` SET $fields WHERE `team`.`team_id` = :team_id");
		$sth -> execute($data);
	}

	/**
	 * Add new team
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
		$sth = database::$dbh -> prepare("INSERT INTO `team` ($fields) VALUES ($vals);");
		$sth -> execute($data);
		$this -> set_team_id(database::$dbh->lastInsertId());
	}

	/**
	 * Delete team
	 */
	public function delete() {
		$sth = database::$dbh -> prepare("DELETE FROM `team` WHERE `team`.`team_id` = :team_id");
		$data['team_id'] = $this -> get_team_id();
		$sth -> execute($data);
	}

	/**
	 * List associated rows from answer table
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public function populate_list_answer($start = 0, $limit = -1) {
		$team_id = $this -> get_team_id();
		$this -> list_answer = answer_model::list_by_team_id($team_id, $start, $limit);
	}

	/**
	 * List associated rows from person_table table
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public function populate_list_person_table($start = 0, $limit = -1) {
		$team_id = $this -> get_team_id();
		$this -> list_person_table = person_table_model::list_by_team_id_fk1($team_id, $start, $limit);
	}

	/**
	 * List associated rows from team_round table
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public function populate_list_team_round($start = 0, $limit = -1) {
		$team_team_id = $this -> get_team_id();
		$this -> list_team_round = team_round_model::list_by_fk_team_round_team1_idx($team_team_id, $start, $limit);
	}

	/**
	 * Retrieve by primary key
	 */
	public static function get($team_id) {
		$sth = database::$dbh -> prepare("SELECT `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM team JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE `team`.`team_id` = :team_id;");
		$sth -> execute(array('team_id' => $team_id));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new team_model($assoc);
	}

	/**
	 * Retrieve by team_code
	 */
	public static function get_by_team_code($team_code) {
		$sth = database::$dbh -> prepare("SELECT `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM team JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE `team`.`team_code` = :team_code;");
		$sth -> execute(array('team_code' => $team_code));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new team_model($assoc);
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
		$sth = database::$dbh -> prepare("SELECT `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `team` JOIN `game` ON `team`.`game_id` = `game`.`game_id`" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute();
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new team_model($assoc);
		}
		return $ret;
	}

	/**
	 * List rows by game_id index
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function list_by_game_id($game_id, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `team` JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE team.game_id = :game_id" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('game_id' => $game_id));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new team_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within team_code field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_team_code($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `team` JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE team_code LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new team_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within team_name field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_team_name($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `team`.`team_id`, `team`.`team_code`, `team`.`game_id`, `team`.`team_name`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `team` JOIN `game` ON `team`.`game_id` = `game`.`game_id` WHERE team_name LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new team_model($assoc);
		}
		return $ret;
	}
}
?>