<h1>
	<a
		href="/game/<?php echo core::escapeHTML($data['game'] -> get_game_code()); ?>"><?php echo core::escapeHTML($data['game'] -> get_game_name()); ?>
	</a>
</h1>

<div id="errbox" style="display: none" class="alert alert-warning">
	<span id="errmsg">.</span> <span class="glyphicon glyphicon-remove"
		style="float: right" onClick="errClose()"></span>
</div>

<div class="panel panel-success">
	<div class="panel-heading">
		<h3 class="panel-title">
			<?php echo core::escapeHTML($data['game'] -> get_game_name()) ?> (Zen Master view)
		</h3>
	</div>
	<div class="panel-body">
		<div class="row question-row">
			<div class="col-sm-4 questions-left">
				<ul id="round-list" class="nav nav-pills nav-stacked">
				</ul>
			</div>
			<div class="col-sm-8 questions-right" id="round-box"></div>
		</div>
	</div>
</div>