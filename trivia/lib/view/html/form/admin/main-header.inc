<script type="text/template" id="template-round-li">
<% _.each(round_list, function(round)  { %>
	<li><a href="#" onClick="return showRound(<%= _.escape(round.id) %>);"><%= _.escape(round.name) %></a></li>
<% }); %>
	<li><a href="#" onClick="return newRound();"><span class="glyphicon glyphicon-plus"></span> New</a></li>
</script>

