<?php

/**
 * Classe de routage de url
 * @author francois.espinet
 *
 */


class Router {

    /**
     * Tableau des url d'administration
     * @var array d'url
     */
    protected $urls = array();

    protected $prefixes = array();

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
     * Section spéciale pour les routes sans préfixes
     * @var string
     */

    /**
     * Le fichier ini contenant les routes.
     * @var string
     */
    const INI_FILE = "router.ini";

    const SECTION_ROOT = "root";

    /**
     * le layout de l'application
     * @var Layout
     */
    protected $layout = null;
    /**
     * Constructeur, prend en argument l'url demandée ($_SERVER['REQUEST_URI'])
     * @author francois.espinet
     * @param string $request l'url demandée par l'utilisateur
     * @param Layout $layout le l'objet layout de l'application
     */
    public function __construct($request, $layout) {
        $this->layout = $layout;
        //chargement du fichier ini, initialisation des tableaux
        $this->_loadIni(CONFIG_PATH.'/'.self::INI_FILE);
        //initialisation de l'objet requete
        $this->requete = new Requete($request);

        //si la requete est invalide
        if ($this->requete->is_invalide) {
            $this->layout->not_found = true;
            $this->setAccueil();
        }

        //si la requete est valide, on met en marche le mécanisme de routage.
        $this->_setFileFromUrl();

        //die($this->file);
    }

    /**
     * Chargement du fichier contenant les routes
     * @author francois.espinet
     */
    private function _loadIni($IniFile) {
        if ( ($urls = parse_ini_file($IniFile, true)) ) {
            $this->prefixes = $urls['prefixes'];
            $this->urls = array(self::SECTION_ROOT => $urls[self::SECTION_ROOT]);
            foreach ($this->prefixes as $keypref => $prefixe ) {
                $this->urls[$prefixe] = $urls[$keypref];
            }
        } else {
            throw new Exception("Impossible de charger le fichier de configuration du routeur");
        }
    }

    private function setAccueil() {
        $this->file = PAGES_PATH.'/'.$this->urls[self::SECTION_ROOT]['accueil'];
        return;
    }

    /**
     * @brief conversion de l'url en fichier à charger
     * Découpe l'url et retourne le fichier demandée en fonction des morceaux de l'url
     * @author francois.espinet
     */
    private function _setFileFromUrl() {
        $this->file = PAGES_PATH.'/';
        if ($this->requete->prefixe != null) {
            $prefix = $this->__traitementPrefixe();
            if (! $this->layout->not_found) {
                $this->__traitementSuffixe($prefix);
            }
        } else {
            $this->__traitementSuffixe();
        }

        if ($this->layout->not_found) {
            $this->setAccueil();
        }
            
    }

    private function __traitementPrefixe() {
        if (array_key_exists($this->requete->prefixe, $this->urls)) {
            return $this->requete->prefixe;
        } else {
            $this->layout->not_found = true;
        }
    }

    private function __traitementSuffixe($prefix = self::SECTION_ROOT) {
        if (array_key_exists($this->requete->suffixe, $this->urls[$prefix]) ) {
            $this->file .= $this->urls[$prefix][$this->requete->suffixe];
            if ($this->requete->prefixe == 'administration') {
                $this->layout->is_admin = true;
            }
        } elseif ($this->requete->suffixe == null) {
            $this->setAccueil();
        } else {
            $this->layout->not_found = true;
        }
    }

}
