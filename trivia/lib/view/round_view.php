<?php
class round_view {
	function teams_json($data) {
		echo json_encode($data);
	}
	
	function responses_json($data) {
		echo json_encode($data);
	}
	
	function error_json($data) {
		header("HTTP/1.1 500 Internal Server Error");
		echo json_encode($data);
	}
}


?>
