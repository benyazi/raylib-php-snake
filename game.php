<?php
require '.\vendor\autoload.php';
include_once('.\src\GameLoop.php');
include_once('.\src\GameState.php');

$game = new GameLoop(600,600);
$game->start();