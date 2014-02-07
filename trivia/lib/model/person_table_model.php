<?php
class person_table_model {
	/**
	 * @var int round_id
	 */
	private $round_id;

	/**
	 * @var int person_id
	 */
	private $person_id;

	/**
	 * @var int team_id
	 */
	private $team_id;

	private $model_variables_changed; // Only variables which have been changed
	private $model_variables_set; // All variables which have been set (initially or with a setter)

	/* Parent tables */
	public $round;
	public $person;
	public $team;

	/**
	 * Construct new person_table from field list
	 * 
	 * @return array
	 */
	public function __construct(array $fields = array()) {
		if(isset($fields['person_table.round_id'])) {
			$this -> set_round_id($fields['person_table.round_id']);
		}
		if(isset($fields['person_table.person_id'])) {
			$this -> set_person_id($fields['person_table.person_id']);
		}
		if(isset($fields['person_table.team_id'])) {
			$this -> set_team_id($fields['person_table.team_id']);
		}

		$this -> model_variables_changed = array();
		$this -> round = new round_model($fields);
		$this -> person = new person_model($fields);
		$this -> team = new team_model($fields);
	}

	/**
	 * Convert person_table to shallow associative array
	 * 
	 * @return array
	 */
	private function to_array() {
		$values = array(
			'round_id' => $this -> round_id,
			'person_id' => $this -> person_id,
			'team_id' => $this -> team_id);
		return $values;
	}

	/**
	 * Convert person_table to associative array, including only visible fields,
	 * parent tables, and loaded child tables
	 * 
	 * @param string $role The user role to use
	 */
	public function to_array_filtered($role = "anon") {
		// TODO: Insert code for person_table permission-check
	}

	/**
	 * Convert retrieved database row from numbered to named keys, including table name
	 * 
	 * @param array $row ror retrieved from database
	 * @return array row with indices
	 */
	private static function row_to_assoc(array $row) {
		$values = array(
			"person_table.round_id" => $row[0],
			"person_table.person_id" => $row[1],
			"person_table.team_id" => $row[2],
			"round.round_id" => $row[3],
			"round.name" => $row[4],
			"round.game_id" => $row[5],
			"round.round_sortkey" => $row[6],
			"round.round_state" => $row[7],
			"person.person_id" => $row[8],
			"person.person_name" => $row[9],
			"person.game_id" => $row[10],
			"team.team_id" => $row[11],
			"team.team_code" => $row[12],
			"team.game_id" => $row[13],
			"team.team_name" => $row[14],
			"game.game_id" => $row[15],
			"game.game_name" => $row[16],
			"game.game_state" => $row[17],
			"game.game_code" => $row[18]);
		return $values;
	}

	/**
	 * Get round_id
	 * 
	 * @return int
	 */
	public function get_round_id() {
		if(!isset($this -> model_variables_set['round_id'])) {
			throw new Exception("person_table.round_id has not been initialised.");
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
			throw new Exception("person_table.round_id must be numeric");
		}
		$this -> round_id = $round_id;
		$this -> model_variables_changed['round_id'] = true;
		$this -> model_variables_set['round_id'] = true;
	}

	/**
	 * Get person_id
	 * 
	 * @return int
	 */
	public function get_person_id() {
		if(!isset($this -> model_variables_set['person_id'])) {
			throw new Exception("person_table.person_id has not been initialised.");
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
			throw new Exception("person_table.person_id must be numeric");
		}
		$this -> person_id = $person_id;
		$this -> model_variables_changed['person_id'] = true;
		$this -> model_variables_set['person_id'] = true;
	}

	/**
	 * Get team_id
	 * 
	 * @return int
	 */
	public function get_team_id() {
		if(!isset($this -> model_variables_set['team_id'])) {
			throw new Exception("person_table.team_id has not been initialised.");
		}
		return $this -> team_id;
	}

	/**
	 * Set team_id
	 * 
	 * @param int $team_id
	 */
	public function set_team_id($team_id) {
		if(!is_numeric($team_id)) {
			throw new Exception("person_table.team_id must be numeric");
		}
		$this -> team_id = $team_id;
		$this -> model_variables_changed['team_id'] = true;
		$this -> model_variables_set['team_id'] = true;
	}

