<?php
class round_model {
	/**
	 * @var int round_id
	 */
	private $round_id;

	/**
	 * @var string name
	 */
	private $name;

	/**
	 * @var int game_id
	 */
	private $game_id;

	/**
	 * @var int round_sortkey
	 */
	private $round_sortkey;

	/**
	 * @var int round_state
	 */
	private $round_state;

	private $model_variables_changed; // Only variables which have been changed
	private $model_variables_set; // All variables which have been set (initially or with a setter)

	/* Parent tables */
	public $game;

	/* Child tables */
	public $list_question;
	public $list_team_round;

	/* Sort clause to add when listing rows from this table */
	const SORT_CLAUSE = " ORDER BY `round`.`round_sortkey`";

	/**
	 * Initialise and load related tables
	 */
	public static function init() {
		core::loadClass("database");
		core::loadClass("game_model");

		/* Child tables */
		core::loadClass("question_model");
	}

	/**
	 * Construct new round from field list
	 * 
	 * @return array
	 */
	public function __construct(array $fields = array()) {
		/* Initialise everything as blank to avoid tripping up the permissions fitlers */
		$this -> round_id = '';
		$this -> name = '';
		$this -> game_id = '';
		$this -> round_sortkey = '';
		$this -> round_state = '';

		if(isset($fields['round.round_id'])) {
			$this -> set_round_id($fields['round.round_id']);
		}
		if(isset($fields['round.name'])) {
			$this -> set_name($fields['round.name']);
		}
		if(isset($fields['round.game_id'])) {
			$this -> set_game_id($fields['round.game_id']);
		}
		if(isset($fields['round.round_sortkey'])) {
			$this -> set_round_sortkey($fields['round.round_sortkey']);
		}
		if(isset($fields['round.round_state'])) {
			$this -> set_round_state($fields['round.round_state']);
		}

		$this -> model_variables_changed = array();
		$this -> game = new game_model($fields);
		$this -> list_question = array();
		$this -> list_team_round = array();
	}

	/**
	 * Convert round to shallow associative array
	 * 
	 * @return array
	 */
	private function to_array() {
		$values = array(
			'round_id' => $this -> round_id,
			'name' => $this -> name,
			'game_id' => $this -> game_id,
			'round_sortkey' => $this -> round_sortkey,
			'round_state' => $this -> round_state);
		return $values;
	}

	/**
	 * Convert round to associative array, including only visible fields,
	 * parent tables, and loaded child tables
	 * 
	 * @param string $role The user role to use
	 */
	public function to_array_filtered($role = "anon") {
		if(core::$permission[$role]['round']['read'] === false) {
			return false;
		}
		$values = array();
		$everything = $this -> to_array();
		foreach(core::$permission[$role]['round']['read'] as $field) {
			if(!isset($everything[$field])) {
				throw new Exception("Check permissions: '$field' is not a real field in round");
			}
			$values[$field] = $everything[$field];
		}
		$values['game'] = $this -> game -> to_array_filtered($role);

		/* Add filtered versions of everything that's been loaded */
		$values['question'] = array();
		foreach($this -> list_question as $question) {
			$values['question'][] = $question -> to_array_filtered($role);
		}
		$values['team_round'] = array();
		foreach($this -> list_team_round  as $team_round) {
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
			"round.round_id" => $row[0],
			"round.name" => $row[1],
			"round.game_id" => $row[2],
			"round.round_sortkey" => $row[3],
			"round.round_state" => $row[4],
			"game.game_id" => $row[5],
			"game.game_name" => $row[6],
			"game.game_state" => $row[7],
			"game.game_code" => $row[8]);
		return $values;
	}

	/**
	 * Get round_id
	 * 
	 * @return int
	 */
	public function get_round_id() {
		if(!isset($this -> model_variables_set['round_id'])) {
			throw new Exception("round.round_id has not been initialised.");
		}
		return $this -> round_id;
	}

	/**
	 * Set round_id
	 * 
	 * @param int $round_id
	 */
	private function set_round_id($round_id) {
		if(!is_numeric($round_id)) {
			throw new Exception("round.round_id must be numeric");
		}
		$this -> round_id = $round_id;
		$this -> model_variables_changed['round_id'] = true;
		$this -> model_variables_set['round_id'] = true;
	}

	/**
	 * Get name
	 * 
	 * @return string
	 */
	public function get_name() {
		if(!isset($this -> model_variables_set['name'])) {
			throw new Exception("round.name has not been initialised.");
		}
		return $this -> name;
	}

	/**
	 * Set name
	 * 
	 * @param string $name
	 */
	public function set_name($name) {
		// TODO: Add TEXT validation to round.name
		$this -> name = $name;
		$this -> model_variables_changed['name'] = true;
		$this -> model_variables_set['name'] = true;
	}

	/**
	 * Get game_id
	 * 
	 * @return int
	 */
	public function get_game_id() {
		if(!isset($this -> model_variables_set['game_id'])) {
			throw new Exception("round.game_id has not been initialised.");
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
			throw new Exception("round.game_id must be numeric");
		}
		$this -> game_id = $game_id;
		$this -> model_variables_changed['game_id'] = true;
		$this -> model_variables_set['game_id'] = true;
	}

