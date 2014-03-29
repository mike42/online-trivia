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
				<button class="btn btn-xs btn-danger">0</button>
				<button class="btn btn-xs btn-success">1</button>
				<button class="btn btn-xs btn-primary">2</button>
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
<ul class="list-group">
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
		<a href="#" onClick="return leaderboard();">
			<span class="glyphicon glyphicon-list-alt"></span> Leaderboard
		</a>
	</li>
</script>