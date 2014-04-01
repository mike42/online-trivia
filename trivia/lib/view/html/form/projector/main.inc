<div style="position: absolute; top:0; left: 0;">
	<a class="btn btn-primary btn-sm glyphicon glyphicon-home" href="?"></a><br/>
	<a class="btn btn-primary btn-sm glyphicon glyphicon-refresh" href=""></a><br/>
	<a class="btn btn-primary btn-sm glyphicon glyphicon-list-alt" onClick="return leaderBoard();" href="#"></a><br/>
	<a class="btn btn-info btn-sm glyphicon glyphicon-chevron-left" onClick="return prev();" href="#"></a><br/>
	<a class="btn btn-info btn-sm glyphicon glyphicon-chevron-right" onClick="return next();" href="#"></a><br/>
	<?php
		foreach($data['game'] -> list_round as $round) {
			echo "<a class=\"btn btn-sm btn-success\" onClick=\"setRound(" . ((int)$round -> get_round_sortkey()-1). ")\">" . (int)$round -> get_round_sortkey() . "</a><br/>";
		}
	?>
</div>

<div id="title-pane" class="main-info jumbotron">
	<h1><?php echo core::escapeHTML($data['game'] -> get_game_name()); ?></h1>
	<p class="lead">
		<img src="/public/question-box.png" style="width: 30%"/>
	</p>
</div>

<div id="leaderboard" class="main-info jumbotron" style="display: none">
	<h1>Leader Board</h1>
	<div id="leaderboard-frame"></div>
</div>

<div id="answers" class="main-info jumbotron" style="display: none">
	<h1>Answers</h1>
</div>

<div id="round" class="main-info jumbotron" style="display: none">
	<h1 id="round-title">(Round title)</h1>
	<p id="round-subtitle" class="lead question-text">&nbsp;</p>
	<div class="row" id="team-status">
		
	</div>
</div>
