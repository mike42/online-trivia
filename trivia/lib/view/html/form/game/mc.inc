<div style="position: absolute; top:0; left: 0;">
	<a class="btn btn-primary btn-sm glyphicon glyphicon-refresh" href=""></a><br/>
	<a class="btn btn-primary btn-sm glyphicon glyphicon-list-alt" onClick="leaderBoard();" href="#"></a><br/>
	<a class="btn btn-info btn-sm glyphicon glyphicon-chevron-left" onClick="prev();" href="#"></a><br/>
	<a class="btn btn-info btn-sm glyphicon glyphicon-chevron-right" onClick="next();" href="#"></a><br/>
	<?php
		foreach($data['game'] -> list_round as $round) {
			echo "<a class=\"btn btn-sm btn-success\" onClick=\"setRound(" . (int)$round -> get_round_sortkey() . ")\">" . (int)$round -> get_round_sortkey() . "</a><br/>";
		}
	?>
</div>

<div id="title-pane" class="main-info jumbotron">
	<h1><?php echo core::escapeHTML($data['game'] -> get_game_name()); ?></h1>
	<p class="lead">
		<img src="/assets/question-box.png" style="width: 30%"/>
	</p>
</div>

<div id="leaderboard" class="main-info jumbotron" style="display: none">
<h1>Leader Board</h1>
...
</div>

<div id="round" class="main-info jumbotron" style="display: none">
	<h1 id="round-title">(Round title)</h1>
	<p id="round-subtitle" class="lead">&nbsp;</p>
	<div class="row">
		<div class="col-sm-6">
		<?php
			$odd = $even = array();
			$is_odd = true;
			foreach($data['game'] -> list_team as $team) {
				if($is_odd) {
					$odd[] = $team;
				} else {
					$even[] = $team;
				}
				$is_odd = !$is_odd;
			}
			
			foreach($odd as $team) {
				echo "<h3><span id=\"team-".(int)$team -> get_team_id()."\" class=\"label label-default\">0</span> ".$team -> get_team_name() ."</h3>\n";
			}
		?>
		</div>
		<div class="col-sm-6">
		<?php
			foreach($even as $team) {
				echo "<h3><span id=\"team-".(int)$team -> get_team_id()."\" class=\"label label-default\">0</span> ".$team -> get_team_name() ."</h3>\n";
			}
		?>
		</div>
	</div>
</div>
<?php
$rounds = array();
foreach($data['game'] -> list_round as $round) {
	$questions = array();
	foreach($round -> list_question as $question) {
		$questions[$question -> get_question_sortkey()] = array('id' => $question -> get_question_id(), 'text' => $question -> get_question_text());
	}
	$rounds[$round -> get_round_sortkey()] = array('id' => $round -> get_round_id(), 'name' => $round -> get_name(), 'question' => $questions);
}

$teams = array();
foreach($data['game'] -> list_team as $team) {
	$teams[$team -> get_team_id()] = array('id' => $team -> get_team_id(), 'name' => $team -> get_team_name());
}

?>

<script type="text/javascript">
var round = <?php echo json_encode($rounds); ?>;
var team = <?php echo json_encode($teams); ?>;
var game_id = '<?php echo $data['game'] -> get_game_code() ?>';
var active='title-pane';
var cur_round=1;
var cur_q=0;
var t;
var timer_is_on = 0;

function prev() {
	if(active == 'title-pane') {
		
	} else if(active == 'leaderboard') {
		setRound(cur_round);
	} else if(active == 'round') {
		if(round[cur_round]['question'][cur_q - 1] != undefined) {
			setQuestion(cur_q - 1);
		} else if(cur_q - 1 == 0) {
			setRound(cur_round);
		} else if(round[cur_round - 1] != undefined){
			setRound(cur_round - 1);
		} else {
			activate('title-pane');
		}
	}
	return false;
}

function next() {
	if(active == 'title-pane') {
		setRound(1);
	} else if(active == 'leaderboard') {
		setRound(cur_round);
	} else if(active == 'round') {
		if(round[cur_round]['question'][cur_q + 1] != undefined) {
			setQuestion(cur_q + 1);
		} else if(round[cur_round + 1] != undefined){
			setRound(cur_round + 1);
		} else {
			activate('leaderboard');
		}
	}
	return false;
}

function setRound(num) {
	cur_round=num;
	cur_q=0;
	$('#round-title').text(round[num]['name']);
	activate('round');
	update();
	return false;
}

function setQuestion(q) {
	cur_q = q;
	$('#round-title').text(q + ". " + round[cur_round]['question'][cur_q]['text']);
	update();
	return false;
}

function leaderBoard() {
	activate('leaderboard');
	return false;
}

function activate(id) {
	if(active != id) {
		$('#' + active).hide();
		$('#' + id).show();
		active=id;
	}
	return false;
}

function update() {
	if(active == 'round') {
		if(cur_q == 0) {
			url='index.php?p=round/teams/' + round[cur_round]['id'] + '.json';
		} else {
			url='index.php?p=round/responses/' + round[cur_round]['id'] + '/' + round[cur_round]['question'][cur_q]['id'] + '.json';
		}
		var jqxhr = $.ajax({url: url, timeout: 5, dataType: "json"})
		.done(function(data) {
			$.each(data[0], function(key, val) {
				if(cur_q == 0) {
					$('#team-'+key).text(val);
				} else {
					if(val == 0) {
						$('#team-'+key).text('...');
					} else {
						$('#team-'+key).text('OK');
					}
				}
				$('#team-'+key).toggleClass( 'label-default', val == 0);
				$('#team-'+key).toggleClass( 'label-primary', val != 0);
			});
			update_repeat(2000);
		})
		.fail(function() {
			update_repeat(1000);
		})
		.always(function() {
			//
		});
	}
	return false;
}

function update_repeat(delay) {
	if(timer_is_on == 1) {
		clearTimeout(t);
		timer_is_on=0;
	}
	
	if(active == 'round') {
		timer_is_on=1;
		t = setTimeout(update, delay);
	}
}
</script>
