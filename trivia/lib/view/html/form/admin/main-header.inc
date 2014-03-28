<script type="text/template" id="template-round-li">
<% _.each(round_list, function(round)  { %>
	<li id="roundpill-<%= _.escape(round.round_id) %>"><a href="#" onClick="return showRound(<%= _.escape(round.round_id) %>);"><%= _.escape(round.name) %></a></li>
<% }); %>
	<li><a href="#" onClick="return newRound();"><span class="glyphicon glyphicon-plus"></span> New</a></li>
</script>

<script type="text/template" id="template-round">

<h3>Prepare round: <%= _.escape(name) %></h3>
<div class="input-group" id="round-edit">
	<input type="text" id="round-name" class="form-control" placeholder="Round name" value="<%= _.escape(name) %>" />
	<span class="input-group-btn">
	  	<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-arrow-up" /></button>
		<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-arrow-down" /></button>
		<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-trash" /></button>
	</span>
</div>
<div id="round-title">
	<h2></h2>
</div>

<div class="panel panel-default">
<ul class="list-group">
	<% _.each(question, function(question)  { %>
	<li class="list-group-item"><b><%= escape(question.sortkey) %>.</b> <%=escape(question_text)%><br/>
	A. <%= escape(question.answer) %>.</li>
	<% }); %>
	<li="list-group-item">..</li>
</ul>
</div>
</script>

