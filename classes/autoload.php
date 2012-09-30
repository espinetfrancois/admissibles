<?php
function autoloader($class) {
	try {
		include($class.".class.php");
	} catch(Exception $e) {
		throw new Exception("Erreur lors du chargement de la classe : ".$class);
	}

}


spl_autoload_register('autoloader');