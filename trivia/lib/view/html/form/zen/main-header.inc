<script type="text/template" id="template-round">
<h3><%= _.escape(name) %></h3>
<div class="panel panel-default" style="margin-top: 25px">
	<div class="panel-heading">Questions</div>
<ul class="list-group">
	<% _.each(question, function(question)  { %>
	<li class="list-group-item">
		<b><%= _.escape(question.question_sortkey) %>.</b> <%= _.escape(question.question_text) %><br/>
		A. <%= _.escape(question.question_answer) %>
		<ul class="answer-list">
		<% _.each(question.answer, function(a)  { %>
			<li id="answer-q<%= _.escape(a.question_id) %>-t<%= _.escape(a.team_id) %>">
			<div style="float:right" class="btn-group btn-group-xs">
				<button class="btn btn-xs btn-danger" onClick="score('<%= _.escape(a.question_id) %>', '<%= _.escape(a.team_id) %>', 1)">0</button>
				<button class="btn btn-xs btn-success" onClick="score('<%= _.escape(a.question_id) %>', '<%= _.escape(a.team_id) %>', 2)">1</button>
				<button class="btn btn-xs btn-primary" onClick="score('<%= _.escape(a.question_id) %>', '<%= _.escape(a.team_id) %>', 3)">2</button>
			</div>
			<b><%= _.escape(a.team.team_name) %>:</b> <%= _.escape(a.answer_text) %></li>
		<% }); %>
		</ul>
	</li>
	<% }); %>
</ul>
</div>

<div class="panel panel-default" style="margin-top: 25px">
<div class="panel-heading">Bonus points</div>
<ul class="list-group" id="bonus-points">
<% _.each(team_round, function(team_round)  { %>
<li class="list-group-item form-group" id="box-r<%= _.escape(team_round.round.round_id) %>-t<%= _.escape(team_round.team.team_id) %>">
<label><%= _.escape(team_round.team.team_name) %></label> <input onChange="updateBonus('<%= _.escape(team_round.round.round_id) %>', '<%= _.escape(team_round.team.team_id) %>')" id="bonus-r<%= _.escape(team_round.round.round_id) %>-t<%= _.escape(team_round.team.team_id) %>" type="text" class="form-control" value="<%= _.escape(team_round.bonus_points) %>"/>
</li>
<% }); %>
<ul>
</div>
</script>

<script type="text/template" id="template-round-li">
<% _.each(round_list, function(round)  { %>
	<li id="roundpill-<%= _.escape(round.round_id) %>">
		<a href="#" onClick="return showRound(<%= _.escape(round.round_id) %>);">
			<%= _.escape(round.round_sortkey) %>. <%= _.escape(round.name) %>
		</a>
	</li>
<% }); %>
	<li class="lastround">
		<a href="?">
			<span class="glyphicon glyphicon-arrow-left"></span> Back to game setup
		</a>
	</li>
</script>