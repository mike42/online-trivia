<?php
class session_controller {
	public static function init() {
		core::loadClass("session");
	}
	
	public static function login() {
		if(!isset($_POST['username']) || !isset($_POST['password'])) {
			return array('error' => 'No login details provided', 'code' => '403');
		}
		
		/* Get username & password */
		$username = $_POST['username'];
		$password = $_POST['password'];
		$ok = session::authenticate($username, $password);
		if($ok) {
			return array('success' => 'true', 'username' => $username, 'role' => session::getRole());
		}
		return array('error' => 'Login failed', 'code' => '403');
	}
	
	public static function logout() {
		session::logout();
		return array('success' => 'true'); // Log-out will always succeed
	}
}