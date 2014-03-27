<?php 
	echo "<form action=\"".core::constructURL("team", "people", array($data['team'] -> get_team_code(), $data['round'] -> get_round_id()), 'html')."\" method=\"post\">";
?>
<div class="panel panel-info">
	<div class="panel-heading">
		<h3 class="panel-title">Who is on your team?</h3>
	</div>
	<ul class="list-group">
	<?php
		foreach($data['people'] as $person) {
			echo "<li class=\"list-group-item\"><label><input type=\"checkbox\" name=\"person-" . $person -> get_person_id() . "\"> " . core::escapeHTML( $person -> get_person_name() ) . "</label></li>";
		}			
	?>
	<li class="list-group-item">
		<button type="submit" class="btn btn-primary">Save</button>
	</li>
	</ul>
</div>
<?php
	echo "</form>";
?>

