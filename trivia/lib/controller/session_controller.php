<?php
class session_controller {
	public static function init() {
		core::loadClass("session");
	}
	
	public static function logout() {
		session::logout();
		return array('success' => 'true'); // Log-out will always succeed
	}
}