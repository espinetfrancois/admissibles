<?php
/**
 * Classe représentant l'élève X proposant un logement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

class Eleve {

    /**
     * nom d'utilisateur unique : prenom.nom
     * @var string
     * @access protected
     */
    protected  $user;

    /**
     * M || F
     * @var string
     * @access protected
     */
    protected  $sexe;

    /**
     * Adresse email
     * @var string
     * @access protected
     */
    protected  $email;

    /**
     * Promotion de la forme 20XX
     * @var int
     * @access protected
     */
    protected  $promo;

    /**
     * Section sportive
     * @var string
     * @access protected
     */
    protected  $section;

    /**
     * Etablissement scolaire d'origine
     * @var string
     * @access protected
     */
    protected  $prepa;

    /**
     * Filière d'origine
     * @var string
     * @access protected
     */
    protected  $filiere;

    /**
     * Disponibilité en série 1
     * @var int
     * @access protected
     */
    protected  $serie1;

    /**
     * Disponibilité en série 2
     * @var int
     * @access protected
     */
    protected  $serie2;

    /**
     * Disponibilité en série 3
     * @var int
     * @access protected
     */
    protected  $serie3;

    /**
     * Disponibilité en série 4
     * @var int
     * @access protected
     */
    protected  $serie4;

    /**
     * Erreurs de remplissage des attributs
     * @var array
     * @access protected
     */
    protected  $erreurs;

    /**
     * Constantes relatives aux erreurs possibles rencontrées lors de l'exécution de la méthode
     */
    const USER_INVALIDE = 1;
    const SEXE_INVALIDE = 2;
    const EMAIL_INVALIDE = 3;
    const PROMO_INVALIDE = 4;
    const SECTION_INVALIDE = 5;
    const PREPA_INVALIDE = 6;
    const FILIERE_INVALIDE = 7;
    const SERIE_INVALIDE = 8;

    /**
     * Constructeur de la classe qui assigne les données spécifiées en paramètre aux attributs correspondants
     * @access public
     * @param array $valeurs Les valeurs à assigner
     * @return void
     */

    public  function __construct($valeurs = array()) {
        if (!empty($valeurs)) { // Si on a spécifié des valeurs, alors on hydrate l'objet
            $this->hydrate($valeurs);
        }
    }


    /**
     * Méthode assignant les valeurs spécifiées aux attributs correspondant
     * @access public
     * @param array $donnees Les données à assigner
     * @return void
     */

    public  function hydrate($donnees) {
        foreach ($donnees as $attribut => $valeur) {
            $methode = 'set'.ucfirst($attribut);
            if (is_callable(array($this, $methode))) {
                $this->$methode($valeur);
            }
        }
    }


    /**
     * Méthode permettant de savoir si l'éleve est nouveau
     * @access public
     * @return bool
     */

    public  function isNew() {
        return empty($this->user);
    }


    /**
     * Méthode permettant de savoir si les attributs sont valides
     * @access public
     * @return bool
     */

    public final  function isValid() {
        return !(empty($this->user) || empty($this->sexe) || empty($this->promo) || empty($this->section) || empty($this->prepa) || empty($this->filiere) || empty($this->email));
    }


    /**
     * @access public
     * @param string $user 
     * @return void
     */

    public  function setUser($user) {
        if (!preg_match('#[a-z-_]+\.[a-z-_]+#',$user)) { // de la forme prenom.nom
            $this->erreurs[] = self::USER_INVALIDE;
        } else {
            $this->user = $user;
        }
    }


    /**
     * @access public
     * @param string $sexe 
     * @return void
     */

    public  function setSexe($sexe) {
        if ($sexe != "M" && $sexe != "F") { // de la forme M ou F
            $this->erreurs[] = self::SEXE_INVALIDE;
        } else {
            $this->sexe = $sexe;
        }
    }


    /**
     * @access public
     * @param string $email 
     * @return void
     */

    public  function setEmail($email) {
        if (!preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#',$email)) { // de la forme prenom.nom
            $this->erreurs[] = self::EMAIL_INVALIDE;
        } else {
            $this->email = $email;
        }
    }


    /**
     * @access public
     * @param int $promo 
     * @return void
     */

    public  function setPromo($promo) {
        if (!is_integer($promo)) { // id numérique
            $this->erreurs[] = self::PROMO_INVALIDE;
        } else {
            $this->promo = $promo;
        }
    }


    /**
     * @access public
     * @param string $section 
     * @return void
     */

    public  function setSection($section) {
        if (!is_integer($section)) { // id numérique
            $this->erreurs[] = self::SECTION_INVALIDE;
        } else {
            $this->section = $section;
        }
    }


    /**
     * @access public
     * @param string $prepa 
     * @return void
     */

    public  function setPrepa($prepa) {
        if (!is_integer($prepa)) { // id numérique
            $this->erreurs[] = self::PREPA_INVALIDE;
        } else {
            $this->prepa = $prepa;
        }
    }


    /**
     * @access public
     * @param string $filiere 
     * @return void
     */

    public  function setFiliere($filiere) {
        if (!is_integer($filiere)) { // id numérique
            $this->erreurs[] = self::FILIERE_INVALIDE;
        } else {
            $this->filiere = $filiere;
        }
    }


    /**
     * @access public
     * @param int $serie1 
     * @return void
     */

    public  function setSerie1($serie1) {
        if ($serie1 != 0 && $serie1 != 1) { // 0 ou 1
            $this->erreurs[] = self::SERIE_INVALIDE;
        } else {
            $this->serie1 = $serie1;
        }
    }


    /**
     * @access public
     * @param int $serie2 
     * @return void
     */

    public  function setSerie2($serie2) {
        if ($serie2 != 0 && $serie2 != 1) { // 0 ou 1
            $this->erreurs[] = self::SERIE_INVALIDE;
        } else {
            $this->serie2 = $serie2;
        }
    }


    /**
     * @access public
     * @param int $serie3 
     * @return void
     */

    public  function setSerie3($serie3) {
        if ($serie3 != 0 && $serie3 != 1) { // 0 ou 1
            $this->erreurs[] = self::SERIE_INVALIDE;
        } else {
            $this->serie3 = $serie3;
        }
    }


    /**
     * @access public
     * @param int $serie4 
     * @return void
     */

    public  function setSerie4($serie4) {
        if ($serie4 != 0 && $serie4 != 1) { // 0 ou 1
            $this->erreurs[] = self::SERIE_INVALIDE;
        } else {
            $this->serie4 = $serie4;
        }
    }


    /**
     * @access public
     * @return string
     */

    public  function user() {
        return $this->user;
    }


    /**
     * @access public
     * @return string
     */

    public  function sexe() {
        return $this->sexe;
    }


    /**
     * @access public
     * @return string
     */

    public  function email() {
        return $this->email;
    }


    /**
     * @access public
     * @return int
     */

    public  function promo() {
        return $this->promo;
    }


    /**
     * @access public
     * @return string
     */

    public  function section() {
        return $this->section;
    }


    /**
     * @access public
     * @return string
     */

    public  function prepa() {
        return $this->prepa;
    }


    /**
     * @access public
     * @return string
     */

    public  function filiere() {
        return $this->filiere;
    }


    /**
     * @access public
     * @return int
     */

    public  function serie1() {
        return $this->serie1;
    }


    /**
     * @access public
     * @return int
     */

    public  function serie2() {
        return $this->serie2;
    }


    /**
     * @access public
     * @return int
     */

    public  function serie3() {
        return $this->serie3;
    }


    /**
     * @access public
     * @return int
     */

    public  function serie4() {
        return $this->serie4;
    }


    /**
     * @access public
     * @return array
     */

    public  function erreurs() {
        return $this->erreurs;
    }


}
?>
