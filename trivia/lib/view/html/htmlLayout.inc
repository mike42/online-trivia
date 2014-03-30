<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/favicon.ico">

    <title><?php 
    if(isset($data['game'])) {
    	echo core::escapeHTML($data['game'] -> get_game_name()) . " - Trivia";
    } else {
    	echo "Trivia";
    }
    ?></title>
    <link href="/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/public/css/trivia.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/public/question64px.png">
<?php
    if(file_exists(dirname(__FILE__) . "/form/" . $data['template'] . "-header.inc")) {
		include(dirname(__FILE__) . "/form/" . $data['template'] . "-header.inc");
    }
?>
  </head>

  <body>
    <div class="container">
<?php
	include(dirname(__FILE__) . "/form/" . $data['template'] . ".inc");
?>
    </div>
    <script src="/public/js/jquery-1.10.2.min.js"></script>
   	<script src="/public/js/underscore-min.js"></script>
   	<script src="/public/js/backbone-min.js"></script>
   	<script src="/public/js/models.js"></script>
    <script src="/public/js/bootstrap.min.js"></script>
	<script>
<?php
	include(dirname(__FILE__) . "/form/" . $data['template'] . ".js");
?></script>
  </body>
</html>

