<h1>
	<a
		href="/trivia/game/<?php echo core::escapeHTML($data['game'] -> get_game_code()); ?>"><?php echo core::escapeHTML($data['game'] -> get_game_name()); ?>
	</a>
</h1>
<div id="errbox" style="display: none" class="alert alert-warning">
	<span id="errmsg">.</span> <span class="glyphicon glyphicon-remove"
		style="float: right" onClick="errClose()"></span>
</div>
<ul id="navbutton" class="nav nav-pills">
	<li id="pill-overview" class="active"><a href="#overview">Setup</a></li>
	<li id="pill-rounds"><a href="#rounds">Questions</a></li>
	<li id="pill-teams"><a href="#teams">Teams</a></li>
	<li id="pill-people"><a href="#people">People</a></li>
</ul>

<div class="page" id="page-overview" style="display: none">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Game setup</h3>
		</div>
		<div class="panel-body">
			<form class="navbar-form" action="" method="post" id="rename-form">
				<div class="form-group">
					<input type="hidden" name="action" value="rename" /> <input
						type="text" class="form-control" name="game_name"
						value="<?php echo $data['game'] -> get_game_name(); ?>"
						placeholder="Game title" />
				</div>
				<button class="btn btn-default" type="submit"
					onClick="saveGameName()">Save</button>
			</form>

			<p>Your game needs an MC to call out the questions, and a Zen Master
				to correct the answers as they come in.</p>
			<form class="navbar-form navbar-left">
				<a class="btn btn-success" href="?zen">Zen Master view</a> <a
					class="btn btn-primary" href="?projector" target="_blank">MC view</a>
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
				<button type="submit" class="btn btn-danger">Reset Game</button>
			</form>
		</div>
	</div>
</div>

<div class="page" id="page-rounds" style="display: none">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="col-sm-4 questions-left">
				<ul id="round-list" class="nav nav-pills nav-stacked">
				</ul>
			</div>
			<div class="col-sm-8 questions-right" id="round-box"></div>
		</div>
	</div>
</div>

<div class="page" id="page-teams" style="display: none">
	<div class="panel panel-default">
		<div class="panel-heading">
			<button type="button" class="btn btn-default btn-xs"
				style="float: right" onClick="addTeam();">
				<span class="glyphicon glyphicon glyphicon-plus"></span> Add Team
			</button>
			<h3 class="panel-title">
				Teams
			</h3>
		</div>
		<ul class="list-group" id="team-list">
		</ul>
	</div>
</div>

<div class="page" id="page-people" style="display: none">
	<div class="panel panel-default">
		<div class="panel-heading">
			<button type="button" class="btn btn-default btn-xs"
				style="float: right" onClick="addPerson();">
				<span class="glyphicon glyphicon glyphicon-plus"></span> Add People
			</button>
			<h3 class="panel-title">
				People
			</h3>
		</div>
		<ul class="list-group" id="person-list">
		</ul>
	</div>
</div>

<div class="modal fade" id="addPerson" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add People</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label>Names, one per line</label>
						<textarea class="form-control" id="addPersonName" placeholder="Name"></textarea>
							
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onClick="addPeopleSave();">Save changes</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="addTeam" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Team</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label>Team Name</label>
						<input type="text" class="form-control" id="addTeamName" placeholder="Team Name" />
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onClick="addTeamSave();">Save changes</button>
			</div>
		</div>
	</div>
</div>

