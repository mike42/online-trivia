<?php
class person_model {
	/**
	 * @var int person_id
	 */
	private $person_id;

	/**
	 * @var string person_name
	 */
	private $person_name;

	/**
	 * @var int game_id
	 */
	private $game_id;

	private $model_variables_changed; // Only variables which have been changed
	private $model_variables_set; // All variables which have been set (initially or with a setter)

	/* Parent tables */
	public $game;

	/* Child tables */
	public $list_person_table;

	/* Sort clause to add when listing rows from this table */
	const SORT_CLAUSE = " ORDER BY `person`.`person_id`";

	/**
	 * Initialise and load related tables
	 */
	public static function init() {
		core::loadClass("database");
		core::loadClass("game_model");

		/* Child tables */
		core::loadClass("person_table_model");
	}

	/**
	 * Construct new person from field list
	 * 
	 * @return array
	 */
	public function __construct(array $fields = array()) {
		/* Initialise everything as blank to avoid tripping up the permissions fitlers */
		$this -> person_id = '';
		$this -> person_name = '';
		$this -> game_id = '';

		if(isset($fields['person.person_id'])) {
			$this -> set_person_id($fields['person.person_id']);
		}
		if(isset($fields['person.person_name'])) {
			$this -> set_person_name($fields['person.person_name']);
		}
		if(isset($fields['person.game_id'])) {
			$this -> set_game_id($fields['person.game_id']);
		}

		$this -> model_variables_changed = array();
		$this -> game = new game_model($fields);
		$this -> list_person_table = array();
	}

	/**
	 * Convert person to shallow associative array
	 * 
	 * @return array
	 */
	private function to_array() {
		$values = array(
			'person_id' => $this -> person_id,
			'person_name' => $this -> person_name,
			'game_id' => $this -> game_id);
		return $values;
	}

	/**
	 * Convert person to associative array, including only visible fields,
	 * parent tables, and loaded child tables
	 * 
	 * @param string $role The user role to use
	 */
	public function to_array_filtered($role = "anon") {
		if(core::$permission[$role]['person']['read'] === false) {
			return false;
		}
		$values = array();
		$everything = $this -> to_array();
		foreach(core::$permission[$role]['person']['read'] as $field) {
			if(!isset($everything[$field])) {
				throw new Exception("Check permissions: '$field' is not a real field in person");
			}
			$values[$field] = $everything[$field];
		}
		$values['game'] = $this -> game -> to_array_filtered($role);

		/* Add filtered versions of everything that's been loaded */
		$values['person_table'] = array();
		foreach($this -> list_person_table as $person_table) {
			$values['person_table'][] = $person_table -> to_array_filtered($role);
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
			"person.person_id" => $row[0],
			"person.person_name" => $row[1],
			"person.game_id" => $row[2],
			"game.game_id" => $row[3],
			"game.game_name" => $row[4],
			"game.game_state" => $row[5],
			"game.game_code" => $row[6]);
		return $values;
	}

	/**
	 * Get person_id
	 * 
	 * @return int
	 */
	public function get_person_id() {
		if(!isset($this -> model_variables_set['person_id'])) {
			throw new Exception("person.person_id has not been initialised.");
		}
		return $this -> person_id;
	}

	/**
	 * Set person_id
	 * 
	 * @param int $person_id
	 */
	private function set_person_id($person_id) {
		if(!is_numeric($person_id)) {
			throw new Exception("person.person_id must be numeric");
		}
		$this -> person_id = $person_id;
		$this -> model_variables_changed['person_id'] = true;
		$this -> model_variables_set['person_id'] = true;
	}

	/**
	 * Get person_name
	 * 
	 * @return string
	 */
	public function get_person_name() {
		if(!isset($this -> model_variables_set['person_name'])) {
			throw new Exception("person.person_name has not been initialised.");
		}
		return $this -> person_name;
	}

	/**
	 * Set person_name
	 * 
	 * @param string $person_name
	 */
	public function set_person_name($person_name) {
		// TODO: Add TEXT validation to person.person_name
		$this -> person_name = $person_name;
		$this -> model_variables_changed['person_name'] = true;
		$this -> model_variables_set['person_name'] = true;
	}

	/**
	 * Get game_id
	 * 
	 * @return int
	 */
	public function get_game_id() {
		if(!isset($this -> model_variables_set['game_id'])) {
			throw new Exception("person.game_id has not been initialised.");
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
			throw new Exception("person.game_id must be numeric");
		}
		$this -> game_id = $game_id;
		$this -> model_variables_changed['game_id'] = true;
		$this -> model_variables_set['game_id'] = true;
	}

	/**
	 * Update person
	 */
	public function update() {
		if(count($this -> model_variables_changed) == 0) {
			throw new Exception("Nothing to update");
		}

		/* Compose list of changed fields */
		$fieldset = array();
		$everything = $this -> to_array();
		$data['person_id'] = $this -> get_person_id();
		foreach($this -> model_variables_changed as $col => $changed) {
			$fieldset[] = "`$col` = :$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);

		/* Execute query */
		$sth = database::$dbh -> prepare("UPDATE `person` SET $fields WHERE `person`.`person_id` = :person_id");
		$sth -> execute($data);
	}

	/**
	 * Add new person
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
		$sth = database::$dbh -> prepare("INSERT INTO `person` ($fields) VALUES ($vals);");
		$sth -> execute($data);
		$this -> set_person_id(database::$dbh->lastInsertId());
	}

	/**
	 * Delete person
	 */
	public function delete() {
		$sth = database::$dbh -> prepare("DELETE FROM `person` WHERE `person`.`person_id` = :person_id");
		$data['person_id'] = $this -> get_person_id();
		$sth -> execute($data);
	}

	/**
	 * List associated rows from person_table table
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public function populate_list_person_table($start = 0, $limit = -1) {
		$person_id = $this -> get_person_id();
		$this -> list_person_table = person_table_model::list_by_person_id_fk1($person_id, $start, $limit);
	}

	/**
	 * Retrieve by primary key
	 */
	public static function get($person_id) {
		$sth = database::$dbh -> prepare("SELECT `person`.`person_id`, `person`.`person_name`, `person`.`game_id`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM person JOIN `game` ON `person`.`game_id` = `game`.`game_id` WHERE `person`.`person_id` = :person_id;");
		$sth -> execute(array('person_id' => $person_id));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new person_model($assoc);
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
		$sth = database::$dbh -> prepare("SELECT `person`.`person_id`, `person`.`person_name`, `person`.`game_id`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `person` JOIN `game` ON `person`.`game_id` = `game`.`game_id`" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute();
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new person_model($assoc);
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
		$sth = database::$dbh -> prepare("SELECT `person`.`person_id`, `person`.`person_name`, `person`.`game_id`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `person` JOIN `game` ON `person`.`game_id` = `game`.`game_id` WHERE person.game_id = :game_id" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('game_id' => $game_id));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new person_model($assoc);
		}
		return $ret;
	}

	/**
	 * Simple search within person_name field
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function search_by_person_name($search, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start >= 0 && $limit > 0) {
			$ls = " LIMIT $start, $limit";
		}
		$sth = database::$dbh -> prepare("SELECT `person`.`person_id`, `person`.`person_name`, `person`.`game_id`, `game`.`game_id`, `game`.`game_name`, `game`.`game_state`, `game`.`game_code` FROM `person` JOIN `game` ON `person`.`game_id` = `game`.`game_id` WHERE person_name LIKE :search" . self::SORT_CLAUSE . $ls . ";");
		$sth -> execute(array('search' => "%".$search."%"));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new person_model($assoc);
		}
		return $ret;
	}
}
?>