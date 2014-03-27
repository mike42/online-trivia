<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/favicon.ico">

    <title>Trivia</title>
    <link href="/trivia/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/trivia/public/css/trivia.css" rel="stylesheet">
  </head>

  <body>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <div class="container">
<?php
include(dirname(__FILE__) . "/form/" . $data['template'] . ".inc");
?>
    </div>
    <script src="/trivia/public/js/bootstrap.min.js"></script>
  </body>
</html>

