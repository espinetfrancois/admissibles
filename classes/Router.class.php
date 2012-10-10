<?php

/**
 * Classe de routage de url
 * @author francois.espinet
 *
 */
class Router {

	protected $urlAdmin = array();
	protected $urlAdmissible = array();
	protected $urlX = array();
	protected $accueil = null;

	const URL_ACCUEIL = 'accueil';

	public $file = null;

	const SECTION_X = 'x';
	const SECTION_ADMISSIBLE = "admissible";
	const SECTION_ADMIN = "admin";

	const ADMIN_PREFIX = 'administration';
	const X_PREFIX = 'x';
	const ADMISSIBLES_PREFIX = 'admissibles';

	const INI_FILE = "router.ini";

	public function __construct($request) {
		$this->_loadIni(CONFIG_PATH.'/'.self::INI_FILE);
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


	private function _setFileFromUrl($request) {
		$aUrlParts = explode('/', $request);
		$this->file = PAGES_PATH.'/';
		if (count($aUrlParts) > 0) {
			//die(var_export($aUrlParts));
			switch ($aUrlParts[1]) {
				case self::ADMIN_PREFIX:
					$this->file .= $this->urlAdmin[$aUrlParts[2]];
					break;
				case self::X_PREFIX:
					$this->file .= $this->urlX[$aUrlParts[2]];
					break;
				case self::ADMISSIBLES_PREFIX:
					$this->file .= $this->urlAdmissible[$aUrlParts[2]];
					break;
				default:
					$this->file .= $this->accueil;
					break;
			}
		} else {
			$this->file .= $this->accueil;
		}
	}

}
