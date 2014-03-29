<?php
class session {
	/**
	 * Open the session
	 */
	public static function init() {
		session_start();
	}

	/**
	 * Get the role of the current user, or 'anon' if they aren't logged in.
	 * 
	 * @return string Name of the user's current role
	 */
	public static function getRole() {
		if(isset($_SESSION['role'])) {
			return $_SESSION['role'];
		}
		return "anon";
	}

	/**
	 * End the session
	 */
	public static function logout() {
		session_destroy();
	}
	
	public static function game_login($game_code) {
		core::loadClass("game_model");
		if($game = game_model::get_by_game_code($game_code)) {
			$_SESSION['role'] = 'user';
			$_SESSION['game_id'] = $game -> get_game_id();
		}
	}
	
	public static function team_login($team_code) {
		if(self::getRole() == 'user') {
			/* Don't demote a user who is poking around on the team pages */
			return;
		}
		core::loadClass("team_model");
		if($team = game_model::get_by_game_code($team_code)) {
			$_SESSION['role'] = 'team';
			$_SESSION['team_id'] = $team -> get_team_id();
		}
	}
	
	public static function is_game_master($game_id) {
		if(self::getRole() == "user") {
			return $game_id == $_SESSION['game_id'];
		}
		return false;
	}
	
	public static function is_game_member($game_id) {
		if(self::is_game_master($game_id)) {
			return true;
		}
		if(self::getRole() == "team" && $team = team_model::get($_SESSION['team_id'])) {
			return $team -> get_game_id() == $game_id;
		}
		return false;
	}
	
	public static function is_team_member($team_id) {
		if(self::getRole() == "team") {
			return $team_id == $_SESSION['team_id'];
		} else if(self::getRole() == "user" && $team = team_model::get($team_id)) {
			return $team -> get_game_id() == $_SESSION['game_id'];
		}
		return false;
	}
}
