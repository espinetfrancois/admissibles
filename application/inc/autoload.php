<?php
/**
 * Fichier inclut dans chaque page (index.php) : fonction de chargement automatique des classes
 * @author François Espinet
 * @version 1.0
 */

/**
 * Fonction de chargement automatique des classes
 * Le format des classe peut être en Prefix_Suffixe1_Suffixe2_...
 * La classe est alors cherchée dans le dossier Prefix/Suffixe1/Suffixe2...
 * @param string $class Nom de la classe à insérer
 * @return void
 */
function autoloader($class) {
	try {
		$sDefaultClass = APPLICATION_PATH . '/classes/' . $class . ".class.php";
		if (file_exists($sDefaultClass)) {
			require_once($sDefaultClass);
		} else {
			require_once(APPLICATION_PATH . '/classes/' . str_replace('_', '/', $class) . ".class.php");
		}
	} catch (Exception $e) {
		throw new Exception("Erreur lors du chargement de la classe : " . $class, null, $e);
	}
}

spl_autoload_register('autoloader');
