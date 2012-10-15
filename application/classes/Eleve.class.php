<?php
/**
 * Classe représentant l'élève X proposant un logement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo : gestion logs
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
     * @var int
     * @access protected
     */
    protected  $section;

    /**
     * Etablissement scolaire d'origine
     * @var int
     * @access protected
     */
    protected  $prepa;

    /**
     * Filière d'origine
     * @var int
     * @access protected
     */
    protected  $filiere;

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
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user)) { // de la forme prenom.nom
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
        if (!preg_match('#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-z]{2,4}$#',$email)) { // adresse email
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
        if (!is_numeric($promo)) { // id numérique
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
        if (!is_numeric($section)) { // id numérique
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
        if (!is_numeric($prepa)) { // id numérique
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
        if (!is_numeric($filiere)) { // id numérique
            $this->erreurs[] = self::FILIERE_INVALIDE;
        } else {
            $this->filiere = $filiere;
        }
    }
    
    
    /**
     * @access public
     * @return void
     */

    public  function setErreurs() {
        $this->erreurs = array();
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
     * @return array
     */

    public  function erreurs() {
        return $this->erreurs;
    }


}
?>
