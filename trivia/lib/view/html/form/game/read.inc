<h1>Trivia game setup</h1>

<div class="panel panel-info">
	<div class="panel-heading">
		<h3 class="panel-title">Game Info</h3>
	</div>
	<div class="panel-body">
		<form class="navbar-form">
			<div class="form-group">
				<input type="text" class="form-control" name="game_name" value="<?php echo $data['game'] -> get_game_name(); ?>" placeholder="Game title" />
			</div>
			<button class="btn btn-default" type="button" onClick="alert('Button doesnt do anything yet!');">Save</button>
		</form>
	</div>
</div>

<div class="panel panel-info">
	<div class="panel-heading">
		<button type="button" class="btn btn-primary btn-xs" style="float: right" onClick="alert('Button doesnt do anything yet!');">
			<span class="glyphicon glyphicon-plus"></span> Add Round
		</button>
		<h3 class="panel-title">Rounds (<?php echo count($data['game'] -> list_round); ?>)</h3>
	</div>
	<ul class="list-group">
	<?php
		foreach($data['game'] -> list_round as $round) {
			echo "<li class=\"list-group-item\">" .
			"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"alert('Button doesnt do anything yet!');\"><span class=\"glyphicon glyphicon-pencil\"></span></button>" .
			"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"alert('Button doesnt do anything yet!');\"><span class=\"glyphicon glyphicon-trash\"></span></button>" .
				core::escapeHTML($round -> get_name()) . "</li>";
		}	
	?>
	</ul>
</div>

<div class="panel panel-info">
	<div class="panel-heading">
		<button type="button" class="btn btn-primary btn-xs" style="float: right" onClick="alert('Button doesnt do anything yet!');">
			<span class="glyphicon glyphicon glyphicon-plus"></span> Add Team
		</button>
		<h3 class="panel-title">Teams (<?php echo count($data['game'] -> list_team); ?>)</h3>
	</div>
	<ul class="list-group">
	<?php
		foreach($data['game'] -> list_team as $team) {
			echo "<li class=\"list-group-item\">" .
			"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"alert('Button doesnt do anything yet!');\"><span class=\"glyphicon glyphicon-qrcode\"></span></button>" .
			"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"alert('Button doesnt do anything yet!');\"><span class=\"glyphicon glyphicon-trash\"></span></button>" .
				core::escapeHTML($team -> get_team_name()) . "</li>";
		}
	?>
	</ul>
</div>

<div class="panel panel-info">
	<div class="panel-heading">
		<button type="button" class="btn btn-primary btn-xs" style="float: right" onClick="alert('Button doesnt do anything yet!');">
			<span class="glyphicon glyphicon glyphicon-plus"></span> Add People
		</button>
		<h3 class="panel-title">People (<?php echo count($data['game'] -> list_person); ?>)</h3>
	</div>
	<ul class="list-group">
	<?php
		foreach($data['game'] -> list_person as $person) {
			echo "<li class=\"list-group-item\">" .
			"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"alert('Button doesnt do anything yet!');\"><span class=\"glyphicon glyphicon-trash\"></span></button>" .
				core::escapeHTML($person -> get_person_name()) . "</li>";
		}
	?>
	</ul>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">Start game</h3>
	</div>
	<div class="panel-body">
		<form class="navbar-form navbar-left" role="search">
			<a class="btn btn-success" href="<?php echo core::constructURL("game", "zen", array($data['game'] -> get_game_code()), "html"); ?>">Zen Master view</a>
			<a class="btn btn-primary" href="<?php echo core::constructURL("game", "mc", array($data['game'] -> get_game_code()), "html"); ?>" target="_blank">MC view</a>
		</form>
	</div>
</div>
