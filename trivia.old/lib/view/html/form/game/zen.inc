<?php
$prev=-1;

if(count($data['answers']) != 0) {
	foreach($data['answers'] as $answer) {
		$cur = $answer -> question -> get_question_id();
		if($cur != $prev) {
			if($prev != -1) {
				echo "</ul>";
			}
			echo "<h2>Round " . $answer -> question -> round -> get_round_sortkey() . ", Q" . $answer -> question -> get_question_sortkey() . "</h2>" . 
				"<p>Q. ".core::escapeHTML($answer -> question -> get_question_text()) . "</p><ul>";
			$prev = $cur;
		}
		echo "<li id=\"answer-t".$answer -> get_team_id() . "-q" . $answer -> get_question_id() . "\">" .
		" <a href=\"#\" class=\"btn-xs btn-success\" onClick=\"mark(".$answer -> get_team_id() . "," . $answer -> get_question_id() . ", 2);\"><span class=\"glyphicon glyphicon-ok\"> </span> Yes</a>" .
		" <a href=\"#\" class=\"btn-xs btn-danger\" onClick=\"mark(".$answer -> get_team_id() . "," . $answer -> get_question_id() . ", 1);\"><span class=\"glyphicon glyphicon-remove\"> </span> No</a>" .
		" &nbsp; &nbsp; \"" . core::escapeHTML($answer -> get_answer_text()) . "\"" .
		"</li>";
	}
	echo "</ul>";
}


?>
<p>
<a href="<?php echo core::constructURL("game", "zen", array($data['game'] -> get_game_code()), "html"); ?>" class="btn btn-success"><span class="glyphicon glyphicon-refresh"></span> Check for new answers</a>
<a href="<?php echo core::constructURL("game", "read", array($data['game'] -> get_game_code()), "html"); ?>" class="btn btn-default"><span class="glyphicon glyphicon-home"></span> Go back</a>
</p>


<script type="text/javascript">
function mark(team_id, question_id, answer_is_correct) {
	$('#answer-t' + team_id + '-q' + question_id).hide()
	url = <?php echo json_encode(core::constructURL("game", "zen", array($data['game'] -> get_game_code()), 'json')); ?>;
	$.post( url, {action: 'correct', team_id: team_id, question_id: question_id, answer_is_correct: answer_is_correct})
	.done(function( data ) {
		$('#answer-t' + team_id + '-q' + question_id).remove();
	})
	.fail(function() {
		alert( "Correction could not be saved!" );
		$('#answer-t' + team_id + '-q' + question_id).show();
	});
}
</script>
