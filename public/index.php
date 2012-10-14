<?php
define('ROOT_PATH', realpath(dirname(__FILE__).'/../'));
define('APPLICATION_PATH', ROOT_PATH.'/application');

require_once(APPLICATION_PATH.'/inc/autoload.php');

session_start();
require_once(APPLICATION_PATH.'/inc/sql.php');

$router = new Router($_SERVER['REQUEST_URI']);
$layout = new Layout();
if ($router->not_found) {
	$layout->not_found = true;
}
$layout->addContent($router->file);
echo $layout;
