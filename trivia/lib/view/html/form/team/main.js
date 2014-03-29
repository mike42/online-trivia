var team = <?php echo json_encode($data['team']); ?>;
var round = null;
var question = null;

function endGame() {
	// TODO
	showBox('finish');
}

function endRound() {
	showBox('endround');
}

function selectTeam() {
	showBox('team-select');
	$('#people input').removeAttr('checked');
	$('#people-select-round-sortkey').val(round.round_sortkey);
	$('#peopleSearch').focus();
}

function next_round() {
	if(team.game.round.length > 0){
		round = team.game.round.shift();
		question = null;
		selectTeam();
	} else {
		endGame();
	}
}

function next_question() {
	if(round.question.length > 0) {
		showBox('question');
		question = round.question.shift();
		console.log(question);
	} else {
		if(eam.game.round.length > 0) {
			endRound();
		} else {
			endGame();
		}
	}
}

function showBox(box_name) {
	$('#switch-boxes .panel-body').hide();
	$('#box-' + box_name).show();
}

$(function() {
	if(team.game.round.length == 0) {
		endGame();
	}
	
	$('#error-box').on('click', function() {hideError(); });

	$('#ans-form').on('submit', function(e) { //use on if jQuery 1.7+
	    e.preventDefault();  //prevent form from submitting
	    submitAnswer($("#people-select").serialize());
	    return false;
	});
})

$('#peopleSearch').on('input', function() {
	var value = $('#peopleSearch').val().toLowerCase();
	$("#people li").show();
	$("#people > li").filter(
		function() {
			return $(this).text().toLowerCase().indexOf(value) == -1;
		}).hide();
});

$('#savePeople').on('click', function() {
	var datastring = $("#people-select").serialize();
	$.ajax({
        type: "POST",
        url: "/team/" + team.team_code,
        data: datastring,
        dataType: "json",
        success: function(data) {
        	next_question()
        },
        error: function(){
            showError("Couldn't save team list. Try again!");
        }
    });
    return false;
});

function submitAnswer() {
	// TODO
}

function showError(text) {
	$('#error-msg').text(text);
	$('#error-box').show(500);
}

function hideError() {
	$('#error-box').hide(500);
}