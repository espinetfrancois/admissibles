<?php
/**
 * Page de gestion des erreurs
 * @todo : gestion des erreurs dans cette page même
 */
session_start();
define('ROOT_PATH', realpath(dirname(__FILE__).'/../'));
define('APPLICATION_PATH', ROOT_PATH.'/application');
define('LIBRARY_PATH', APPLICATION_PATH.'/library');

try {
    include_once(APPLICATION_PATH.'/inc/autoload.php');
    $config = new Config();
    $layout = new Layout();

    if (isset($_GET['exception'])) {
    	$e = urldecode($_GET['exception']);
    	if (isset($_SESSION['administrateur']) && $_SESSION['administrateur'] === true) {
    	    $layout->addContent("<h2>Cher administrateur, voici des informations supplémentaires.</h2>");
    	    $layout->addContent($e);
    	} else {
    	    if (APP_MAIL) {
    	        $mail = new Mail_AdminTech();
    	        $mail->exception($e);
    	    }
    	}
    	//éventuellement ajouter un formulaire de contact
    	$layout->prependContent(file_get_contents(TEMPLATE_PATH.'/erreur.html'));
    	$layout->appendCss('erreurs.css');
    } else {
        //l'utilisateur est arrivé par ici par hasard
        $layout->addContent("<h2>L'application ne se trouve pas ici.</h2><p>Pour acceder à l'application, c'est <a href='/'>ici</a></p>");
    }
    echo $layout;
} catch (Exception $ex) {
    echo "oooops";
}

