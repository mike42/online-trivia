online-trivia
=============
Web app for running a trivia game

Description
-----------
This web app facilitates paperless trivia games for as many teams as you can fit in the room.

* Teams each get a web address (can be placed on their table as a QR code), and are prompted with the next part of the game when they log in.
* The MC has a full-screen display of questions, which contains helpful hints such as which tables have answered so far. At the end of the round, answers and an optional leader-board are also displayed.
* A 'Zen master' sees questions and anwers as they come in, and allocates points.

Note on game variant
--------------------
This is an 'individuals trivia' game, so people are expected to change groups each round (you will need to organise how you do this!). The team as a whole does not appear on the leader-board: each individual in a team is credited with the number of points that the team earned in that round.

Setup instructions
------------------
Clone the repo.

Import `trivia.sql` into a new database.

Set `trivia` as your web root, symlink your web-root to it, or copy the contents.

Add database connection details to the following code snippet and save to `trivia/site/config.php`
```php
<?php
/* Database connection options */
$config['database']['user'] = "";
$config['database']['pass'] = "";
$config['database']['host'] = "localhost";
$config['database']['db'] = "";
```
