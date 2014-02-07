<div class="main-info">
<h1><?php echo core::escapeHTML($data['round'] -> get_name()); ?></h1>
<?php
if(count($data['round'] -> list_question) == 0) {
	echo "<p>That's the end of the round!</p><p><a class=\"btn btn-lg btn-success\" href=\"".core::constructURL("team", "read", array($data['team'] -> get_team_code()), "html") . "\">Back</a>";
} else {
	$question = array_shift($data['round'] -> list_question);
	echo "<form action=\"".core::constructURL("team", "round", array($data['team'] -> get_team_code(), $data['round'] -> get_round_id()), "html") . "\" method=\"post\">" .
		"<p>Question ".(int)$question -> get_question_sortkey() . "</p>" .
		"<input type=\"hidden\" name=\"question_id\" value=\"".$question -> get_question_id()."\" />" .
		"<p><input class=\"form-control\" type=\"text\" name=\"answer_text\" placeholder=\"Answer\" id=\"ans-box\" autocomplete=\"off\" /></p><p><button type=\"submit\" class=\"btn btn-primary\">Answer</button></p>" .
		"</form>";
}
?>
</div>

<script type="text/javascript">
	$( document ).ready(function() {
	    $('#ans-box').focus();
	});
</script>
