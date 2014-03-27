<div class="main-info">
<h1><?php echo core::escapeHTML($data['team'] -> get_team_name()); ?></h1>
<?php
if(count($data['team'] -> game -> list_round) == 0) {
	echo "<p>No rounds left!</p>";
} else {
	foreach($data['team'] -> game -> list_round as $round) {
		echo "<p><a class=\"btn btn-primary btn-lg\" " .
			"href=\"".core::constructURL("team", "people", array($data['team'] -> get_team_code(), $round -> get_round_id()), 'html')."\">" . 
			core::escapeHTML($round -> get_name())."</a></p>";
		break;
	}
}
?>
</div>
