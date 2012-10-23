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
    const INI_FILE = 'router.ini';

    const SECTION_ROOT = 'root';

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
    public function __construct($request, $layout)
    {
        $this->layout = $layout;
        //chargement du fichier ini, initialisation des tableaux
        $this->_loadIni(CONFIG_PATH.'/'.self::INI_FILE);
        //initialisation de l'objet requete
        $this->requete = new Requete($request);

        //si la requete est invalide
        if ($this->requete->is_invalide) {
            //on retourne l'accueil et page non trouvée
            $this->layout->not_found = true;
            $this->setAccueil();
            return;
        }

        //si la requete est valide, on met en marche le mécanisme de routage.
        $this->_setFileFromUrl();
    }

    /**
     * Chargement du fichier contenant les routes
     * @author francois.espinet
     */
    private function _loadIni($IniFile)
    {
        if ( ($urls = parse_ini_file($IniFile, true)) ) {
            //tableau des prefixes de l'application , correspond aux sections du fichier ini
            $this->prefixes = $urls['prefixes'];
            //tableau des url, contient suffixe de la route => fichier à charger
            $this->urls = array(self::SECTION_ROOT => $urls[self::SECTION_ROOT]);
            foreach ($this->prefixes as $keypref => $prefixe ) {
                $this->urls[$prefixe] = $urls[$keypref];
            }
        } else {
            throw new Exception('Impossible de charger le fichier de configuration du routeur');
        }
    }

    /**
     * Methode qui prevoit l'affichage de la page d'accueil
     * @author francois.espinet
     */
    private function setAccueil()
    {
        $this->file = PAGES_PATH.'/'.$this->urls[self::SECTION_ROOT]['accueil'];
        return;
    }

    /**
     * @brief conversion de l'url en fichier à charger
     * Découpe l'url et retourne le fichier demandée en fonction des morceaux de l'url
     * @author francois.espinet
     */
    private function _setFileFromUrl()
    {
        //on commence par mettre le chemin vers les pages
        $this->file = PAGES_PATH.'/';
        //si le prefix n'est pas null (url de la forme /prefix/suffixe)
        if ($this->requete->prefixe != null) {
            $prefix = $this->__traitementPrefixe();
            //si le prefix existe
            if (! $this->layout->not_found) {
                //on traite le suffixe avec le prefixe fournit
                $this->__traitementSuffixe($prefix);
            }
        } else {
            //on traite directement le suffixe
            $this->__traitementSuffixe();
        }
        //si la page n'est pas trouvée, on met l'accueil
        if ($this->layout->not_found) {
            $this->setAccueil();
        }

    }
    /**
     * Traitement du préfixe de la requete
     * @author francois.espinet
     * @return prefixe de la requete ou null si le prefix n'existe pas dans la base
     */
    private function __traitementPrefixe()
    {
        if (array_key_exists($this->requete->prefixe, $this->urls)) {
            //on retourne le prefix s'il existe
            return $this->requete->prefixe;
        } else {
            //on signale au layout que la page est non-trouvée
            $this->layout->not_found = true;
            return null;
        }
    }

    /**
     * Traitement de la partie suffixe de la requête
     * @author francois.espinet
     * @param string $prefix les prefixe précédement calculé (peut-être nul!)
     */
    private function __traitementSuffixe($prefix = self::SECTION_ROOT)
    {
        if ($this->requete->suffixe == null) {
            // si le suffixe est nul, on renvoie l'accueil
            $this->setAccueil();
        } elseif (array_key_exists($this->requete->suffixe, $this->urls[$prefix]) ) {
            //on ajout le fichier signifié dans l'ini au fichier à charger
            $this->file .= $this->urls[$prefix][$this->requete->suffixe];
            //si on est dans l'administration
            if ($this->requete->prefixe == 'administration') {
                //on ajoute le menu d'administration
                $this->layout->is_admin = true;
            }
        } else {
            //sinon, on signale que la page est inconnue
            $this->layout->not_found = true;
        }
    }

}
