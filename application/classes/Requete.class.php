<?php

/**
 * Classe représantant la requete d'un utilisateur
 * @author francois.espinet
 * @version 1.0
 *
 */
class Requete
{

//     /**
//      * Profondeur maximale de la requete
//      * la profondeur de /qqqche/qqche/qqche est 3
//      * @var int
//      */
//     const Profondeur_Max_Requete = 2;

//     /**
//      * Le préfixe de la requête, peut être nul
//      * @var string
//      */
//     public $prefixe = null;

//     /**
//      * Le suffixe de la requête
//      * Peut être nul dans le cas de la demande de l'url /
//      * @var string
//      */
//     public $suffixe = null;
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
//         //on test si la requètre fait la bonne longueur
//         if (count($aRequeteParts) > self::Profondeur_Max_Requete) {
//             //si la requette est trop longue, elle est invalide
//             $this->is_invalide = true;
//         } elseif(count($aRequeteParts) == self::Profondeur_Max_Requete) {
//             //si elle est tout juste de la bonne taille, on remplit le prefix et le suffixe
//             $this->prefixe = $aRequeteParts[0];
//             $this->suffixe = $aRequeteParts[1];
//         } elseif(count($aRequeteParts) == 1) {
//             //demande de qqch différent de l'accueil
//             if ($aRequeteParts[0] != '') {
//                 $this->suffixe = $aRequeteParts[0];
//             }
//         }

    }

    public function compact() {
        return urlencode(implode('/', $this->aParts));
    }

}