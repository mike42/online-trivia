<?php
$count = 1;

if(count($data['leaderboard']) == 0) {
	echo "<h2>No winner yet!</h2>";
} else {
	foreach($data['leaderboard'] as $leader) {
		echo "<h2>#" . $count . ". " . core::escapeHTML($leader['person_name']) . " &mdash; " . $leader['score'] . "</h2>";
		$count++;
		if($count > 7) {
			break;
		}
	}
}
?>
