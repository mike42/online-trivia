var game = <?php echo json_encode($data['game'] -> to_array_filtered('user')); ?>;

var in_game = false;
var cur_round = 0;
var cur_q = 0;

var refresh = 'none';

/* Team status code */
var team_status_model = Backbone.Model.extend({
	defaults: {
		game_id: 0,
		team_id: 0,
		team_name: '',
		num: 0
	}
});
var team_status_collection = Backbone.Collection.extend({
	model: team_status_model
});
// div#team-status
// /api/round/team_counts/:round_id
// /api/question/repsondents/:question_id

/* Leader board code */
var leaderboard_model = Backbone.Model.extend({
	defaults: {
		game_id: 0,
		person_id: 0,
		person_name: '',
		score: 0
	}
});

var leaderboard_collection = Backbone.Collection.extend({
	model: leaderboard_model
});

var LeaderboardView = Backbone.View.extend({
	model: round_model,
	el: 'div#leaderboard-frame',
	template: _.template($('#template-leaderboard').html()),

	render : function() {
		this.$el.html(this.template({
			leaderboard_list: this.collection.toJSON()
		}));
		return this;
	}
});

/* Button clicks and such */
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
		signup(cur_round);		
		in_game = true;
	}
}

function signup(cur_round) {
	$('#round-subtitle').text('Signup');
	
	
}

function tabTo(id) {
	$('.main-info').hide();
	$('#' + id).show();
}

function leaderBoard() {
	tabTo('leaderboard');
	in_game = false;

	var leaderboard = new leaderboard_collection();
	leaderboard.fetch({
		url: '/api/game/leaderboard/' + game.game_id,
		success : function(results) {
			var db = new LeaderboardView({
				collection: leaderboard
			});
			db.render();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
}
