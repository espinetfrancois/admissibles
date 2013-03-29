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
			require_once(APPLICATION_PATH . '/classes/'
					. str_replace('_', '/', $class) . ".class.php");
		}
	} catch (Exception $e) {
		throw new Exception(
				"Erreur lors du chargement de la classe : " . $class, null, $e);
	}
}

/**
 * Gère les exception non rattrappée
 * Redirige l'utilisateur vers la page des erreurs dans ce cas.
 * @author francois.espinet
 * @param Exception $exception
 */
function projet_exception_handler(Exception $exception)
{
	if (class_exists('Exception_Projet', true)) {
		$e = new Exception_Projet("Exception non capturée", null, $exception);
		//gestion de l'exception (redirection vers la page des erreurs
		$e->handleException();
	} else {
		//si c'est vraiment le chaos, on rend la main à php
		restore_exception_handler();
	}
}

/**
 * Gère les erreurs générales de php et les transforme en exceptions (mais pas les erreurs fatales)
 * @author francois.espinet
 * @param unknown $errno
 * @param unknown $errstr
 * @param unknown $errfile
 * @param unknown $errline
 * @throws Exception_Error
 */
function projet_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
	if (!(error_reporting() & $errno)) {
		// Ce code d'erreur n'est pas inclus dans error_reporting()
		return;
	}
	if (class_exists('Exception_Error', true)) {
		throw new Exception_Error($errno, $errstr, $errfile, $errline);
		//return false; //continue l'execution?
		return true;
	} else {
		//si c'est vraiment grave, on revient à l'ancien (php)
		restore_error_handler();
	}
}

/**
 * Gestion des erreurs fatales
 * @author francois.espinet
 */
// function projet_shutdown()
// {
// 	$error = error_get_last();
// 	if ($error) {
// 		switch ($error['type']) {
// 		case E_ERROR:
// 		case E_CORE_ERROR:
// 		case E_COMPILE_ERROR:
// 			$e = new Exception_Projet("Erreur Fatale", null,new Exception_Error($error['type'], $error['message'], $error['file'], $error['line'], "Erreur fatale"));
// 			$e->handleException();
// 			break;
// 		}
// 	}
// }

spl_autoload_register('autoloader');
set_exception_handler('projet_exception_handler');
set_error_handler('projet_error_handler', E_ALL);
// register_shutdown_function('projet_shutdown');
