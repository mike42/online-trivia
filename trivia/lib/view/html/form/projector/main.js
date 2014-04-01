var game = <?php echo json_encode($data['game'] -> to_array_filtered('user')); ?>;

var in_game = false;
var cur_round = 0;
var cur_q = 0;

// div#team-status
// div#leaderboard-frame

function prev() {
	if(!in_game) {
		return false;
	}
	if(cur_q >= 0) {
		setQuestion(cur_q - 1);
	} else if(cur_round > 0) {
		setRound(cur_round - 1);
		setQuestion(game.round[cur_round].question.length);
	}
	return false;
}

function next() {
	if(!in_game) {
		return false;
	}
	if(cur_q < game.round[cur_round].question.length) {
		setQuestion(cur_q + 1);
	} else if(cur_round < game.round.length - 1) {
		setRound(cur_round + 1);
	} else {
		leaderBoard();
	}
	return false;
}

function setRound(num) {
	$('#round-title').text('');
	if(num >= 0 && num < game.round.length) {
		cur_round = num;
		$('#round-title').text(game.round[cur_round].name);	
		setQuestion(-1);
		tabTo('round');
		in_game = true;
	}

}

function showAnswers(num) {
	tabTo('answers');
}

function setQuestion(num) {
	$('#round-subtitle').text('');
	if(num >= 0 && num < game.round[cur_round].question.length) {
		tabTo('round');
		cur_q = num;
		$('#round-subtitle').text(game.round[cur_round].question[cur_q].question_text);
		in_game = true;
	} else if(num >= game.round[cur_round].question.length) {
		cur_q = game.round[cur_round].question.length;
		showAnswers(num);
		in_game = true;
	} else if(num < 0){
		cur_q = -1;
		$('#round-subtitle').text('Signup');
		in_game = true;
	}
}

function tabTo(id) {
	$('.main-info').hide();
	$('#' + id).show();
}

function leaderBoard() {
	tabTo('leaderboard');
	in_game = false;
}