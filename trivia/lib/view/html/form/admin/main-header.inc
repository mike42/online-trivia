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
	<li class="list-group-item"><b><%= escape(question.sortkey) %>.</b> <%=escape(question_text)%><br/>
	A. <%= escape(question.answer) %>.</li>
	<% }); %>
	<li class="list-group-item"><a class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> Add A Question</a></li>
</ul>
</div>
</script>