	/**
	 * Update person_table
	 */
	public function update() {
		if(count($this -> model_variables_changed) == 0) {
			throw new Exception("Nothing to update");
		}

		/* Compose list of changed fields */
		$fieldset = array();
		$everything = $this -> to_array();
		$data['round_id'] = $this -> get_round_id();
		$data['person_id'] = $this -> get_person_id();
		foreach($this -> model_variables_changed as $col => $changed) {
			$fieldset[] = "$col = :$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);

		/* Execute query */
		$sth = database::$dbh -> prepare("UPDATE person_table SET $fields WHERE round_id = :round_id AND person_id = :person_id");
		$sth -> execute($data);
	}

	/**
	 * Add new person_table
	 */
	public function insert() {
		if(count($this -> model_variables_changed) == 0) {
			throw new Exception("No fields have been set!");
		}

		/* Compose list of set fields */
		$fieldset = array();
		$data = array();
		$everything = $this -> to_array();
		foreach($this -> model_variables_set as $col => $changed) {
			$fieldset[] = $col;
			$fieldset_colon[] = ":$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);
		$vals = implode(", ", $fieldset_colon);

		/* Execute query */
		$sth = database::$dbh -> prepare("INSERT INTO person_table ($fields) VALUES ($vals);");
		$sth -> execute($data);
	}

	/**
	 * Delete person_table
	 */
	public function delete() {
		$sth = database::$dbh -> prepare("DELETE FROM person_table WHERE round_id = :round_id AND person_id = :person_id");
		$data['round_id'] = $this -> get_round_id();
		$data['person_id'] = $this -> get_person_id();
		$sth -> execute($data);
	}

	/**
	 * Retrieve by primary key
	 */
	public static function get($round_id, $person_id) {
		$sth = database::$dbh -> prepare("SELECT person_table.round_id, person_table.person_id, person_table.team_id, round.round_id, round.name, round.game_id, round.round_sortkey, round.round_state, person.person_id, person.person_name, person.game_id, team.team_id, team.team_code, team.game_id, team.team_name, game.game_id, game.game_name, game.game_state, game.game_code FROM person_table JOIN round ON person_table.round_id = round.round_id JOIN person ON person_table.person_id = person.person_id JOIN team ON person_table.team_id = team.team_id JOIN game ON round.game_id = game.game_id WHERE person_table.round_id = :round_id AND person_table.person_id = :person_id;");
		$sth -> execute(array('round_id' => $round_id, 'person_id' => $person_id));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new person_table_model($assoc);
	}

	/**
	 * List rows by person_id_fk1 index
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function list_by_person_id_fk1($person_id, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start > 0 && $limit > 0) {
			$ls = " LIMIT $start, " . ($start + $limit);
		}
		$sth = database::$dbh -> prepare("SELECT person_table.round_id, person_table.person_id, person_table.team_id, round.round_id, round.name, round.game_id, round.round_sortkey, round.round_state, person.person_id, person.person_name, person.game_id, team.team_id, team.team_code, team.game_id, team.team_name, game.game_id, game.game_name, game.game_state, game.game_code FROM person_table JOIN round ON person_table.round_id = round.round_id JOIN person ON person_table.person_id = person.person_id JOIN team ON person_table.team_id = team.team_id JOIN game ON round.game_id = game.game_id WHERE person_table.person_id = :person_id" . $ls . ";");
		$sth -> execute(array('person_id' => $person_id));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new person_table_model($assoc);
		}
		return $ret;
	}

	/**
	 * List rows by team_id_fk1 index
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function list_by_team_id_fk1($team_id, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start > 0 && $limit > 0) {
			$ls = " LIMIT $start, " . ($start + $limit);
		}
		$sth = database::$dbh -> prepare("SELECT person_table.round_id, person_table.person_id, person_table.team_id, round.round_id, round.name, round.game_id, round.round_sortkey, round.round_state, person.person_id, person.person_name, person.game_id, team.team_id, team.team_code, team.game_id, team.team_name, game.game_id, game.game_name, game.game_state, game.game_code FROM person_table JOIN round ON person_table.round_id = round.round_id JOIN person ON person_table.person_id = person.person_id JOIN team ON person_table.team_id = team.team_id JOIN game ON round.game_id = game.game_id WHERE person_table.team_id = :team_id" . $ls . ";");
		$sth -> execute(array('team_id' => $team_id));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new person_table_model($assoc);
		}
		return $ret;
	}
	
	public static function count_by_round($round_id) {
		$sth = database::$dbh -> prepare("SELECT team_id, count(person_id) as people FROM person_table WHERE round_id = :round_id GROUP BY team_id;");
		$sth -> execute(array('round_id' => $round_id));
		$rows = $sth -> fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
}
?>
