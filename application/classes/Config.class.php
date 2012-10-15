<?php

/**
 * Classe de configuration du projet
 * Définit les constantes de l'application
 * @author francois
 *
 */


class Config {

	/**
     * @var string
     * @access protected
     */
    protected $_dbhost;
	
	/**
     * @var string
     * @access protected
     */
    protected $_dblogin;
	
	/**
     * @var string
     * @access protected
     */
    protected $_dbbase;
	
	/**
     * @var string
     * @access protected
     */
    protected $_dbpass;
	
	/**
     * @var string
     * @access protected
     */
    protected $_otherparam = array();


	/**
     * Constructeur de la classe
     * @access public
     * @return void
     */
    public function __construct() {
        self::defineConstantes();
        self::setErrors();
        $this->loadConfig();
    }


	/**
     * @access public
     * @return string
     */
    public function get_dbhost() {
        return $this->_dbhost;
    }


	/**
     * @access public
     * @return string
     */
    public function get_dblogin() {
        return $this->_dblogin;
    }


	/**
     * @access public
     * @return string
     */
    public function get_dbbase() {
        return $this->_dbbase;
    }


	/**
     * @access public
     * @return string
     */
    public function get_dbpass() {
        return $this->_dbpass;
    }


    /**
     * Définition des constantes de l'application
	 * @access public
     * @return void
     */
    static function defineConstantes() {
        //define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
        define('CONFIG_PATH', APPLICATION_PATH."/configs");
        define('PUBLIC_PATH', ROOT_PATH."/public");
        define('PAGES_PATH', APPLICATION_PATH.'/pages');
        define('TEMPLATE_PATH', APPLICATION_PATH.'/template');
        
        define('HTTP_PUBLIC_PATH', '');
        define('HTTP_CSS_PATH', HTTP_PUBLIC_PATH."/css");
        define('HTTP_JS_PATH', HTTP_PUBLIC_PATH."/js");
        define('HTTP_LIBRARY_PATH', HTTP_PUBLIC_PATH."/library");
        define('HTTP_IMAGES_PATH', HTTP_PUBLIC_PATH.'/images');
        
        define('APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));
    }


	/**
	 * Ajuste le niveau de verbosité des erreurs
	 * @access public
     * @return void
     */
    static function setErrors() {
        if (APP_ENV != "production") {
            ini_set('error_reporting', E_ALL);
            ini_set('display_errors', 1);
        }
    }


	/**
	 * Chargement du fichier de configuration
	 * @access protected
     * @return void
     */
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


	/**
	 * Hydratation
	 * @access protected
     * @return void
     */
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


	/**
	 * Lecture de paramètres facultatifs
	 * @access public
     * @return void
     */
    public function get_otherparam() {
        return $this->_otherparam;
    }
}