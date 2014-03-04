<?php
class team_round_model {
	/**
	 * @var int round_round_id
	 */
	private $round_round_id;

	/**
	 * @var int team_team_id
	 */
	private $team_team_id;

	/**
	 * @var int bonus_points
	 */
	private $bonus_points;

	private $model_variables_changed; // Only variables which have been changed
	private $model_variables_set; // All variables which have been set (initially or with a setter)

	/* Parent tables */
	public $round;
	public $team;

	/**
	 * Initialise and load related tables
	 */
	public static function init() {
		core::loadClass("database");
		core::loadClass("round_model");
		core::loadClass("team_model");
	}

	/**
	 * Construct new team_round from field list
	 * 
	 * @return array
	 */
	public function __construct(array $fields = array()) {
/* Initialise everything as blank to avoid tripping up the permissions fitlers */
		$this -> round_round_id = '';
		$this -> team_team_id = '';
		$this -> bonus_points = '';

		if(isset($fields['team_round.round_round_id'])) {
			$this -> set_round_round_id($fields['team_round.round_round_id']);
		}
		if(isset($fields['team_round.team_team_id'])) {
			$this -> set_team_team_id($fields['team_round.team_team_id']);
		}
		if(isset($fields['team_round.bonus_points'])) {
			$this -> set_bonus_points($fields['team_round.bonus_points']);
		}

		$this -> model_variables_changed = array();
		$this -> round = new round_model($fields);
		$this -> team = new team_model($fields);
	}

	/**
	 * Convert team_round to shallow associative array
	 * 
	 * @return array
	 */
	private function to_array() {
		$values = array(
			'round_round_id' => $this -> round_round_id,
			'team_team_id' => $this -> team_team_id,
			'bonus_points' => $this -> bonus_points);
		return $values;
	}

