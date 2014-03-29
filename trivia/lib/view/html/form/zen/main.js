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

function handleFailedRequest(response) {
	var responseObj = $.parseJSON(response.responseText);
	$('#errmsg').text(responseObj.error);
	$('#errbox').show(300);
}

function errClose() {
	$('#errbox').hide(300);
}

function leaderboard() {
	alert('No leaderboard exists yet');
}