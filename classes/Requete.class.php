<?php

/**
 * Classe représantant la requete d'un utilisateur
 * @author francois.espinet
 *
 */
class Requete {
	
	/**
	 * Profondeur maximale de la requete
	 * la profondeur de /qqqche/qqche/qqche est 3
	 * @var int
	 */
	const PROFONDEUR_MAX_REQUETE = 2;
	/**
	 * Le préfixe de la requête, peut être nul
	 * @var string
	 */
	public $prefixe = null;
	
	/**
	 * Le suffixe de la requête
	 * Peut être nul dans le cas de la demande de l'url /
	 * @var string
	 */
	public $suffixe = null;
	
	/**
	 * Dis si la requete est valide ou non
	 * @var boolean
	 */
	public $is_invalide = false;
	/**
	 * Constructeur de la classe reque^te
	 * @author francois.espinet
	 * @param string $request la requete de l'utilisateur
	 */
	public function __construct($request) {
		$this->_setRequete($request);
	}
	
	/**
	 * Méthode qui analyse la requête et remplit les champs
	 * @author francois.espinet
	 * @param string $request la requete de l'utilisateur
	 */
	protected function _setRequete($request) {
		$aRequeteParts = explode('/', $request);
		//le +1 se justifie par le fait que le premier élément est toujours ""
		if (count($aRequeteParts) > self::PROFONDEUR_MAX_REQUETE+1) {
			$this->is_invalide = true;
		} elseif(count($aRequeteParts) == self::PROFONDEUR_MAX_REQUETE+1) {
			$this->prefixe = $aRequeteParts[1];
			$this->suffixe = $aRequeteParts[2];
		} elseif(count($aRequeteParts) == self::PROFONDEUR_MAX_REQUETE) {
			//demande de qqch différent de l'accueil
			if ($aRequeteParts[1] != '') {
				$this->suffixe = $aRequeteParts[1];
			}
		}
	}
}