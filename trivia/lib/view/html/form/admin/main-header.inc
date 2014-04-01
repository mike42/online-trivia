<script type="text/template" id="template-round-li">
<% _.each(round_list, function(round)  { %>
	<li id="roundpill-<%= _.escape(round.round_id) %>">
		<a href="#" onClick="return showRound(<%= _.escape(round.round_id) %>);">
			<%= _.escape(round.round_sortkey) %>. <%= _.escape(round.name) %>
		</a>
	</li>
<% }); %>
	<li class="lastround">
		<a href="#" onClick="return newRound();">
			<span class="glyphicon glyphicon-plus"></span> Add Round
		</a>
	</li>
</script>

<script type="text/template" id="template-person-li">
<% _.each(person_list, function(person)  { %>
	<li class="list-group-item">
		<a class="btn btn-default btn-xs" style="float: right" href="#" onClick="return editPerson(<%= _.escape(person.person_id) %>)"><span class="glyphicon glyphicon-edit"></span></a>
		<%= _.escape(person.person_name) %>
	</li>
<% }); %>
</script>

<script type="text/template" id="template-team-li">
<% _.each(team_list, function(team)  { %>
	<li class="list-group-item">
		<div class="btn-group btn-group-xs" style="float: right">
			<a class="btn btn-default btn-xs" href="#" onClick="return teamInfo(<%= _.escape(team.team_id) %>)"><span class="glyphicon glyphicon-qrcode"></span></a>
			<a class="btn btn-default btn-xs" href="#" onClick="return editTeam(<%= _.escape(team.team_id) %>)"><span class="glyphicon glyphicon-edit"></span></a>
		</div>

		<a href="http://<%= _.escape(location.hostname) %>/team/<%= _.escape(team.team_code) %>"><%= _.escape(team.team_name) %></a>
	</li>
<% }); %>
</script>

<script type="text/template" id="template-team-info">
<div style="text-align: center">
	<h4><%= _.escape(team_name) %></h4>
	<div><img src="/api/team/qr/<%= _.escape(team_id) %>" /></div>
	<div><a href="http://<%= _.escape(location.hostname) %>/team/<%= _.escape(team_code) %>">http://<%= _.escape(location.hostname) %>/team/<%= _.escape(team_code) %></a></div>
</div>
</script>

<script type="text/template" id="template-round">
<h3>Prepare round: <%= _.escape(name) %></h3>
<div class="input-group" id="round-edit">
	<input type="text" id="round-name" class="form-control" placeholder="Round name" value="<%= _.escape(name) %>" />
	<span class="input-group-btn">
	  	<button id="round-up" type="button" class="btn btn-default"><span class="glyphicon glyphicon-arrow-up" /></button>
		<button id="round-down" type="button" class="btn btn-default"><span class="glyphicon glyphicon-arrow-down" /></button>
		<button id="round-trash" type="button" class="btn btn-default"><span class="glyphicon glyphicon-trash" /></button>
	</span>
</div>

<div class="panel panel-default" style="margin-top: 25px">
	<div class="panel-heading">Questions</div>
<ul class="list-group">
	<% _.each(question, function(question)  { %>
	<li class="list-group-item">
		<div class="btn-group btn-group-xs" style="float: right">
		<% if(question.question_sortkey != 1) { %>
			<a class="btn btn-default btn-xs" id="question-up-<%= _.escape(question.question_id) %>" onClick="return questionUp(<%= _.escape(question.question_id) %>)"><span class="glyphicon glyphicon-arrow-up"></span></a>
		<% } %>
		<% if(question.question_sortkey != question_count) { %>
			<a class="btn btn-default btn-xs" id="question-down-<%= _.escape(question.question_id) %>" onClick="return questionDown(<%= _.escape(question.question_id) %>)"><span class="glyphicon glyphicon-arrow-down"></span></a>
		<% } %>
		<a class="btn btn-default btn-xs" href="#" onClick="return editQuestion(<%= _.escape(question.question_id) %>)"><span class="glyphicon glyphicon-edit"></span></a>
		</div>
		<b><%= _.escape(question.question_sortkey) %>.</b> <span class="question-text"><%= _.escape(question.question_text) %></span><br/>
		A. <span class="question-text"><%= _.escape(question.question_answer) %></span></li>
	<% }); %>
	<li class="list-group-item">
		<a class="btn btn-default" href="#" onClick="return addQuestion(<%=  _.escape(round_id) %>)"><span class="glyphicon glyphicon-plus"></span> Add A Question</a>
	</li>
</ul>
</div>
</script>

