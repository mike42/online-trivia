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
		  loadTeams();
	  },
	  
	  people: function() {
		  tabTo('people');
		  loadPeople();
	  },
	  
	  defaultRoute: function(path) {
		  this.navigate("overview", {trigger: true});
	  }
});

var appRouter = new AdminMain();
Backbone.history.start();

function handleFailedRequest(response) {
	var responseObj = $.parseJSON(response.responseText);
	$('#errmsg').text(responseObj.error);
	$('#errbox').show(300);
}

function errClose() {
	$('#errbox').hide(300);
}

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

function loadRounds(highlight) {
	var rounds = new round_collection();
	rounds.fetch({
		url: '/trivia/api/round/list_by_game_id/' + game_id,
		success : function(results) {
			var db = new RoundListView({
				collection : rounds
			});
			db.render();
			if(typeof highlight !== 'undefined') {
				showRound(highlight);
			}
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
}

function newRound() {
	var round = new round_model({name: "New Round", game_id: game_id, sortkey: 0});
	round.save(null, {
		success : function(round) {
			loadRounds(round.id);
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
	
	return false;
}

function showRound(round_id) {
	$('#round-list li').removeClass('active');
	$('#roundpill-' + round_id).addClass('active');
	
	var round = new round_model({round_id: round_id});
	round.fetch({
		success : function(results) {
			var db = new RoundView({
				model: round
			});
			db.render();
			if($('#round-name').val() == "New Round") {
				$('#round-name').focus(); // Prompt the user to rename rounds
			}
			
			$('#round-name').on('change', function() {
				round.set({name: $('#round-name').val()});
				round.save(null, {
					success: function(results) {
						loadRounds(results.id);
					},
					error : function(model, response) {
						handleFailedRequest(response);
					}
				});
			});
			
			$('#round-up').on('click', function() {
				round.set({round_sortkey: 'up'});
				round.save(null, {
					patch: true,
					success: function(results) {
						loadRounds(results.id);
					},
					error : function(model, response) {
						handleFailedRequest(response);
					}
				});
			});
			
			$('#round-down').on('click', function() {
				round.set({round_sortkey: 'down'});
				round.save(null, {
					patch: true,
					success: function(results) {
						loadRounds(results.id);
					},
					error : function(model, response) {
						handleFailedRequest(response);
					}
				});
			});
			
			$('#round-trash').on('click', function() {
				if(confirm('Delete the round?')) {
					$('#round-box').empty();
					round.destroy({
						success: function(model, response) {
							loadRounds();
						},							
						error: function(model, response) {
							handleFailedRequest(response);
						}
					});
				}
			});
			
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
	
	return false;
}

/**
 * Code to deal with Questions
 */
$('#addQuestion').on('show.bs.modal', function() {
    $("#addQuestionText").val('');
    $("#addQuestionAnswer").val('');
})
$('#addQuestion').on('shown.bs.modal', function() {
    $("#addQuestionText").focus();
})

function addQuestion(round_id) {
    $("#addQuestionRoundId").val(round_id);
    $('#addQuestion').modal();
    return false;
}

function addQuestionSave() {
	var round_id = $("#addQuestionRoundId").val();
	var question = new question_model({
		round_id: round_id,
		question_text:  $("#addQuestionText").val(),
		question_answer: $("#addQuestionAnswer").val()
	});
	question.save(null, {
		success : function(results) {
			showRound(round_id);
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
	
	$('#addQuestion').modal('hide');
}

function editQuestion(question_id) {
	alert(question_id);
	return false;
}

/**
 * Code to deal with Teams
 */
var TeamListView = Backbone.View.extend({
	collection : null,
	el : '#team-list',
	template : _.template($('#template-team-li').html()),

	render : function() {
		this.$el.html(this.template({
			team_list: this.collection.toJSON()
		}));
		return this;
	}
});

$('#addTeam').on('show.bs.modal', function() {
    $("#addTeamName").val('');
})
$('#addTeam').on('shown.bs.modal', function() {
    $("#addTeamName").focus();
})

function addTeam() {
	$('#addTeam').modal('show');
}

function addTeamSave() {
	var team = new team_model({team_name: $("#addTeamName").val(), game_id: game_id});
	team.save(null, {
		success : function(team) {
			loadTeams();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
	
	$('#addTeam').modal('hide');
}

function loadTeams() {
	var teams = new team_collection();
	teams.fetch({
		url: '/trivia/api/team/list_by_game_id/' + game_id,
		success : function(results) {
			var db = new TeamListView({
				collection : teams
			});
			db.render();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
}

function editTeam(team_id) {
	alert(team_id);
	return false;
}

/**
 * Code to deal with People
 */
var PersonListView = Backbone.View.extend({
	collection : null,
	el : '#person-list',
	template : _.template($('#template-person-li').html()),

	render : function() {
		this.$el.html(this.template({
			person_list: this.collection.toJSON()
		}));
		return this;
	}
});

$('#addPerson').on('show.bs.modal', function() {
    $("#addPersonName").val('');
})
$('#addPerson').on('shown.bs.modal', function() {
    $("#addPersonName").focus();
})

function addPerson() {
	$('#addPerson').modal();
}

function addPeopleSave() {
	var person = new person_model({person_name: $("#addPersonName").val(), game_id: game_id});
	person.save(null, {
		success : function(person) {
			loadPeople();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
	
	$('#addPerson').modal('hide');
}

function loadPeople() {
	var people = new person_collection();
	people.fetch({
		url: '/trivia/api/person/list_by_game_id/' + game_id,
		success : function(results) {
			var db = new PersonListView({
				collection : people
			});
			db.render();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
}

function editPerson(person_id) {
	var person = new person_model({person_id: person_id});
	person.fetch({
		success: function(results) {
			$('#editPersonId').val(person_id);
			$('#editPersonName').val(person.get('person_name'));
			$('#editPerson').modal();
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
	return false;
}

function editPersonDelete() {
	if(confirm('Delete person?')) {
		var person = new person_model({person_id: $('#editPersonId').val()});
		person.destroy({
			success: function(model, response) {
				$('#editPerson').modal('hide');
				loadPeople();
			},							
			error: function(model, response) {
				handleFailedRequest(response);
			}
		});
	}
}

function editPersonSave() {
	var person = new person_model({person_id: $('#editPersonId').val()});
	person.fetch({
		success: function(results) {
			person.set('person_name', $('#editPersonName').val());
			person.save(null, {
				success : function(person) {
					$('#editPerson').modal('hide');
					loadPeople();
				},
				error : function(model, response) {
					handleFailedRequest(response);
				}
			});
		},
		error : function(model, response) {
			handleFailedRequest(response);
		}
	});
	return false;
}