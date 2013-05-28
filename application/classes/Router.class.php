<?php

/**
 * Classe de routage de l'url.
 * L'utilisation de mod_rewrite d'apache permet une flexibilité qui s'exprime ici!
 *
 * @author francois.espinet
 * @version 1.0
 */
class Router
{

    /**
     * L'objet requête de l'utilisateur.
     *
     * @var Requete
     */
    protected $_requete = null;

    /**
     * le layout de l'application.
     *
     * @var Layout
     */
    protected $_layout = null;

    /**
     * Tableau des routes de l'application.
     * Ces routes sont définies dans configs/router.php
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * La profondeur courante que traite le routeur.
     *
     * @var integer
     */
    protected $_depth = 0;

    /**
     * Le mot clé qui défini le mot utiliser pour la racine du niveau courant dans router.php.
     *
     * @var string
     */
    const Root_Keyword = 'root';

    /**
     * Le fichier php contenant les routes.
     *
     * @var string
     */
    const Routes_File = 'router.php';

    /**
     * Le filename du fichier à charger.
     *
     * @var string
     */
    public $file = null;

    /**
     * Index des routes dans le cache.
     *
     * @var string
     */
    const Cache_Index_Routes = 'routes';

    /**
     * Définit si l'on met en cache toutes les routes ou seulement les valides.
     *
     * @var boolean
     */
    const Cache_All = false;

    /**
     * Constructeur, prend en argument l'url demandée ($_SERVER['REQUEST_URI']).
     *
     * @author francois.espinet
     * @param string $request l'url demandée par l'utilisateur
     * @param Layout $layout le l'objet layout de l'application
     */
    public function __construct($request, $layout)
    {
        $this->_layout = $layout;
        $this->file = PAGES_PATH . '/';
        $this->_requete = new Requete($request);

        //on regarde si le cache des routes existe
        if (Cache::test(self::Cache_Index_Routes)) {
            //cache des routes visites
            $vroutes = Cache::load(self::Cache_Index_Routes);
            //si la route est en cache on la renvoie
            if (array_key_exists($this->_requete->compact(), $vroutes)) {
                $this->file = $vroutes[$this->_requete->compact()];
                return;
            }
        }

        // si les routes ne sont pas en cache
        $this->_loadRoutes(CONFIG_PATH . '/' . self::Routes_File);
        $this->_setFileFromUrl();
        //on ne cache que les routes valides
        if (self::Cache_All || !$this->_layout->page404) {
            $vroutes[$this->_requete->compact()] = $this->file;
            Cache::save($vroutes, self::Cache_Index_Routes);
        }
    }

    /**
     * Chargement du fichier contenant les routes.
     *
     * @author francois.espinet
     * @param string $routesFile le fichier des routes
     * @throws Exception_Router
     */
    private function _loadRoutes($routesFile)
    {
        if (file_exists($routesFile)) {
            $this->_routes = require_once($routesFile);
        } else {
            throw new Exception_Router('Le fichier des routes est absent.', Exception_Router::Routes_Not_Found);
        }
    }

    /**
     * Methode qui prevoit l'affichage de la page d'accueil.
     *
     * @author francois.espinet
     */
    private function setAccueil()
    {
        $this->file = PAGES_PATH . '/' . $this->_routes['accueil'];
    }

    /**
     * @brief conversion de l'url en fichier à charger.
     * Découpe l'url et retourne le fichier demandée en fonction des morceaux de l'url.
     *
     * @author francois.espinet
     */
    private function _setFileFromUrl()
    {
        $a = $this->_routes;
        if ($this->_requete->depth === 0) {
            $this->setAccueil();
            return;
        }
        //tant qu'on est pas arrivé au bout, on continue la descente
        while (is_array($a) && $this->_requete->depth >= $this->_depth) {
            if (array_key_exists($this->_depth, $this->_requete->aParts) === true && array_key_exists($this->_requete->aParts[$this->_depth], $a) === true) {
                $a = $a[$this->_requete->aParts[$this->_depth]];
                $this->_depth++;
            } else if (array_key_exists(self::Root_Keyword, $a) && $this->_depth === $this->_requete->depth) {
                $a = $a[self::Root_Keyword];
            } else {
                $this->_layout->page404 = true;
                $this->setAccueil();
                return;
            }
        }

        //si les profondeurs ne sont pas les mêmes, la route n'a pas été trouvée
        if ($this->_depth != $this->_requete->depth) {
            $this->_layout->page404 = true;
            $this->_layout->addMessage('Cette page est la plus proche de celle que vous avez demandée.', MSG_LEVEL_WARNING);
        }

        $this->file .= $a;
    }
}
