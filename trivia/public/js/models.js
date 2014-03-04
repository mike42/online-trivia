/* game */
game_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/game',
	idAttribute: 'game_id',
	defaults: {
		game_name: '',
		game_state: 0,
		game_code: ''
	}
});

/* team */
team_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/team',
	idAttribute: 'team_id',
	defaults: {
		team_code: '',
		game_id: 0,
		team_name: ''
	}
});

/* round */
round_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/round',
	idAttribute: 'round_id',
	defaults: {
		name: '',
		game_id: 0,
		round_sortkey: 0,
		round_state: 0
	}
});

/* question */
question_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/question',
	idAttribute: 'question_id',
	defaults: {
		round_id: 0,
		question_text: '',
		question_sortkey: 0,
		question_state: 0
	}
});

/* person */
person_model = Backbone.Model.extend({
	urlRoot: '/trivia/api/person',
	idAttribute: 'person_id',
	defaults: {
		person_name: '',
		game_id: 0
	}
});

