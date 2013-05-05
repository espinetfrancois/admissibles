<?php
/**
 * Classe représentant l'élève X proposant un logement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */
class Model_Eleve extends Model {

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
     * 1 || 0
     * @var int
     * @access protected
     */
    protected  $sexeAdm;

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
    const User_Invalide = 1;
    const Sexe_Invalide = 2;
    const Email_Invalide = 3;
    const Promo_Invalide = 4;
    const Section_Invalide = 5;
    const Prepa_Invalide = 6;
    const Filiere_Invalide = 7;
	const SexeAdm_Invalide = 8;

    /**
     * Méthode permettant de savoir si l'éleve est nouveau
     * @access public
     * @return bool
     */
    public  function isNew()
    {
        return empty($this->user);
    }


    /**
     * Méthode permettant de savoir si les attributs sont valides
     * @access public
     * @return bool
     */
    public final function isValid()
    {
        return !(empty($this->user) || empty($this->sexe) || empty($this->sexeAdm) || empty($this->promo) || empty($this->section) || empty($this->prepa) || empty($this->filiere) || empty($this->email));
    }


    /**
     * Setter user
     * @access public
     * @param string $user
     * @return void
     */
    public  function setUser($user)
    {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user)) { // de la forme prenom.nom
            $this->erreurs[] = self::User_Invalide;
        } else {
            $this->user = $user;
        }
    }


    /**
     * Setter sexe
     * @access public
     * @param string $sexe
     * @return void
     */
    public  function setSexe($sexe)
    {
        if ($sexe != 'M' && $sexe != 'F') { // de la forme M ou F
            $this->erreurs[] = self::Sexe_Invalide;
        } else {
            $this->sexe = $sexe;
        }
    }


	/**
     * Setter sexeAdm
     * @access public
     * @param int $sexeAdm
     * @return void
     */
    public  function setSexeAdm($sexeAdm)
    {
        if ($sexeAdm != 0 && $sexeAdm != 1) { // de la forme 0 ou 1
            $this->erreurs[] = self::SexeAdm_Invalide;
        } else {
            $this->sexe = $sexe;
        }
    }


    /**
     * Setter email
     * @access public
     * @param string $email
     * @return void
     */
    public  function setEmail($email)
    {
        if (!preg_match('#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-z]{2,4}$#',$email)) { // adresse email
            $this->erreurs[] = self::Email_Invalide;
        } else {
            $this->email = $email;
        }
    }


    /**
     * Setter promo
     * @access public
     * @param text $promo
     * @return void
     */
    public  function setPromo($promo)
    {
        $this->promo = $promo;
    }


    /**
     * Setter section
     * @access public
     * @param text $section
     * @return void
     */
    public  function setSection($section)
    {
       $this->section = $section;
    }


    /**
     * Setter prepa
     * @access public
     * @param int $prepa
     * @return void
     */
    public  function setPrepa($prepa)
    {
        if (!is_numeric($prepa)) { // id numérique
            $this->erreurs[] = self::Prepa_Invalide;
        } else {
            $this->prepa = $prepa;
        }
    }


    /**
     * Setter filiere
     * @access public
     * @param int $filiere
     * @return void
     */
    public  function setFiliere($filiere)
    {
        if (!is_numeric($filiere)) { // id numérique
            $this->erreurs[] = self::Filiere_Invalide;
        } else {
            $this->filiere = $filiere;
        }
    }


    /**
     * Setter erreurs null
     * @access public
     * @return void
     */
    public  function setErreurs()
    {
        $this->erreurs = array();
    }


    /**
     * Getter user
     * @access public
     * @return string
     */
    public  function user()
    {
        return $this->user;
    }


    /**
     * Getter sexe
     * @access public
     * @return string
     */
    public  function sexe()
    {
        return $this->sexe;
    }
	
	
	/**
     * Getter sexeAdm
     * @access public
     * @return string
     */
    public  function sexeAdm()
    {
        return $this->sexeAdm;
    }


    /**
     * Getter email
     * @access public
     * @return string
     */
    public  function email()
    {
        return $this->email;
    }


    /**
     * Getter promo
     * @access public
     * @return text
     */
    public  function promo()
    {
        return $this->promo;
    }


    /**
     * Getter section
     * @access public
     * @return text
     */
    public  function section()
    {
        return $this->section;
    }


    /**
     * Getter prepa
     * @access public
     * @return int
     */
    public  function prepa()
    {
        return $this->prepa;
    }


    /**
     * Getter filiere
     * @access public
     * @return int
     */
    public  function filiere()
    {
        return $this->filiere;
    }


    /**
     * Getter erreurs
     * @access public
     * @return array
     */
    public  function erreurs()
    {
        return $this->erreurs;
    }

}
