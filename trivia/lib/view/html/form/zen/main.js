var game_code = <?php echo json_encode($data['game'] -> get_game_code()); ?>;
var game_id	= <?php echo json_encode($data['game'] -> get_game_id()); ?>;

/**
 * Code to deal with Rounds
 */
var RoundListView = Backbone.View.extend({
	collection : null,
	el : '#round-list',
	template : _.template($('#template-round-li').html()),

	render : function() {
		this.$el.html(this.template({
			round_list: this.collection.toJSON()
		}));
		return this;
	}
});

var RoundView = Backbone.View.extend({
	model: round_model,
	el: '#round-box',
	template: _.template($('#template-round').html()),

	render : function() {
		this.$el.html(this.template(this.model.toJSON()));
		return this;
	}
});

function loadRounds() {
	var rounds = new round_collection();
	rounds.fetch({
		url: '/api/round/list_by_game_id/' + game_id,
		success : function(results) {
			var db = new RoundListView({
				collection : rounds
			});
			round_count = rounds.length;
			db.render();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
}

function showRound(round_id) {
	errClose();
	$('#round-list li').removeClass('active');
	$('#roundpill-' + round_id).addClass('active');
	
	var round = new round_model({round_id: round_id});
	round.fetch({
		url: '/api/round/detailed/' + round_id,
		success : function(results) {
			var db = new RoundView({
				model: round
			});
			db.render();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
	
	return false;
}

$(function() {
	loadRounds();
});

function score(question_id, team_id, score) {
	$('#answer-q' + question_id + '-t' + team_id + ' button').addClass('disabled');
	var answer = new answer_model();
	answer.fetch({
		url: '/api/answer/read/' + question_id + '/' + team_id,
		success : function(results) {
			answer.set({answer_is_correct: score});
			answer.save(null, {
				url: '/api/answer/update/' + question_id + '/' + team_id,
				patch: true,
				success : function(results) {
					$('#answer-q' + question_id + '-t' + team_id).hide();
				},
				error : function(model, response) {
					$('#answer-q' + question_id + '-t' + team_id + ' button').removeClass('disabled');
					handleFailedRequest(response);
				}
			});
		},
		error : function(model, response) {
			$('#answer-q' + question_id + '-t' + team_id + ' button').removeClass('disabled');
			handleFailedRequest(response);
		}
	});
}

function handleFailedRequest(response) {
	var responseObj = $.parseJSON(response.responseText);
	$('#errmsg').text(responseObj.error);
	$('#errbox').show(300);
}

function errClose() {
	$('#errbox').hide(300);
}

var answer_model = Backbone.Model.extend({
	defaults: {
		question_id: 0,
		team_id: 0,
		answer_text: '',
		answer_is_correct: 0,
		answer_time: ''
	}
});

var team_round_model = Backbone.Model.extend({
	defaults: {
		round_round_id: 0,
		team_team_id: 0,
		bonus_points: 0
	}
});

function updateBonus(round_id, team_id) {
	var id = 'r' + round_id + '-t' + team_id;
	$('#box-' + id).removeClass('has-success');
	$('#box-' + id).removeClass('has-error');
	points = $('#bonus-' + id).val();
	
	var team_round = new team_round_model();
	team_round.fetch({
		url: '/api/team_round/read/' + round_id + '/' + team_id,
		success : function(results) {
			team_round.set({bonus_points: points});
			team_round.save(null, {
				url: '/api/team_round/update/' + round_id + '/' + team_id,
				patch: true,
				success : function(results) {
					$('#box-' + id).addClass('has-success');
				},
				error : function(model, response) {
					$('#box-' + id).addClass('has-error');
					handleFailedRequest(response);
				}
			});
		},
		error : function(model, response) {
			$('#box-' + id).addClass('has-error');
			handleFailedRequest(response);
		}
	});
}