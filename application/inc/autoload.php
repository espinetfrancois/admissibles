<?php
/**
 * Fichier inclut dans chaque page (index.php) : fonction de chargement automatique des classes
 * @author François Espinet
 * @version 1.0
 *
 */

/**
 * Fonction de chargement automatique des classes
 * @param string $class Nom de la classe à insérer
 * @return void
 */
function autoloader($class) {
    try {
        require_once(APPLICATION_PATH."/classes/".$class.".class.php");
    } catch(Exception $e) {
        throw new Exception("Erreur lors du chargement de la classe : ".$class);
    }

}

//include des librairies
require_once(LIBRARY_PATH.'/phpmailer/phpmailer.class.php');
spl_autoload_register('autoloader');