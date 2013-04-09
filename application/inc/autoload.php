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
		throw new Exception("Erreur lors du chargement de la classe : " . $class, null, $e);
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
 * Gère les erreurs générales de php.
 * Plus particulièrement leur affichage à l'écran
 * @author francois.espinet
 * @param unknown $errno
 * @param unknown $errstr
 * @param unknown $errfile
 * @param unknown $errline
 * @throws Exception_Error
 */
function projet_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
    //si la classe layout est chargé, on améliore le rendu des erreurs
    if (Registry::isRegistered('layout') && ini_get('display_errors')) {
        Registry::get('layout')->addMessage('PHP : '.$errstr.' in '.$errfile.' line : '.$errline, MSG_LEVEL_ERROR);
        //on désactive l'affichage des erreurs puisqu'elle est déjà affichée (mais elle peut encore être loggée)
        ini_set('display_errors', false);
        //on continue le process de l'erreur par le handler php
        return false;
    } else {
        restore_error_handler();
        return false;
    }
}

spl_autoload_register('autoloader');
//gestion personnalisée des erreurs
set_exception_handler('projet_exception_handler');
set_error_handler('projet_error_handler', E_ALL);
