<script type="text/template" id="template-leaderboard">
	<table class="leaderboard-table">
	<%
		var rank = 0;
		var lastscore = 0;
	%>
	<% _.each(leaderboard_list, function(leader)  { %>
	<%
		if(lastscore != leader.score) {
			rank++;
			lastscore = leader.score;
		}
	%>
	<tr><td class="leader-rank"><%= _.escape(rank) %></td><td class="leader-name"><%= _.escape(leader.person_name) %></td><td class="leader-score"><%= _.escape(leader.score) %></td></tr>
	<% }); %>
	</table>
</script>

<script type="text/template" id="template-status">
	<%
		var a = false;
	%>
	<div class="col-sm-6">
	<% _.each(team_status_list, function(team_status)  { %>
		<%
		a = !a;
		if(a) {
		%>
			<h3><span id="team-<%= _.escape(team_status.team_id) %>" class="label <%= team_status.num == 0 ? 'label-default' : 'label-info' %>"><%= _.escape(team_status.num) %></span> <%= _.escape(team_status.team_name) %>
		<% } %>
	<% }); %>
	</div>

	<%
		var a = true;
	%>
	<div class="col-sm-6">
	<% _.each(team_status_list, function(team_status)  { %>
		<%
		a = !a;
		if(a) {
		%>
			<h3><span id="team-<%= _.escape(team_status.team_id) %>" class="label <%= team_status.num == 0 ? 'label-default' : 'label-info' %>"><%= _.escape(team_status.num) %></span> <%= _.escape(team_status.team_name) %>
		<% } %>
	<% }); %>
	</div>
</script>