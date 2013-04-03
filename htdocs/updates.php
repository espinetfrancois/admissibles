<?php
session_start();

define('ROOT_PATH', realpath(dirname(__FILE__).'/../'));
define('APPLICATION_PATH', ROOT_PATH.'/application');
define('LIBRARY_PATH', APPLICATION_PATH.'/library');

if ($_SESSION['administrateur'] !== true) {
    if (file_exists(APPLICATION_PATH.'/inc/fkz_auth.php')) {
    	include_once(APPLICATION_PATH.'/inc/fkz_auth.php');
    	frankiz_do_auth("/updates");
    	exit();
    } else {
        echo "L'application tourne en mode bare. (première installation?)";
        $_SESSION['administrateur'] = true;
        header('Location:/updates');
    }
}

try {
    //definition de l'endroit au se trouve le gestionnaire de mises à jour
    define('UPDATES_PATH',ROOT_PATH.'/updates');

    if (file_exists(APPLICATION_PATH.'/inc/autoload.php')) {
        //on sait jamais
        include_once(APPLICATION_PATH.'/inc/autoload.php');
        Config::constantes();
        $layout = new Layout();

        ob_start();
            include(UPDATES_PATH.'/index.php');
        $layout->addContent(ob_get_clean());

        echo $layout;
    } else {
        echo include(UPDATES_PATH.'/index.php');
        throw new Exception("L'application tourne en mode bare.");
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