	/**
	 * Convert team_round to associative array, including only visible fields,
	 * parent tables, and loaded child tables
	 * 
	 * @param string $role The user role to use
	 */
	public function to_array_filtered($role = "anon") {
		if(core::$permission[$role]['team_round']['read'] === false) {
			return false;
		}
		$values = array();
		$everything = $this -> to_array();
		foreach(core::$permission[$role]['team_round']['read'] as $field) {
			if(!isset($everything[$field])) {
				throw new Exception("Check permissions: '$field' is not a real field in team_round");
			}
			$values[$field] = $everything[$field];
		}
		$values['round'] = $this -> round -> to_array_filtered($role);
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
			"team_round.round_round_id" => $row[0],
			"team_round.team_team_id" => $row[1],
			"team_round.bonus_points" => $row[2],
			"round.round_id" => $row[3],
			"round.name" => $row[4],
			"round.game_id" => $row[5],
			"round.round_sortkey" => $row[6],
			"round.round_state" => $row[7],
			"team.team_id" => $row[8],
			"team.team_code" => $row[9],
			"team.game_id" => $row[10],
			"team.team_name" => $row[11],
			"game.game_id" => $row[12],
			"game.game_name" => $row[13],
			"game.game_state" => $row[14],
			"game.game_code" => $row[15]);
		return $values;
	}

	/**
	 * Get round_round_id
	 * 
	 * @return int
	 */
	public function get_round_round_id() {
		if(!isset($this -> model_variables_set['round_round_id'])) {
			throw new Exception("team_round.round_round_id has not been initialised.");
		}
		return $this -> round_round_id;
	}

	/**
	 * Set round_round_id
	 * 
	 * @param int $round_round_id
	 */
	private function set_round_round_id($round_round_id) {
		if(!is_numeric($round_round_id)) {
			throw new Exception("team_round.round_round_id must be numeric");
		}
		$this -> round_round_id = $round_round_id;
		$this -> model_variables_changed['round_round_id'] = true;
		$this -> model_variables_set['round_round_id'] = true;
	}

	/**
	 * Get team_team_id
	 * 
	 * @return int
	 */
	public function get_team_team_id() {
		if(!isset($this -> model_variables_set['team_team_id'])) {
			throw new Exception("team_round.team_team_id has not been initialised.");
		}
		return $this -> team_team_id;
	}

	/**
	 * Set team_team_id
	 * 
	 * @param int $team_team_id
	 */
	private function set_team_team_id($team_team_id) {
		if(!is_numeric($team_team_id)) {
			throw new Exception("team_round.team_team_id must be numeric");
		}
		$this -> team_team_id = $team_team_id;
		$this -> model_variables_changed['team_team_id'] = true;
		$this -> model_variables_set['team_team_id'] = true;
	}

	/**
	 * Get bonus_points
	 * 
	 * @return int
	 */
	public function get_bonus_points() {
		if(!isset($this -> model_variables_set['bonus_points'])) {
			throw new Exception("team_round.bonus_points has not been initialised.");
		}
		return $this -> bonus_points;
	}

	/**
	 * Set bonus_points
	 * 
	 * @param int $bonus_points
	 */
	public function set_bonus_points($bonus_points) {
		if(!is_numeric($bonus_points)) {
			throw new Exception("team_round.bonus_points must be numeric");
		}
		$this -> bonus_points = $bonus_points;
		$this -> model_variables_changed['bonus_points'] = true;
		$this -> model_variables_set['bonus_points'] = true;
	}

	/**
	 * Update team_round
	 */
	public function update() {
		if(count($this -> model_variables_changed) == 0) {
			throw new Exception("Nothing to update");
		}

		/* Compose list of changed fields */
		$fieldset = array();
		$everything = $this -> to_array();
		$data['round_round_id'] = $this -> get_round_round_id();
		$data['team_team_id'] = $this -> get_team_team_id();
		foreach($this -> model_variables_changed as $col => $changed) {
			$fieldset[] = "$col = :$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);

		/* Execute query */
		$sth = database::$dbh -> prepare("UPDATE team_round SET $fields WHERE round_round_id = :round_round_id AND team_team_id = :team_team_id");
		$sth -> execute($data);
	}

	/**
	 * Add new team_round
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
			$fieldset[] = $col;
			$fieldset_colon[] = ":$col";
			$data[$col] = $everything[$col];
		}
		$fields = implode(", ", $fieldset);
		$vals = implode(", ", $fieldset_colon);

		/* Execute query */
		$sth = database::$dbh -> prepare("INSERT INTO team_round ($fields) VALUES ($vals);");
		$sth -> execute($data);
	}

	/**
	 * Delete team_round
	 */
	public function delete() {
		$sth = database::$dbh -> prepare("DELETE FROM team_round WHERE round_round_id = :round_round_id AND team_team_id = :team_team_id");
		$data['round_round_id'] = $this -> get_round_round_id();
		$data['team_team_id'] = $this -> get_team_team_id();
		$sth -> execute($data);
	}

	/**
	 * Retrieve by primary key
	 */
	public static function get($round_round_id, $team_team_id) {
		$sth = database::$dbh -> prepare("SELECT team_round.round_round_id, team_round.team_team_id, team_round.bonus_points, round.round_id, round.name, round.game_id, round.round_sortkey, round.round_state, team.team_id, team.team_code, team.game_id, team.team_name, game.game_id, game.game_name, game.game_state, game.game_code FROM team_round JOIN round ON team_round.round_round_id = round.round_id JOIN team ON team_round.team_team_id = team.team_id JOIN game ON round.game_id = game.game_id WHERE team_round.round_round_id = :round_round_id AND team_round.team_team_id = :team_team_id;");
		$sth -> execute(array('round_round_id' => $round_round_id, 'team_team_id' => $team_team_id));
		$row = $sth -> fetch(PDO::FETCH_NUM);
		if($row === false){
			return false;
		}
		$assoc = self::row_to_assoc($row);
		return new team_round_model($assoc);
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
		if($start > 0 && $limit > 0) {
			$ls = " LIMIT $start, " . ($start + $limit);
		}
		$sth = database::$dbh -> prepare("SELECT team_round.round_round_id, team_round.team_team_id, team_round.bonus_points, round.round_id, round.name, round.game_id, round.round_sortkey, round.round_state, team.team_id, team.team_code, team.game_id, team.team_name, game.game_id, game.game_name, game.game_state, game.game_code FROM team_round JOIN round ON team_round.round_round_id = round.round_id JOIN team ON team_round.team_team_id = team.team_id JOIN game ON round.game_id = game.game_id" . $ls . ";");
		$sth -> execute();
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new team_round_model($assoc);
		}
		return $ret;
	}

	/**
	 * List rows by fk_team_round_team1_idx index
	 * 
	 * @param int $start Row to begin from. Default 0 (begin from start)
	 * @param int $limit Maximum number of rows to retrieve. Default -1 (no limit)
	 */
	public static function list_by_fk_team_round_team1_idx($team_team_id, $start = 0, $limit = -1) {
		$ls = "";
		$start = (int)$start;
		$limit = (int)$limit;
		if($start > 0 && $limit > 0) {
			$ls = " LIMIT $start, " . ($start + $limit);
		}
		$sth = database::$dbh -> prepare("SELECT team_round.round_round_id, team_round.team_team_id, team_round.bonus_points, round.round_id, round.name, round.game_id, round.round_sortkey, round.round_state, team.team_id, team.team_code, team.game_id, team.team_name, game.game_id, game.game_name, game.game_state, game.game_code FROM team_round JOIN round ON team_round.round_round_id = round.round_id JOIN team ON team_round.team_team_id = team.team_id JOIN game ON round.game_id = game.game_id WHERE team_round.team_team_id = :team_team_id" . $ls . ";");
		$sth -> execute(array('team_team_id' => $team_team_id));
		$rows = $sth -> fetchAll(PDO::FETCH_NUM);
		$ret = array();
		foreach($rows as $row) {
			$assoc = self::row_to_assoc($row);
			$ret[] = new team_round_model($assoc);
		}
		return $ret;
	}
}
?>