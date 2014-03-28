<h1><a href="/trivia/game/<?php echo core::escapeHTML($data['game'] -> get_game_code()); ?>"><?php echo core::escapeHTML($data['game'] -> get_game_name()); ?></a></h1>
<div id="errbox" style="display: none" class="alert alert-warning"><span id="errmsg">.</span> <span class="glyphicon glyphicon-remove" style="float: right" onClick="errClose()"></span></div>
<ul id="navbutton" class="nav nav-pills">
	<li id="pill-overview" class="active"><a href="#overview">Setup</a></li>
	<li id="pill-rounds"><a href="#rounds">Questions</a></li>
	<li id="pill-teams"><a href="#teams">Teams</a></li>
	<li id="pill-people"><a href="#people">People</a></li>
</ul>

<div class="page" id="page-overview" style="display: none">
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title">Game Info</h3>
		</div>
		<div class="panel-body">
			<form class="navbar-form" action="" method="post" id="rename-form">
				<div class="form-group">
					<input type="hidden" name="action" value="rename" />
					<input type="text" class="form-control" name="game_name" value="<?php echo $data['game'] -> get_game_name(); ?>" placeholder="Game title" />
				</div>
				<button class="btn btn-default" type="submit" onClick="saveGameName()">Save</button>
			</form>
		</div>
	</div>
	
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Start game</h3>
		</div>
		<div class="panel-body">
			<p>Your game needs an MC to call out the questions, and a Zen Master to correct the answers as they come in.</p>
			<form class="navbar-form navbar-left" role="search">
				<a class="btn btn-success" href="?zen">Zen Master view</a>
				<a class="btn btn-primary" href="?projector" target="_blank">MC view</a>
			</form>
		</div>
	</div>
	
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title">Reset game</h3>
		</div>
		<div class="panel-body">
			<p>Clear out all submitted answers so you can start again.</p>
			<form class="navbar-form navbar-left" action="" method="post">
				<input type="hidden" name="action" value="reset" />
				<button type="submit" class="btn btn-danger" href="<?php echo core::constructURL("game", "zen", array($data['game'] -> get_game_code()), "html"); ?>">Reset Game</button>
			</form>
		</div>
	</div>
</div>

<div class="page" id="page-rounds" style="display: none">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="col-sm-4">
				<ul id="round-list" class="nav nav-pills nav-stacked">
				</ul>
			</div>
			<div class="col-sm-8" id="round-box"></div>
		</div>
	</div>
</div>

<div class="page" id="page-teams" style="display: none">
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
				"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"showTeam('".$team -> get_team_code()."')\"><span class=\"glyphicon glyphicon-qrcode\"></span></button>" .
				"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"alert('Button doesnt do anything yet!');\"><span class=\"glyphicon glyphicon-pencil\"></span></button>" .
				"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"alert('Button doesnt do anything yet!');\"><span class=\"glyphicon glyphicon-trash\"></span></button>" .
					core::escapeHTML($team -> get_team_name()) . "</li>";
			}
		?>
		</ul>
	</div>
</div>

<div class="page" id="page-people" style="display: none">
	<div class="panel panel-info">
		<div class="panel-heading">
			<button type="button" class="btn btn-primary btn-xs" style="float: right" onClick="$('#modal-people').modal();">
				<span class="glyphicon glyphicon glyphicon-plus"></span> Add People
			</button>
			<h3 class="panel-title">People (<?php echo count($data['game'] -> list_person); ?>)</h3>
		</div>
		<ul class="list-group">
		<?php
			foreach($data['game'] -> list_person as $person) {
				echo "<li id=\"person-".$person -> get_person_id()."\" class=\"list-group-item\">" .
				"<button id=\"person-delete-btn-".$person -> get_person_id()."\" type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"delPerson('".$person -> get_person_id()."');\"><span class=\"glyphicon glyphicon-trash\"></span></button>" .
					core::escapeHTML($person -> get_person_name()) . "</li>";
			}
		?>
		</ul>
	</div>
</div>

