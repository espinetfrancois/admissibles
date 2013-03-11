<?php
define('ROOT_PATH', realpath(dirname(__FILE__).'/../'));
define('APPLICATION_PATH', ROOT_PATH.'/application');
define('LIBRARY_PATH', APPLICATION_PATH.'/library');

require_once(APPLICATION_PATH.'/inc/autoload.php');
session_start();

$_SESSION['config'] = new Config();
//require_once(APPLICATION_PATH.'/inc/sql.php');

$layout = new Layout();
$router = new Router($_SERVER['REQUEST_URI'], $layout);

try {
    $layout->addPage($router->file);
} catch (RuntimeException $e) {
    $layout->addContent(TEMPLATE_PATH.'/probleme.html');
    if (APP_ENV != 'production') {
        $layout->addContent($e->getMessage());
        $layout->addContent($e->getTraceAsString());
    }
}
echo $layout;