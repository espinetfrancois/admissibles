<?php
//TODO : newline after control structure
//TODO : 2 blank lines after function
//TODO : check @return

define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', ROOT_PATH . '/application');
define('LIBRARY_PATH', APPLICATION_PATH . '/library');

require_once(APPLICATION_PATH . '/inc/autoload.php');
session_start();
//instanciation des objets de base (ne doit pas rater)
try {
	$config = new Config();
	$layout = new Layout();
	//enregistrement dans le registre (accès à travers l'application)
	Registry::getInstance()->set('config', $config);
	Registry::getInstance()->set('layout', $layout);

	$router = new Router($_SERVER['REQUEST_URI'], $layout);
	//après le layout
	require_once(APPLICATION_PATH . '/inc/sql.php');

	//gestion des erreurs dans les pages
	try {
		$layout->addPage($router->file);
	} catch (Exception $e) {
	    $layout->clearContent();
		$layout->addContent(file_get_contents(TEMPLATE_PATH . '/probleme.html'));
		if (APP_ENV != 'production') {
		    $layout->appendCss('erreurs.css');
			$layout->addMessage($e->getMessage() .' : <br/><pre>'. $e->getTraceAsString() . '</pre><pre>'.$e->getPrevious() . '</pre>', MSG_LEVEL_ERROR);
		} else {
			//redirection sur la page des erreurs
			$ewrap = new Exception_Projet("Erreur capturée dans une page : " . $e->getMessage(),null, $e);
			$layout->addHead('<meta http-equiv="Refresh" CONTENT="1; URL=/errors?exception='. $ewrap->url() . '">');
		}
	}
	echo $layout;
} catch (Exception_Layout $e) {
    //pas de layout, on fait à la main
    echo "Problème lors du chargement du layout de l'application";
    if (APP_ENV != 'production')
    	$layout->addMessage($e->getMessage().'<br/><pre>'.$e->getTraceAsString().'</pre><pre>'.$e->getPrevious().'</pre>', MSG_LEVEL_ERROR);

} catch (Exception_Config $e) {
    //pas de layout, on fait à la main
    echo "Problème lors du chargement de la configuration de l'application";
    if (APP_ENV != 'production')
    	$layout->addMessage($e->getMessage().'<br/><pre>'.$e->getTraceAsString().'</pre><pre>'.$e->getPrevious().'</pre>', MSG_LEVEL_ERROR);

} catch (Exception_Bdd $e) {
    $layout->addMessage("Un problème est survenu avec la base de données du site.", MSG_LEVEL_ERROR);
    if (APP_ENV != 'production')
        $layout->addMessage($e->getMessage().'<br/><pre>'.$e->getTraceAsString().'</pre><pre>'.$e->getPrevious().'</pre>', MSG_LEVEL_ERROR);

    echo $layout;
} catch (Exception $e) {
	//erreur grave, on fait ce qu'on peut
	echo "Un problème grave est survenu.<br/>";
	try {
	    $exep = new Exception_Projet("Une erreur grave est survenue", null, $e);
	    $mail = new Mail_AdminTech();
	    $mail->exception($exep->render());
	    echo "Les administrateurs du site ont été prévenus.<br/>";
	} catch (Exception $e) {
	    //rien?
	}
}