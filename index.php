<?php
require_once('inc/autoload.php');
session_start();
require_once('inc/sql.php');

$router = new Router($_SERVER['REQUEST_URI']);
$layout = new Layout();
$layout->addContent($router->file);
//$layout->setId(1);
echo $layout;
