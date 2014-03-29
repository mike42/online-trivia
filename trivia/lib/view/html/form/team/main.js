var team = <?php echo json_encode($data['team']); ?>;
var round = null;
var question = null;

function endGame() {
	showBox('finish');
}

function endRound() {
	showBox('endround');
}

function selectTeam() {
	showBox('team-select');
	$('#people input').removeAttr('checked');
	$('#people-select-round-sortkey').val(round.round_sortkey);
	$('#peopleSearch').val('');
	peopleSearchFilter();
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
		$('#question-title').text(question.round.name + ', Q' + question.question_sortkey);
		$('#question-id').val(question.question_id);
		$('#ans-box').val('');
		$('#ans-box').focus();
	} else {
		if(team.game.round.length > 0) {
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
	    submitAnswer($("#ans-form").serialize());
	    return false;
	});

	$('#peopleSearch').on('input', function() {
		peopleSearchFilter();
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
})

function peopleSearchFilter() {
	var value = $('#peopleSearch').val().toLowerCase();
	$("#people li").show();
	$("#people > li").filter(
		function() {
			return $(this).text().toLowerCase().indexOf(value) == -1;
		}).hide();
}

function submitAnswer(data) {
	$.ajax({
        type: "POST",
        url: "/team/" + team.team_code,
        data: data,
        dataType: "json",
        success: function(data) {
        	next_question();
        },
        error: function(){
            showError("Couldn't save your answer. Please try again!");
        }
    });
}

function showError(text) {
	$('#error-msg').text(text);
	$('#error-box').show(500);
}

function hideError() {
	$('#error-box').hide(500);
}