	/**
	 * Get round_sortkey
	 * 
	 * @return int
	 */
	public function get_round_sortkey() {
		if(!isset($this -> model_variables_set['round_sortkey'])) {
			throw new Exception("round.round_sortkey has not been initialised.");
		}
		return $this -> round_sortkey;
	}

	/**
	 * Set round_sortkey
	 * 
	 * @param int $round_sortkey
	 */
	public function set_round_sortkey($round_sortkey) {
		if(!is_numeric($round_sortkey)) {
			throw new Exception("round.round_sortkey must be numeric");
		}
		$this -> round_sortkey = $round_sortkey;
		$this -> model_variables_changed['round_sortkey'] = true;
		$this -> model_variables_set['round_sortkey'] = true;
	}

	/**
	 * Get round_state
	 * 
	 * @return int
	 */
	public function get_round_state() {
		if(!isset($this -> model_variables_set['round_state'])) {
			throw new Exception("round.round_state has not been initialised.");
		}
		return $this -> round_state;
	}

	/**
	 * Set round_state
	 * 
	 * @param int $round_state
	 */
	public function set_round_state($round_state) {
		if(!is_numeric($round_state)) {
			throw new Exception("round.round_state must be numeric");
		}
		$this -> round_state = $round_state;
		$this -> model_variables_changed['round_state'] = true;
		$this -> model_variables_set['round_state'] = true;
	}

	/**
	 * Update round
	 */
	public function update() {
		if(count($this -> model_variables_changed) == 0) {
			throw new Exception("Nothing to update");
		}

		/* Compose list of changed fields */
		$fieldset = array();
		$everything = $this -> to_array();
		$data['round_id'] = $this -> get_round_id();
		foreach($this -> model_variables_changed as $col => $changed) {
			$fieldset[] = "`$col` = :$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);

		/* Execute query */
		$sth = database::$dbh -> prepare("UPDATE `round` SET $fields WHERE `round`.`round_id` = :round_id");
		$sth -> execute($data);
	}

	/**
	 * Add new round
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
		$sth = database::$dbh -> prepare("INSERT INTO `round` ($fields) VALUES ($vals);");
		$sth -> execute($data);
		$this -> set_round_id(database::$dbh->lastInsertId());
	}

	/**
	 * Delete round
	 */
	public function delete() {
		$sth = database::$dbh -> prepare("DELETE FROM `round` WHERE `round`.`round_id` = :round_id");
		$data['round_id'] = $this -> get_round_id();
		$sth -> execute($data);
	}

	/**
	 * List associated rows from question table
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public function populate_list_question($start = 0, $limit = -1) {
		$round_id = $this -> get_round_id();
		$this -> list_question = question_model::list_by_round_id($round_id, $start, $limit);
	}

	/**
	 * Retrieve by primary key
	 */
	public static function get($round_id) {
		$sth = database::$dbh -> prepare("SELECT `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM round JOIN `game` ON `round`.`game_id` = `game`.`game_id` WHERE `round`.`round_id` = :round_id;");
		$sth -> execute(array('round_id' => $round_id));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new round_model($assoc);
	}

	/**
	 * Retrieve by round_sort
	 */
	public static function get_by_round_sort($game_id, $round_sortkey) {
		$sth = database::$dbh -> prepare("SELECT `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM round JOIN `game` ON `round`.`game_id` = `game`.`game_id` WHERE `round`.`game_id` = :game_id AND `round`.`round_sortkey` = :round_sortkey;");
		$sth -> execute(array('game_id' => $game_id, 'round_sortkey' => $round_sortkey));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new round_model($assoc);
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
		$sth = database::$dbh -> prepare("SELECT `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `round` JOIN `game` ON `round`.`game_id` = `game`.`game_id`" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute();
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new round_model($assoc);
		}
		return $ret;
	}

	/**
	 * List rows by game_id_fk2 index
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function list_by_game_id_fk2($game_id, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `round` JOIN `game` ON `round`.`game_id` = `game`.`game_id` WHERE round.game_id = :game_id" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('game_id' => $game_id));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new round_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within name field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_name($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `round`.`round_id`, `round`.`name`, `round`.`game_id`, `round`.`round_sortkey`, `round`.`round_state`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `round` JOIN `game` ON `round`.`game_id` = `game`.`game_id` WHERE name LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new round_model($assoc);
		}
		return $ret;
	}
	
	/**
	 * Return number of people who have registered on each team this round
	 */
	public function getTeamCounts() {
		$sql = "SELECT game_id, team_id, team_name, (SELECT count(person_id) FROM person_table WHERE person_table.team_id = team.team_id AND round_id = :round_id) AS members FROM team WHERE game_id = :game_id ORDER BY team_name";
		$data = array('round_id' => $this -> get_round_id(), game_id' => $this -> get_game_id());
		$sth = database::$dbh -> prepare($query);
		$sth -> execute($data);
		$rows = $sth -> fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
}
?>