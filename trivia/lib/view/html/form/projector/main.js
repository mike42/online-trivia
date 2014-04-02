var game = <?php echo json_encode($data['game'] -> to_array_filtered('user')); ?>;

var in_game = false;
var cur_round = 0;
var cur_q = 0;

var refresh = 'none';
var status_url = '';

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
var TeamStatusView = Backbone.View.extend({
	model: team_status_model,
	el: 'div#team-status',
	template: _.template($('#template-status').html()),

	render : function() {
		this.$el.html(this.template({
			team_status_list: this.collection.toJSON()
		}));
		return this;
	}
});

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
	el: 'div#leaderboard-frame',
	template: _.template($('#template-leaderboard').html()),

	render : function() {
		this.$el.html(this.template({
			leaderboard_list: this.collection.toJSON()
		}));
		return this;
	}
});

/* Answer View */
var AnswerView = Backbone.View.extend({
	el: 'div#answer-frame',
	template: _.template($('#template-answer').html()),
	render : function() {
		this.$el.html(this.template({
			question_list: this.collection.toJSON()
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
	var questions = new question_collection(game.round[cur_round].question);
	
	var db = new AnswerView({
		collection: questions
	});
	db.render();
	
}

function setQuestion(num) {
	$('#round-subtitle').text('');
	if(num >= 0 && num < game.round[cur_round].question.length) {
		tabTo('round');
		cur_q = num;
		$('#round-subtitle').text(game.round[cur_round].question[cur_q].question_text);
		setStatusURL('/api/question/respondents/' + game.round[cur_round].question[cur_q].question_id);
		in_game = true;
	} else if(num >= game.round[cur_round].question.length) {
		cur_q = game.round[cur_round].question.length;
		showAnswers(num);
		in_game = true;
		setStatusURL('');
	} else if(num < 0){
		cur_q = -1;
		in_game = true;
		signup(cur_round);
	}
}

function signup(cur_round) {
	$('#round-subtitle').text('Signup');
	setStatusURL('/api/round/team_counts/' + game.round[cur_round].round_id);
}

function setStatusURL(url) {
	if(status_url != url) {
		$('div#team-status').html('<img src="/public/loading.gif" />');
		status_url = url;
		updateStatus();
	}
}

function updateStatus() {
	if(!in_game || status_url == '') {
		/* Don't bother */
		return;
	}
	
	var team_status = new team_status_collection();
	team_status.fetch({
		url: status_url,
		success : function(results) {
			var db = new TeamStatusView({
				collection: team_status
			});
			db.render();
		},
		error : function(model, response) {
			// Ignore errors, will be reloaded in good time.
		}
	});
}

function tabTo(id) {
	$('.main-info').hide();
	$('#' + id).show();
}

function leaderBoard() {
	$('div#leaderboard-frame').html('<img src="/public/loading.gif" />');
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
			$('div#leaderboard-frames').html('Uh oh! Better hit reload. :(')
		}
	});
}

$(function() {
	window.setInterval(function(){ 
		updateStatus();
	}, 3000)
});