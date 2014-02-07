<?php
class game_view {
	public static function create_html($data) {
		throw new Exception("Not implemented");
	}

	public static function read_html($data) {
		$data['layout'] = 'htmlLayout';
		$data['template'] = 'game/read';
		core::showHTML($data);	
	}
	
	public static function read_json($data) {
		echo json_encode($data);
	}
	
	public static function zen_json($data) {
		echo json_encode($data);
	}
	
	public static function zen_html($data) {
		$data['layout'] = 'htmlLayout';
		$data['template'] = 'game/zen';
		core::showHTML($data);	
	}
	
	public static function mc_html($data) {
		$data['layout'] = 'htmlLayout';
		$data['template'] = 'game/mc';
		core::showHTML($data);	
	}
	
	public static function leaderboard_html($data) {
		$data['layout'] = 'htmlLayout';
		$data['template'] = 'game/leaderboard';
		core::showHTML($data);	
	}

	public static function update_html($data) {
		throw new Exception("Not implemented");
	}

	public static function delete_html($data) {
		throw new Exception("Not implemented");
	}
	
	public static function error_html($data) {
		$data['layout'] = 'htmlLayout';
		$data['template'] = 'error';
		core::showHTML($data);
	}
	
	public static function error_json($data) {
		header("HTTP/1.1 500 Internal Server Error");
		echo json_encode($data);
	}
}
