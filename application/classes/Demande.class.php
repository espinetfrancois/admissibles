<?php
/**
 * Classe représentant un admissible et sa demande de logement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

class Demande {

    /**
     * Identifiant unique
     * @var int
     * @access protected
     */
    protected  $id;

    /**
     * 
     * @var string
     * @access protected
     */
    protected  $nom;

    /**
     * 
     * @var string
     * @access protected
     */
    protected  $prenom;

    /**
     * Adresse email de contact de l'admissible
     * @var string
     * @access protected
     */
    protected  $email;

    /**
     * M || F
     * @var string
     * @access protected
     */
    protected  $sexe;

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
     * Série d'admissibilité
     * @var int
     * @access protected
     */
    protected  $serie;
    
    /**
     * Sport préféré
     * @var int
     * @access protected
     */
    protected  $sport;

    /**
     * Nom d'utilisateur de l'élève polytechnicien accueillant l'admissible
     * @var string
     * @access protected
     */
    protected  $userEleve;

    /**
     * Statut de la demande de logement
     * @var int
     * @access protected
     */
    protected  $status;

    /**
     * Identifiant unique transmis lors des requêtes GET
     * @var string
     * @access protected
     */
    protected  $code;

    /**
     * Erreurs de remplissage des attributs
     * @var array
     * @access protected
     */
    protected  $erreurs;

    /**
     * Constantes relatives aux erreurs possibles rencontrées lors de l'exécution de la méthode
     */
    const ID_INVALIDE = 1;
    const NOM_INVALIDE = 2;
    const PRENOM_INVALIDE = 3;
    const EMAIL_INVALIDE = 4;
    const SEXE_INVALIDE = 5;
    const PREPA_INVALIDE = 6;
    const FILIERE_INVALIDE = 7;
    const SERIE_INVALIDE = 8;
    const SPORT_INVALIDE = 9;
    const USER_INVALIDE = 10;
    const STATUS_INVALIDE = 11;
    const CODE_INVALIDE = 12;
    const NON_ADMISSIBLE = 13;

    /**
     * Constructeur de la classe qui assigne les données spécifiées en paramètre aux attributs correspondants
     * @access public
     * @param array $valeurs Les valeurs à assigner
     * @return void
     */

    public  function __construct($valeurs = array())
    {
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

    public  function hydrate($donnees)
    {
        foreach ($donnees as $attribut => $valeur) {
            $methode = 'set'.ucfirst($attribut);
            if (is_callable(array($this, $methode))) {
                $this->$methode($valeur);
            }
        }
    }


    /**
     * Méthode permettant de savoir si les attributs sont valides
     * @access public
     * @return bool
     */

    public final  function isValid()
    {
        return !(empty($this->nom) || empty($this->prenom) || empty($this->email) || empty($this->sexe) || empty($this->prepa) || empty($this->filiere) || empty($this->sport));
    }
    
    
    /**
     * @access public
     * @param string $id 
     * @return void
     */

    public  function setId($id)
    {
        $this->id = (int) $id;
    }
    

    /**
     * @access public
     * @param string $nom 
     * @return void
     */

    public  function setNom($nom)
    {
        if (!preg_match('#[a-zA-Zéèàêâùïüë_-]+#',$nom)) { // lettres seulement
            $this->erreurs[] = self::NOM_INVALIDE;
        } else {
            $this->nom = $nom;
        }
    }


    /**
     * @access public
     * @param string $prenom 
     * @return void
     */

    public  function setPrenom($prenom)
    {
        if (!preg_match('#[a-zA-Zéèàêâùïüë_-]+#',$prenom)) { // lettres seulement
            $this->erreurs[] = self::PRENOM_INVALIDE;
        } else {
            $this->prenom = $prenom;
        }
    }


    /**
     * @access public
     * @param string $email 
     * @return void
     */

    public  function setEmail($email)
    {
        if (!preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#',$email)) { // adresse email
            $this->erreurs[] = self::EMAIL_INVALIDE;
        } else {
            $this->email = $email;
        }
    }


    /**
     * @access public
     * @param string $sexe 
     * @return void
     */

    public  function setSexe($sexe)
    {
        if ($sexe != 'M' && $sexe != 'F') { // de la forme M ou F
            $this->erreurs[] = self::SEXE_INVALIDE;
        } else {
            $this->sexe = $sexe;
        }
    }


    /**
     * @access public
     * @param string $prepa 
     * @return void
     */

    public  function setPrepa($prepa)
    {
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

    public  function setFiliere($filiere)
    {
        if (!is_numeric($filiere)) { // id numérique
            $this->erreurs[] = self::FILIERE_INVALIDE;
        } else {
            $this->filiere = $filiere;
        }
    }


    /**
     * @access public
     * @param int $serie 
     * @return void
     */

    public  function setSerie($serie)
    {
        if (!is_numeric($serie)) {
            $this->erreurs[] = self::SERIE_INVALIDE;
        } else {
            $this->serie = $serie;
        }
    }


    /**
     * @access public
     * @param int $sport 
     * @return void
     */

    public  function setSport($sport)
    {
        if (!is_numeric($sport)) { // id numérique
            $this->erreurs[] = self::SPORT_INVALIDE;
        } else {
            $this->sport = $sport;
        }
    }

    /**
     * @access public
     * @param string $user 
     * @return void
     */

    public  function setUserEleve($user)
    {
        if (!preg_match('#[a-z_-]+\.[a-z_-]+#',$user)) { // de la forme prenom.nom
            $this->erreurs[] = self::USER_INVALIDE;
        } else {
            $this->user = $user;
        }
    }


    /**
     * @access public
     * @param int $status 
     * @return void
     */

    public  function setStatus($status)
    {
        if (!is_numeric($status)) { // id numérique
            $this->erreurs[] = self::STATUS_INVALIDE;
        } else {
            $this->status = $status;
        }
    }


    /**
     * @access public
     * @return string
     */

    public  function nom()
    {
        return $this->nom;
    }


    /**
     * @access public
     * @return string
     */

    public  function prenom()
    {
        return $this->prenom;
    }


    /**
     * @access public
     * @return string
     */

    public  function email()
    {
        return $this->email;
    }


    /**
     * @access public
     * @return string
     */

    public  function sexe()
    {
        return $this->sexe;
    }


    /**
     * @access public
     * @return string
     */

    public  function prepa()
    {
        return $this->prepa;
    }


    /**
     * @access public
     * @return string
     */

    public  function filiere()
    {
        return $this->filiere;
    }


    /**
     * @access public
     * @return int
     */

    public  function serie()
    {
        return $this->serie;
    }
    
    /**
     * @access public
     * @return int
     */

    public  function sport()
    {
        return $this->sport;
    }


    /**
     * @access public
     * @return string
     */

    public  function userEleve()
    {
        return $this->userEleve;
    }


    /**
     * @access public
     * @return int
     */

    public  function status()
    {
        return $this->status;
    }


    /**
     * @access public
     * @return string
     */

    public  function code()
    {
        return $this->code;
    }


    /**
     * @access public
     * @return array
     */

    public  function erreurs()
    {
        return $this->erreurs;
    }

}
?>
