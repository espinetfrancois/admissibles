<?php
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', ROOT_PATH . '/application');
define('LIBRARY_PATH', APPLICATION_PATH . '/library');

require_once(APPLICATION_PATH . '/inc/autoload.php');
session_start();
//instanciation des objets de base (ne doit pas rater)

try {
	$config = new Config();
	Registry::getInstance()->set('config', $config);
	require_once(APPLICATION_PATH . '/inc/sql.php');
	$layout = new Layout();
	$router = new Router($_SERVER['REQUEST_URI'], $layout);
	Registry::getInstance()->set('layout', $layout);

	//gestion des erreurs dans les pages
	try {
		$layout->addPage($router->file);
	} catch (Exception_Error $e) {
	    //todo logger ici cette erreur mineure
	    throw $e;
	} catch (Exception $e) {
		$layout->addContent(file_get_contents(TEMPLATE_PATH . '/probleme.html'));
		if (APP_ENV != 'production') {
			$layout->addMessage($e->getMessage() .' : <br/>'. $e->getTraceAsString(), MSG_LEVEL_ERROR);
		} else {
			//redirection sur la page des erreurs
			$ewrap = new Exception_Projet("Erreur capturée dans une page : " . $e->getMessage(),null, $e);
			$layout->addHead('<meta http-equiv="Refresh" CONTENT="1; URL=/errors?exception='. $ewrap->url() . '">');
		}
	}
	echo $layout;

} catch (Exception_Error $e) {
	//erreur pas grave, on s'arrète
	if ($e->get_errno() & E_ERROR) {
		echo $layout;
	}
} catch (Exception $e) {
	echo $layout;
}