<?php

/**
 * Classe de configuration du projet
 * 	Définit les constantes de l'application
 * @author francois
 *
 */
class Config {
	
	public function __construct() {
		self::defineConstantes();
		self::setErrors();
	}
	
	/**
	 * Définition des constantes de l'application
	 */
	static function defineConstantes() {
		define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
		define('PUBLIC_PATH', ROOT_PATH."/public");
		define('CSS_PATH', PUBLIC_PATH."/css");
		define('JS_PATH', PUBLIC_PAHT."/js");
		define('LIBRARY_PATH', PUBLIC_PATH."/library");
		define('APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));
	}
	
	static function setErrors() {
		if (APP_ENV != "production") {
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors', 1);
		}
	}
}