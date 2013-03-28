<?php
/**
 * Classe de configuration du projet
 * Définit les constantes de l'application ainsi que les paramètres dans les différents fichiers ini.
 * Elle est appelée à chaque requète (dans le fichier index.php) et est necessaire au bon fonctionnement de l'application.
 * Cette classe à tout à fait sa place dans le registre
 * @author francois.espinet
 * @version 1.0
 * @todo : caching
 */

class Config {

    /**
     * Tableau de configuration de la base de donnée
     * @var array
     */
    protected $_dbconf = null;

    /**
     * Autres paramètres
     * @var string
     * @access protected
     */
    protected $_otherparam = array();

    /**
     * Paramètres de connexion à frankiz
     * @var array
     */
    protected $_frankiz = array();

    /**
     * Constructeur de la classe
     * @access public
     * @return void
     */
    public function __construct()
    {
        //attention à ce que cette fonction (defineConstantes) soit appelée en premier
        self::defineConstantes();
        self::addLibraries();
        $this->loadConfig();
    }

    /**
     * Définition des constantes de l'application.
     * La visibilité évite les doubles définitions
     * @access protected
     * @return void
     */
    static protected function defineConstantes()
    {
        //define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
        define('CONFIG_PATH', APPLICATION_PATH.'/configs');
        define('PUBLIC_PATH', ROOT_PATH.'/public');
        define('PAGES_PATH', APPLICATION_PATH.'/pages');
        define('CLASS_PATH', APPLICATION_PATH.'/classes');

        //définition des template
        define('TEMPLATE_PATH', APPLICATION_PATH.'/template');
        define('MENUS_PATH', TEMPLATE_PATH.'/menus');

        //définition des logs pour les données
        define('DATA_PATH', ROOT_PATH.'/data');
        define('LOGS_PATH', DATA_PATH.'/logs');

        define('HTTP_PUBLIC_PATH', '');
        define('HTTP_CSS_PATH', HTTP_PUBLIC_PATH.'/css');
        define('HTTP_JS_PATH', HTTP_PUBLIC_PATH.'/js');
        define('HTTP_LIBRARY_PATH', HTTP_PUBLIC_PATH.'/library');
        define('HTTP_IMAGES_PATH', HTTP_PUBLIC_PATH.'/images');

        //constantes d'environement
        define('APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));
        define('APP_MAIL', false);
        define('APP_CACHE', false);

        //définition des niveaux de messages
        define('MSG_LEVEL_ERROR', 'error');
        define('MSG_LEVEL_OK', 'ok');
        define('MSG_LEVEL_WARNING', 'warning');
    }

    /**
     * Include des libraries tierces
     * @access public
     * @return void
     */
    static function addLibraries()
    {
        require_once(LIBRARY_PATH.'/phpmailer/phpmailer.class.php');
    }

    /**
     * Chargement du fichier de configuration (.ini)
     * @access protected
     * @return void
     */
    protected function loadConfig()
    {
        if (APP_ENV != 'production') {
            $file = CONFIG_PATH.'/dev.ini';
        } else {
            $file = CONFIG_PATH.'/prod.ini';
        }
        $config = parse_ini_file($file, true);
        foreach ($config as $item=>$configitem) {
            switch ($item) {
                case 'bdd':
                    $this->initBdd($configitem);
                    break;
                case 'frankiz':
                    $this->initFrankiz($configitem);
                    break;
                case 'php':
                    $this->initPhp($configitem);
                    break;
                default:
                    array_merge($this->_otherparam,$configitem);
                    break;
            }
        }
    }


    /**
     * Ajout et vérification de la configuration de la base de données
     * @access protected
     * @return void
     */
    protected function initBdd($aConfig)
    {
        $this->_dbconf = $aConfig;
        if (count($aConfig) < 4) {
            throw new Exception('Le fichier de configuration de la base de données est erroné.');
        }
    }

    /**
     * Initialisation de Frankiz
     * @author francois.espinet
     * @param unknown $aConfig
     */
    protected function initFrankiz($aConfig) {
        $this->_frankiz = $aConfig;
        if (count($aConfig) < 3) {
        	throw new Exception('Le fichier de configuration de Frankiz est erroné.');
        }
    }

    /**
     * Configuration de php
     * @author francois.espinet
     * @param array $aConfig
     */
    protected function initPhp($aConfig) {
        foreach ($aConfig as $item => $value) {
            ini_set($item, $value);
        }
    }


    /**
     * Lecture de paramètres facultatifs
     * @access public
     * @return void
     */
    public function get_otherparam()
    {
        return $this->_otherparam;
    }

    /**
     * Renvoie le tableau de configuration de frankiz
     * @author francois.espinet
     * @return array
     */
    public function getFrankiz()
    {
        return $this->_frankiz;
    }

    public function set_frankiz($_frankiz)
    {
        $this->_frankiz = $_frankiz;
    }

    /**
     * Renvoie l'host de la base de données
     * @access public
     * @return string
     */
    public function getDbhost()
    {
    	return $this->_dbconf['host'];
    }


    /**
     * Renvoie le login de la base de données
     * @access public
     * @return string
     */
    public function getDblogin()
    {
    	return $this->_dbconf['login'];
    }


    /**
     * Renvoie la base de donnée de la base de donnée
     * @access public
     * @return string
     */
    public function getDbbase()
    {
    	return $this->_dbconf['base'];
    }


    /**
     * Renvoie le mot de passe de la base de donnée
     * @access public
     * @return string
     */
    public function getDbpass()
    {
    	return $this->_dbconf['password'];
    }
}