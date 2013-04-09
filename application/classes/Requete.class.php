<?php

/**
 * Classe représantant la requete d'un utilisateur
 * @author francois.espinet
 * @version 1.0
 *
 */
class Requete
{

    /**
     * Les parties de la requète
     * @var array
     */
    public $aParts = array();

    /**
     * Dis si la requete est valide ou non
     * @var boolean
     */
    public $is_invalide = false;

    /**
     * La profondeur de la requête de l'utilisateur
     * @var int
     */
    public $depth = 0;

    /**
     * Constructeur de la classe reque^te
     * @author francois.espinet
     * @param string $request la requete de l'utilisateur
     */
    public function __construct($request)
    {
        $this->_setRequete($request);

    }

    /**
     * Méthode qui analyse la requête et remplit les champs
     * @author francois.espinet
     * @param string $request la requete de l'utilisateur
     */
    protected function _setRequete($request)
    {
        //contient tous les éléments de la requète
        $aAllRequestParts= parse_url($request);
        //séparation de la requête en éléments
        $aRequeteParts = explode('/', $aAllRequestParts['path']);
        //on enlève le premier membre qui est toujours ""
        array_shift($aRequeteParts);
        //gère le cas ou l'url finit par / (ex: /x/admissibles/)
        if (end($aRequeteParts) == "") {
            array_pop($aRequeteParts);
        }
        $this->depth = count($aRequeteParts);
        $this->aParts = $aRequeteParts;
    }

    /**
     * Retourne une formulation compacte de l'url demandée
     * @author francois.espinet
     * @return string
     */
    public function compact() {
        return urlencode(implode('/', $this->aParts));
    }
}