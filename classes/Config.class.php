<?php

/**
 * Classe de configuration du projet
 *     Définit les constantes de l'application
 * @author francois
 *
 */


class Config {

    protected $_dbhost;
    protected $_dblogin;
    protected $_dbbase;
    protected $_dbpass;
    protected $_otherparam = array();

    public function get_dbhost() {
        return $this->_dbhost;
    }

    public function get_dblogin() {
        return $this->_dblogin;
    }

    public function get_dbbase() {
        return $this->_dbbase;
    }

    public function get_dbpass() {
        return $this->_dbpass;
    }


    public function __construct() {
        self::defineConstantes();
        self::setErrors();
        $this->loadConfig();
    }

    /**
     * Définition des constantes de l'application
     */
    static function defineConstantes() {
        define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
        define('CONFIG_PATH', ROOT_PATH."/configs");
        define('PUBLIC_PATH', ROOT_PATH."/public");
        
        define('HTTP_PUBLIC_PATH', '/public');
        define('CSS_PATH', HTTP_PUBLIC_PATH."/css");
        define('JS_PATH', HTTP_PUBLIC_PATH."/js");
        define('LIBRARY_PATH', HTTP_PUBLIC_PATH."/library");
        
        define('PAGES_PATH', PUBLIC_PATH.'/pages');
        define('TEMPLATE_PATH', PUBLIC_PATH.'/template');
        
        define('APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));
    }

    static function setErrors() {
        if (APP_ENV != "production") {
            ini_set('error_reporting', E_ALL);
            ini_set('display_errors', 1);
        }
    }

    protected function loadConfig() {
        if (APP_ENV != "production") {
            $file = CONFIG_PATH."/dev.ini";
        } else {
            $file = CONFIG_PATH."/prod.ini";
        }

        $config = parse_ini_file($file, true);
        foreach ($config as $item=>$configitem) {
            switch ($item) {
                case "bdd":
                    $this->initBdd($configitem);
                    break;
                    /* case "libraries" :
                     $this->initLibrary($configitem);
                    break; */
                default:
                    array_merge($this->_otherparam,$configitem);
                    break;
            }
        }

    }

    protected function initBdd($aConfig) {
        $this->_dbhost = $aConfig['host'];
        $this->_dblogin = $aConfig['login'];
        $this->_dbbase = $aConfig['base'];
        $this->_dbpass = $aConfig['password'];
    }

    /* protected function initLibrary($aConfig) {
        foreach ($aConfig as $key => $value) {
    $aKeys = explode('.', $key);
        
    }
    } */


    public function get_otherparam() {
        return $this->_otherparam;
    }
}