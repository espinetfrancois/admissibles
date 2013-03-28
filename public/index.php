<?php
define('ROOT_PATH', realpath(dirname(__FILE__).'/../'));
define('APPLICATION_PATH', ROOT_PATH.'/application');
define('LIBRARY_PATH', APPLICATION_PATH.'/library');

require_once(APPLICATION_PATH.'/inc/autoload.php');
session_start();

$config = new Config();
Registry::getInstance()->set('config', $config);

require_once(APPLICATION_PATH.'/inc/sql.php');
$layout = new Layout();
$router = new Router($_SERVER['REQUEST_URI'], $layout);

Registry::getInstance()->set('layout', $layout);

try {
    $layout->addPage($router->file);
} catch (Exception $e) {
    $layout->addContent(TEMPLATE_PATH.'/probleme.html');
    if (APP_ENV != 'production') {
        $layout->addMessage($e->getMessage(), MSG_LEVEL_ERROR);
        $layout->addMessage($e->getTraceAsString(), MSG_LEVEL_ERROR);
    }
}
echo $layout;

