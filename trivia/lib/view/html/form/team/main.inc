<div class="main-info">
	<h1>
		<?php echo core::escapeHTML($data['team']['team_name']); ?>
	</h1>

	<div id="switch-boxes" class="panel panel-default">
		<div class="panel-heading">
			<?php echo core::escapeHTML($data['team']['game']['game_name']); ?>
		</div>
		<div id="error-box" class="panel-body" style="display: none">
			<div id="error-msg" class="alert alert-warning">...</div>
		</div>
		<div id="box-welcome" class="panel-body">
			<p>
				Welcome to <b><?php echo core::escapeHTML($data['team']['game']['game_name']); ?>
				</b>.
			</p>
			<p>Each round you will have different team members.</p>
			<p>When you are ready, press the button below to start the first
				round:</p>

			<a class="btn btn-primary btn-lg" href="#"
				onClick="return next_round()">We're ready</a>
		</div>
		<div id="box-team-select" class="panel-body" style="display: none">
			<div class="form-group">
				<label>Who is on your team?</label> <input type="text"
					class="form-control" placeholder="Quick search" id="peopleSearch" />

				<form id="people-select">
					<input type="hidden" id="people-select-round-sortkey" name="round_sortkey" />
					<input type="hidden" name="action" value="people" />
					<ul id="people" style="list-style-type: none;">
						<?php 
						foreach($data['team']['game']['person'] as $person) {
							echo "<li><label class=\"checkbox\">" . core::escapeHTML($person['person_name']) . " <input type=\"checkbox\" name=\"person-".core::escapeHTML($person['person_id'])."\"/></label></li>";
						}
						?>
					</ul>
				</form>

				<button id="savePeople" class="btn btn-default">Continue &rarr;</button>
			</div>
		</div>
		<div id="box-finish" class="panel-body" style="display: none">
			<p>You've reached the end! See you next time.</p>
		</div>
		<div id="box-question" class="panel-body" style="display: none; text-align: center;">
			<h2>..</h2>
			<form id="ans-form">
				<p><input class="form-control" type="text" name="answer_text" placeholder="Answer" id="ans-box" autocomplete="off" /></p>
				<p><button type="submit" class="btn btn-primary">Answer</button></p>
			</form>
		</div>
		<div id="box-endround" class="panel-body" style="display: none">
			<p>Round is over.</p>
			<a class="btn btn-primary btn-lg" href="#"
				onClick="return next_round()">Next</a>
		</div>
	</div>
</div>

