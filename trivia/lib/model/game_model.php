<?php
class game_model {
	/**
	 * @var int game_id
	 */
	private $game_id;

	/**
	 * @var string game_name
	 */
	private $game_name;

	/**
	 * @var int game_state
	 */
	private $game_state;

	/**
	 * @var string game_code
	 */
	private $game_code;

	private $model_variables_changed; // Only variables which have been changed
	private $model_variables_set; // All variables which have been set (initially or with a setter)

	/* Child tables */
	public $list_team;
	public $list_round;
	public $list_person;

	/* Sort clause to add when listing rows from this table */
	const SORT_CLAUSE = " ORDER BY `game`.`game_id`";

	/**
	 * Initialise and load related tables
	 */
	public static function init() {
		core::loadClass("database");

		/* Child tables */
		core::loadClass("team_model");
		core::loadClass("round_model");
		core::loadClass("person_model");
	}

	/**
	 * Construct new game from field list
	 * 
	 * @return array
	 */
	public function __construct(array $fields = array()) {
		/* Initialise everything as blank to avoid tripping up the permissions fitlers */
		$this -> game_id = '';
		$this -> game_name = '';
		$this -> game_state = '';
		$this -> game_code = '';

		if(isset($fields['game.game_id'])) {
			$this -> set_game_id($fields['game.game_id']);
		}
		if(isset($fields['game.game_name'])) {
			$this -> set_game_name($fields['game.game_name']);
		}
		if(isset($fields['game.game_state'])) {
			$this -> set_game_state($fields['game.game_state']);
		}
		if(isset($fields['game.game_code'])) {
			$this -> set_game_code($fields['game.game_code']);
		}

		$this -> model_variables_changed = array();
		$this -> list_team = array();
		$this -> list_round = array();
		$this -> list_person = array();
	}

	/**
	 * Convert game to shallow associative array
	 * 
	 * @return array
	 */
	private function to_array() {
		$values = array(
			'game_id' => $this -> game_id,
			'game_name' => $this -> game_name,
			'game_state' => $this -> game_state,
			'game_code' => $this -> game_code);
		return $values;
	}

	/**
	 * Convert game to associative array, including only visible fields,
	 * parent tables, and loaded child tables
	 * 
	 * @param string $role The user role to use
	 */
	public function to_array_filtered($role = "anon") {
		if(core::$permission[$role]['game']['read'] === false) {
			return false;
		}
		$values = array();
		$everything = $this -> to_array();
		foreach(core::$permission[$role]['game']['read'] as $field) {
			if(!isset($everything[$field])) {
				throw new Exception("Check permissions: '$field' is not a real field in game");
			}
			$values[$field] = $everything[$field];
		}

		/* Add filtered versions of everything that's been loaded */
		$values['team'] = array();
		$values['round'] = array();
		$values['person'] = array();
		foreach($this -> list_team as $team) {
			$values['team'][] = $team -> to_array_filtered($role);
		}
		foreach($this -> list_round as $round) {
			$values['round'][] = $round -> to_array_filtered($role);
		}
		foreach($this -> list_person as $person) {
			$values['person'][] = $person -> to_array_filtered($role);
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
			"game.game_id" => $row[0],
			"game.game_name" => $row[1],
			"game.game_state" => $row[2],
			"game.game_code" => $row[3]);
		return $values;
	}

	/**
	 * Get game_id
	 * 
	 * @return int
	 */
	public function get_game_id() {
		if(!isset($this -> model_variables_set['game_id'])) {
			throw new Exception("game.game_id has not been initialised.");
		}
		return $this -> game_id;
	}

	/**
	 * Set game_id
	 * 
	 * @param int $game_id
	 */
	private function set_game_id($game_id) {
		if(!is_numeric($game_id)) {
			throw new Exception("game.game_id must be numeric");
		}
		$this -> game_id = $game_id;
		$this -> model_variables_changed['game_id'] = true;
		$this -> model_variables_set['game_id'] = true;
	}

	/**
	 * Get game_name
	 * 
	 * @return string
	 */
	public function get_game_name() {
		if(!isset($this -> model_variables_set['game_name'])) {
			throw new Exception("game.game_name has not been initialised.");
		}
		return $this -> game_name;
	}

	/**
	 * Set game_name
	 * 
	 * @param string $game_name
	 */
	public function set_game_name($game_name) {
		// TODO: Add TEXT validation to game.game_name
		$this -> game_name = $game_name;
		$this -> model_variables_changed['game_name'] = true;
		$this -> model_variables_set['game_name'] = true;
	}

	/**
	 * Get game_state
	 * 
	 * @return int
	 */
	public function get_game_state() {
		if(!isset($this -> model_variables_set['game_state'])) {
			throw new Exception("game.game_state has not been initialised.");
		}
		return $this -> game_state;
	}

