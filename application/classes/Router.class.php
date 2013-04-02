<?php

/**
 * Classe de routage de l'url.
 * L'utilisation de mod_rewrite d'apache permet une flexibilité qui s'éxprime ici!
 * @author francois.espinet
 * @version 1.0
 * @todo caching request
 */
class Router {

    protected $routes = array();

    protected $depth = 0;

    const Root_Keyword = 'root';

    const Cache_All    = false;

    /**
     * Le fichier php contenant les routes.
     * @var string
     */
    const Routes_File = 'router.php';

    /**
     * Le filename du fichier à charger
     * @var string
     */
    public $file = null;

    /**
     *
     * @var Requete
     */
    protected $requete = null;

    /**
     * le layout de l'application
     * @var Layout
     */
    protected $layout = null;

    const Cache_Index_Routes = 'routes';
    /**
     * Constructeur, prend en argument l'url demandée ($_SERVER['REQUEST_URI'])
     * @author francois.espinet
     * @param string $request l'url demandée par l'utilisateur
     * @param Layout $layout le l'objet layout de l'application
     */
    public function __construct($request, $layout)
    {
        $this->layout = $layout;
        $this->file = PAGES_PATH.'/';
        $this->requete = new Requete($request);

        //on regarde si le cache des routes existe
        if (Cache::test(self::Cache_Index_Routes)) {
            //cache des routes visites
            $vroutes = Cache::load(self::Cache_Index_Routes);
            //si la route est en cache on la renvoie
            if (array_key_exists($this->requete->compact(), $vroutes)) {
            	$this->file = $vroutes[$this->requete->compact()];
            	return;
            }
        }

        // si les routes ne sont pas en cache
        $this->_loadRoutes(CONFIG_PATH.'/'.self::Routes_File);
        $this->_setFileFromUrl();
        //on ne cache que les routes valides
        if (self::Cache_All || !$this->layout->page404) {
            $vroutes[$this->requete->compact()] = $this->file;
            Cache::save($vroutes, self::Cache_Index_Routes);
        }
    }

    /**
     * Chargement du fichier contenant les routes
     * @author francois.espinet
     * @todo   gestion des erreurs
     */
    private function _loadRoutes($RoutesFile)
    {
        $this->routes = require_once($RoutesFile);
    }

    /**
     * Methode qui prevoit l'affichage de la page d'accueil
     * @author francois.espinet
     */
    private function setAccueil()
    {
        $this->file = PAGES_PATH.'/'.$this->routes['accueil'];
    }

    /**
     * @brief conversion de l'url en fichier à charger
     * Découpe l'url et retourne le fichier demandée en fonction des morceaux de l'url
     * @author francois.espinet
     */
    private function _setFileFromUrl()
    {
        $a = $this->routes;
        if ($this->requete->depth == 0) {
            $this->setAccueil();
            return;
        }
        //tant qu'on est pas arrivé au bout, on continue la descente
        while (is_array($a) && $this->requete->depth >= $this->depth) {
            if (array_key_exists($this->requete->aParts[$this->depth], $a)) {
                $a = $a[$this->requete->aParts[$this->depth]];
            } elseif (array_key_exists(self::Root_Keyword, $a) && $this->depth == $this->requete->depth) {
                $a = $a[self::Root_Keyword];
            } else {
                $this->layout->page404 = true;
                $this->setAccueil();
                return;
            }
            $this->depth++;
        }

        //si les profondeurs ne sont pas les mêmes, la route n'a pas été trouvée
        if ($this->depth != $this->requete->depth) {
            $this->layout->page404 = true;
            $this->layout->addMessage("Cette page est la plus proche de celle que vous avez demandée.", MSG_LEVEL_WARNING);
        }

        $this->file .= $a;
    }
}
