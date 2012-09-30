<?php
/**
 * Fichier inclut dans chaque page : fonction de chargement automatique des classes
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
		include($class.".class.php");
	} catch(Exception $e) {
		throw new Exception("Erreur lors du chargement de la classe : ".$class);
	}

}

spl_autoload_register('autoloader');

?>