	/**
	 * Set game_state
	 * 
	 * @param int $game_state
	 */
	public function set_game_state($game_state) {
		if(!is_numeric($game_state)) {
			throw new Exception("game.game_state must be numeric");
		}
		$this -> game_state = $game_state;
		$this -> model_variables_changed['game_state'] = true;
		$this -> model_variables_set['game_state'] = true;
	}

	/**
	 * Get game_code
	 * 
	 * @return string
	 */
	public function get_game_code() {
		if(!isset($this -> model_variables_set['game_code'])) {
			throw new Exception("game.game_code has not been initialised.");
		}
		return $this -> game_code;
	}

	/**
	 * Set game_code
	 * 
	 * @param string $game_code
	 */
	public function set_game_code($game_code) {
		if(strlen($game_code) > 6) {
			throw new Exception("game.game_code cannot be longer than 6 characters");
		}
		$this -> game_code = $game_code;
		$this -> model_variables_changed['game_code'] = true;
		$this -> model_variables_set['game_code'] = true;
	}

	/**
	 * Update game
	 */
	public function update() {
		if(count($this -> model_variables_changed) == 0) {
			throw new Exception("Nothing to update");
		}

		/* Compose list of changed fields */
		$fieldset = array();
		$everything = $this -> to_array();
		$data['game_id'] = $this -> get_game_id();
		foreach($this -> model_variables_changed as $col => $changed) {
			$fieldset[] = "`$col` = :$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);

		/* Execute query */
		$sth = database::$dbh -> prepare("UPDATE `game` SET $fields WHERE `game`.`game_id` = :game_id");
		$sth -> execute($data);
	}

	/**
	 * Add new game
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
		$sth = database::$dbh -> prepare("INSERT INTO `game` ($fields) VALUES ($vals);");
		$sth -> execute($data);
		$this -> set_game_id(database::$dbh->lastInsertId());
	}

	/**
	 * Delete game
	 */
	public function delete() {
		$sth = database::$dbh -> prepare("DELETE FROM `game` WHERE `game`.`game_id` = :game_id");
		$data['game_id'] = $this -> get_game_id();
		$sth -> execute($data);
	}

	/**
	 * List associated rows from team table
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public function populate_list_team($start = 0, $limit = -1) {
		$game_id = $this -> get_game_id();
		$this -> list_team = team_model::list_by_game_id($game_id, $start, $limit);
	}

	/**
	 * List associated rows from round table
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public function populate_list_round($start = 0, $limit = -1) {
		$game_id = $this -> get_game_id();
		$this -> list_round = round_model::list_by_game_id_fk2($game_id, $start, $limit);
	}

	/**
	 * List associated rows from person table
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public function populate_list_person($start = 0, $limit = -1) {
		$game_id = $this -> get_game_id();
		$this -> list_person = person_model::list_by_game_id($game_id, $start, $limit);
	}

	/**
	 * Retrieve by primary key
	 */
	public static function get($game_id) {
		$sth = database::$dbh -> prepare("SELECT `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM game  WHERE `game`.`game_id` = :game_id;");
		$sth -> execute(array('game_id' => $game_id));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new game_model($assoc);
	}

	/**
	 * Retrieve by game_code
	 */
	public static function get_by_game_code($game_code) {
		$sth = database::$dbh -> prepare("SELECT `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM game  WHERE `game`.`game_code` = :game_code;");
		$sth -> execute(array('game_code' => $game_code));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new game_model($assoc);
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
		$sth = database::$dbh -> prepare("SELECT `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `game` " . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute();
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new game_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within game_name field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_game_name($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `game`  WHERE game_name LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new game_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within game_code field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_game_code($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `game`  WHERE game_code LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new game_model($assoc);
		}
		return $ret;
	}
	
	public function reset() {
		$sth = database::$dbh -> prepare("DELETE team_round FROM team_round JOIN team ON team_round.team_team_id = team.team_id WHERE team.game_id = :game_id;");
		$sth -> execute(array('game_id' => $this -> get_game_id()));
		$sth = database::$dbh -> prepare("DELETE answer FROM answer JOIN question ON answer.question_id = question.question_id JOIN round ON round.round_id = question.round_id WHERE round.game_id = :game_id;");
		$sth -> execute(array('game_id' => $this -> get_game_id()));
		$sth = database::$dbh -> prepare("DELETE person_table FROM person_table JOIN person ON person.person_id = person_table.person_id JOIN game ON person.game_id = game.game_id WHERE game.game_id = :game_id;");
		$sth -> execute(array('game_id' => $this -> get_game_id()));
	}
}
?>