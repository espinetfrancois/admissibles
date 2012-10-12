<?php
require_once('inc/autoload.php');
session_start();
require_once('inc/sql.php');

$router = new Router($_SERVER['REQUEST_URI']);
$layout = new Layout();
if ($router->not_found) {
	$layout->not_found = true;
}
$layout->addContent($router->file);
echo $layout;
