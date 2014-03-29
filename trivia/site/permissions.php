<?php
/* Permissions for database fields */
$permission['team'] = array(
		'game' => array(
				'create' => false,
				'read' => array(
						'game_id',
						'game_name',
						'game_state'),
				'update' => array(),
				'delete' => false),
		'team' => array(
				'create' => false,
				'read' => array(
						'team_id',
						'team_code',
						'game_id',
						'team_name'),
				'update' => array(
						'team_id',
						'team_code',
						'game_id',
						'team_name'),
				'delete' => false),
		'round' => array(
				'create' => false,
				'read' => array(
						'round_id',
						'name',
						'game_id',
						'round_sortkey',
						'round_state'),
				'update' => array(),
				'delete' => false),
		'question' => array(
				'create' => false,
				'read' => array(
						'question_id',
						'round_id',
						'question_sortkey',
						'question_state'),
				'update' => array(),
				'delete' => false),
		'answer' => array(
				'create' => true,
				'read' => array(),
				'update' => array(),
				'delete' => false),
		'person' => array(
				'create' => false,
				'read' => array(
						'person_id',
						'person_name',
						'game_id'),
				'update' => array(),
				'delete' => false),
		'person_table' => array(
				'create' => true,
				'read' => array(
						'round_id',
						'person_id',
						'team_id'),
				'update' => array(),
				'delete' => false),
		'team_round' => array(
				'create' => false,
				'read' => array(),
				'update' => array(),
				'delete' => false));
$permission['user'] = array(
		'game' => array(
				'create' => true,
				'read' => array(
						'game_id',
						'game_name',
						'game_state',
						'game_code'),
				'update' => array(
						'game_id',
						'game_name',
						'game_state',
						'game_code'),
				'delete' => true),
		'team' => array(
				'create' => true,
				'read' => array(
						'team_id',
						'team_code',
						'game_id',
						'team_name'),
				'update' => array(
						'team_id',
						'team_code',
						'game_id',
						'team_name'),
				'delete' => true),
		'round' => array(
				'create' => true,
				'read' => array(
						'round_id',
						'name',
						'game_id',
						'round_sortkey',
						'round_state'),
				'update' => array(
						'round_id',
						'name',
						'game_id',
						'round_sortkey',
						'round_state'),
				'delete' => true),
		'question' => array(
				'create' => true,
				'read' => array(
						'question_id',
						'round_id',
						'question_text',
						'question_sortkey',
						'question_state',
						'question_answer'),
				'update' => array(
						'question_id',
						'round_id',
						'question_text',
						'question_sortkey',
						'question_state',
						'question_answer'),
				'delete' => true),
		'answer' => array(
				'create' => true,
				'read' => array(
						'question_id',
						'team_id',
						'answer_text',
						'answer_is_correct',
						'answer_time'),
				'update' => array(
						'question_id',
						'team_id',
						'answer_text',
						'answer_is_correct',
						'answer_time'),
				'delete' => true),
		'person' => array(
				'create' => true,
				'read' => array(
						'person_id',
						'person_name',
						'game_id'),
				'update' => array(
						'person_id',
						'person_name',
						'game_id'),
				'delete' => true),
		'person_table' => array(
				'create' => true,
				'read' => array(
						'round_id',
						'person_id',
						'team_id'),
				'update' => array(
						'round_id',
						'person_id',
						'team_id'),
				'delete' => true),
		'team_round' => array(
				'create' => true,
				'read' => array(
						'round_round_id',
						'team_team_id',
						'bonus_points'),
				'update' => array(
						'round_round_id',
						'team_team_id',
						'bonus_points'),
				'delete' => true));
$permission['admin'] = array(
		'game' => array(
				'create' => true,
				'read' => array(
						'game_id',
						'game_name',
						'game_state',
						'game_code'),
				'update' => array(
						'game_id',
						'game_name',
						'game_state',
						'game_code'),
				'delete' => true),
		'team' => array(
				'create' => true,
				'read' => array(
						'team_id',
						'team_code',
						'game_id',
						'team_name'),
				'update' => array(
						'team_id',
						'team_code',
						'game_id',
						'team_name'),
				'delete' => true),
		'round' => array(
				'create' => true,
				'read' => array(
						'round_id',
						'name',
						'game_id',
						'round_sortkey',
						'round_state'),
				'update' => array(
						'round_id',
						'name',
						'game_id',
						'round_sortkey',
						'round_state'),
				'delete' => true),
		'question' => array(
				'create' => true,
				'read' => array(
						'question_id',
						'round_id',
						'question_text',
						'question_sortkey',
						'question_state',
						'question_answer'),
				'update' => array(
						'question_id',
						'round_id',
						'question_text',
						'question_sortkey',
						'question_state',
						'question_answer'),
				'delete' => true),
		'answer' => array(
				'create' => true,
				'read' => array(
						'question_id',
						'team_id',
						'answer_text',
						'answer_is_correct',
						'answer_time'),
				'update' => array(
						'question_id',
						'team_id',
						'answer_text',
						'answer_is_correct',
						'answer_time'),
				'delete' => true),
		'person' => array(
				'create' => true,
				'read' => array(
						'person_id',
						'person_name',
						'game_id'),
				'update' => array(
						'person_id',
						'person_name',
						'game_id'),
				'delete' => true),
		'person_table' => array(
				'create' => true,
				'read' => array(
						'round_id',
						'person_id',
						'team_id'),
				'update' => array(
						'round_id',
						'person_id',
						'team_id'),
				'delete' => true),
		'team_round' => array(
				'create' => true,
				'read' => array(
						'round_round_id',
						'team_team_id',
						'bonus_points'),
				'update' => array(
						'round_round_id',
						'team_team_id',
						'bonus_points'),
				'delete' => true));
