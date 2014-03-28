var game_code = <?php echo json_encode($data['game'] -> get_game_code()); ?>;
var game_id	= <?php echo json_encode($data['game'] -> get_game_id()); ?>;

function tabTo(page) {
	$('#navbutton li').removeClass('active');
	$('#pill-' + page).addClass('active');
	$('.page').hide();
	$('#page-' + page).show();
}

var AdminMain = Backbone.Router.extend({
	  routes: {
	    "overview":	"overview",
	    "rounds":	"rounds",
	    "teams":	"teams",
	    "people":	"people",
	    "*path":	"defaultRoute"
	  },

	  overview: function() {
		  tabTo('overview');
	  },
	  
	  rounds: function() {
		  tabTo('rounds');
		  loadRounds();
	  },
	  
	  teams: function() {
		  tabTo('teams');
	  },
	  
	  people: function() {
		  tabTo('people');
	  },
	  
	  defaultRoute: function(path) {
		  this.navigate("overview", {trigger: true});
	  }
});

var appRouter = new AdminMain();
Backbone.history.start();

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

function handleFailedRequest(response) {
	var responseObj = $.parseJSON(response.responseText);
	$('#errmsg').text(responseObj.error);
	$('#errbox').show(300);
}

function errClose() {
	$('#errbox').hide(300);
}

function loadRounds() {
	var rounds = new round_collection();
	rounds.fetch({
		url: '/trivia/api/round/list_by_game_id/' + game_id,
		success : function(results) {
			var db = new RoundListView({
				collection : rounds
			});
			db.render();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
}

function newRound() {
	var round = new round_model({name: "New Round", game_id: game_id, sortkey: 0});
	round.save(null, {
		success : function(results) {
			loadRounds();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
	
	return false;
}