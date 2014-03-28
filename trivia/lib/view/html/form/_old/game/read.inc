<h1>Trivia game setup</h1>

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
			"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"showTeam('".$team -> get_team_code()."')\"><span class=\"glyphicon glyphicon-qrcode\"></span></button>" .
			"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"alert('Button doesnt do anything yet!');\"><span class=\"glyphicon glyphicon-pencil\"></span></button>" .
			"<button type=\"button\" class=\"btn btn-default btn-xs\" style=\"float: right\" onClick=\"alert('Button doesnt do anything yet!');\"><span class=\"glyphicon glyphicon-trash\"></span></button>" .
				core::escapeHTML($team -> get_team_name()) . "</li>";
		}
	?>
	</ul>
</div>

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

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">Start game</h3>
	</div>
	<div class="panel-body">
		<p>Your game needs an MC to call out the questions, and a Zen Master to correct the answers as they come in.</p>
		<form class="navbar-form navbar-left" role="search">
			<a class="btn btn-success" href="<?php echo core::constructURL("game", "zen", array($data['game'] -> get_game_code()), "html"); ?>">Zen Master view</a>
			<a class="btn btn-primary" href="<?php echo core::constructURL("game", "mc", array($data['game'] -> get_game_code()), "html"); ?>" target="_blank">MC view</a>
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

<div class="modal fade" id="modal-team">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Team Info</h4>
      </div>
      <div class="modal-body" id="modal-team-links">
	&nbsp;
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modal-people">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Add people</h4>
      </div>
      <div class="modal-body">
      	<p>Type people's names in the box below, one per line:</p>
      	<form id="form-people" method="post">
      		<input type="hidden" name="action" value="add-people" />
      		<textarea name="people_names" rows="10" cols="50" width="100%"></textarea>
      	</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onClick="$('#form-people').submit()">Save</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
var game_code = <?php echo json_encode($data['game'] -> get_game_code()); ?>;

function showTeam(team_code) {
	$('#modal-team-links').html('Loading...');
	$('#modal-team').modal();
	$('#modal-team-links').load('/team/qr/' + team_code);
	return false;
}

function delPerson(person_id) {
	$('#person-delete-btn-' + person_id).prop('disabled', true);
	url = <?php echo json_encode(core::constructURL("game", "read", array($data['game'] -> get_game_code()), 'json')); ?>;
	$.post( url, {action: 'delete-person', person_id: person_id})
	.done(function( data ) {
		$('#person-' + person_id).remove();
	})
	.fail(function() {
		alert( "Couldn't remove the person. They have probably already joined a team!" );
		$('#person-delete-btn-' + person_id).prop('disabled', false);
	});

}
</script>
