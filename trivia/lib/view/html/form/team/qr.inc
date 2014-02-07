<center><h2><?php echo $data['team'] -> get_team_name(); ?></h2>
<?php
echo "<a href=\"".core::escapeHTML($data['url'])."\"><img src=\"".core::escapeHTML($data['png'])."\" /></a><br/>";
echo "<a href=\"".core::escapeHTML($data['url'])."\">".core::escapeHTML($data['url'])."</a>";
?>
</center>
