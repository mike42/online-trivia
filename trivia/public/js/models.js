/* game */
var game_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/game',
	idAttribute: 'game_id',
	defaults: {
		game_name: '',
		game_state: 0,
		game_code: ''
	}
});
var game_collection = Backbone.Collection.extend({
	url : '/trivia/api/game/list_all/',
	model : game_model
});

/* team */
var team_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/team',
	idAttribute: 'team_id',
	defaults: {
		team_code: '',
		game_id: 0,
		team_name: ''
	}
});
var team_collection = Backbone.Collection.extend({
	url : '/trivia/api/team/list_all/',
	model : team_model
});

/* round */
var round_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/round',
	idAttribute: 'round_id',
	defaults: {
		name: '',
		game_id: 0,
		round_sortkey: 0,
		round_state: 0
	}
});
var round_collection = Backbone.Collection.extend({
	url : '/trivia/api/round/list_all/',
	model : round_model
});

/* question */
var question_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/question',
	idAttribute: 'question_id',
	defaults: {
		round_id: 0,
		question_text: '',
		question_sortkey: 0,
		question_state: 0,
		question_answer: ''
	}
});
var question_collection = Backbone.Collection.extend({
	url : '/trivia/api/question/list_all/',
	model : question_model
});

/* person */
var person_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/person',
	idAttribute: 'person_id',
	defaults: {
		person_name: '',
		game_id: 0
	}
});
var person_collection = Backbone.Collection.extend({
	url : '/trivia/api/person/list_all/',
	model : person_model
});

