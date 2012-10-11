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
	protected $urlAdmin = array();
	 
	/**
	 * Tableau des url des admissibles
	 * @var array d'url
	 */
	protected $urlAdmissible = array();
	/**
	 * Tableau des url pour les x
	 * @var array d'url
	 */
	protected $urlX = array();
	
	/**
	 * url de l'accueil
	 * @var string
	 */
	protected $accueil = null;

	/**
	 * Le filename du fichier à charger
	 * @var string
	 */
	public $file = null;
	
	/**
	 * Booléen qui indique si on a un 404
	 * @var bool page trouvée ou non
	 */
	public $not_found = false;

	/**
	 * Constantes pour les section dans le fichier ini
	 * @var string
	 */
	const SECTION_X = 'x';
	/**
	 * Constantes pour les section dans le fichier ini
	 * @var string
	 */
	const SECTION_ADMISSIBLE = "admissible";
	/**
	 * Constantes pour les section dans le fichier ini
	 * @var string
	 */
	const SECTION_ADMIN = "admin";

	/**
	 * Préfixe pour les url
	 * @var string
	 */
	const ADMIN_PREFIX = 'administration';
	/**
	 * Préfixe pour les url
	 * @var string
	 */
	const X_PREFIX = 'x';
	/**
	 * Préfixe pour les url
	 * @var string
	 */
	const ADMISSIBLES_PREFIX = 'admissibles';

	/**
	 * Le fichier ini contenant les routes.
	 * @var string
	 */
	const INI_FILE = "router.ini";

	
	/**
	 * Constructeur, prend en argument l'url demandée ($_SERVER['REQUEST_URI'])
	 * @author francois.espinet
	 * @param string $request l'url demandée par l'utilisateur
	 */
	public function __construct($request) {
		//chargement du fichier ini, initialisation des tableaux
		$this->_loadIni(CONFIG_PATH.'/'.self::INI_FILE);
		//recuperation du fichier en fonction de l'url demandée
		$this->_setFileFromUrl($request);
		
		//die($this->file);
	}

	/**
	 * Chargement du fichier contenant les routes
	 * @author francois.espinet
	 */
	private function _loadIni($IniFile) {
		if ( ($urls = parse_ini_file($IniFile, true)) ) {
			$this->urlAdmin = $urls[self::SECTION_ADMIN];
			$this->urlAdmissible = $urls[self::SECTION_ADMISSIBLE];
			$this->urlX = $urls[self::SECTION_X];
			$this->accueil = $urls['accueil'];
		} else {
			throw new Exception("Impossible de charger le fichier de configuration du routeur");
		}
	}

	/**
	 * @brief conversion de l'url en fichier à charger
	 * Découpe l'url et retourne le fichier demandée en fonction des morceaux de l'url
	 * @author francois.espinet
	 * @param string $request la requete de l'utilisateur
	 */
	private function _setFileFromUrl($request) {
		/* decomposision de l'url en morceaux selon /
		 Attention : $aUrlParts[0] contient "" */
		$aUrlParts = explode('/', $request);
		//set du prefix du filename du fichier
		$this->file = PAGES_PATH.'/';
		//si l'url n'est pas "/"
		if (count($aUrlParts) > 2) {
			$aPrefUrl = $this->__traitementPrefixe($aUrlParts[1]);
			if (! $this->not_found) {
				$this->__traitementSuffixe($aPrefUrl, $aUrlParts[2]);
			}
		} else {
			//sinon on renvoie l'accueil
			$this->file .= $this->accueil;
		}
		
		if (count($aUrlParts) > 3) {
			$this->not_found = true;
		} 
		
		if ($this->not_found) {
			$this->file = PAGES_PATH.'/'.$this->accueil;
		}
	}
	
	private function __traitementPrefixe($prefix) {
		//en fonction du prefixe
		switch ($prefix) {
			case self::ADMIN_PREFIX:
				return $this->urlAdmin;
				break;
			case self::X_PREFIX:
				return $this->urlX;
				break;
			case self::ADMISSIBLES_PREFIX:
				return $this->urlAdmissible;
				break;
			default:
				$this->not_found = true;
				return null;
				break;
		}
	}
	
	private function __traitementSuffixe(array $url, $suffixe) {
		if (array_key_exists($suffixe, $url)) {
			$this->file .= $url[$suffixe];
		} else {
			$this->not_found = true;
		}
	}

}
