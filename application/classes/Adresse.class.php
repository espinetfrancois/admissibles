<?php
/**
 * Classe représentant un logement recommandé pour les oraux
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

class Adresse {

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
    protected  $adresse;

    /**
     * 
     * @var string
     * @access protected
     */
    protected  $tel;

    /**
     * 
     * @var string
     * @access protected
     */
    protected  $email;

    /**
     * 
     * @var string
     * @access protected
     */
    protected  $description;

    /**
     * Catégorie de l'hébergement : résidence universiataire, hôtel...
     * @var string
     * @access protected
     */
    protected  $categorie;

    /**
     * Validation de l'hébergement par l'administrateur
     * @var int
     * @access protected
     */
    protected  $valide;

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
    const ADRESSE_INVALIDE = 3;
    const TEL_INVALIDE = 4;
    const EMAIL_INVALIDE = 5;
    const DESCRIPTION_INVALIDE = 6;
    const CATEGORIE_INVALIDE = 7;
    const VALIDE_INVALIDE = 8;

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
     * Méthode permettant de savoir si l'adresse est nouvelle
     * @access public
     * @return bool
     */

    public  function isNew()
    {
        return empty($this->id);
    }


    /**
     * Méthode permettant de savoir si les attributs sont valides
     * @access public
     * @return bool
     */

    public final  function isValid()
    {
        return !(empty($this->nom) || empty($this->adresse) || empty($this->description) || empty($this->categorie));
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
        if (empty($nom) || strlen($nom) >= 200) {
            $this->erreurs[] = self::NOM_INVALIDE;
        } else {
            $this->nom = $nom;
        }
    }


    /**
     * @access public
     * @param string $adresse 
     * @return void
     */

    public  function setAdresse($adresse)
    {
        if (empty($adresse) || strlen($adresse) >= 250) {
            $this->erreurs[] = self::ADRESSE_INVALIDE;
        } else {
            $this->adresse = $adresse;
        }
    }


    /**
     * @access public
     * @param string $tel 
     * @return void
     */

    public  function setTel($tel)
    {
        if (!preg_match('#^0[1-8]([-. ]?[0-9]{2}){4}$#',$tel) && !empty($tel)) { // n° de téléphone avec ou sans séparateur si non vide
            $this->erreurs[] = self::TEL_INVALIDE;
        } else {
            $this->tel = $tel;
        }
    }


    /**
     * @access public
     * @param string $email 
     * @return void
     */

    public  function setEmail($email)
    {
        if (!preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#',$email) && !empty($email)) { // adresse email si non vide 
            $this->erreurs[] = self::EMAIL_INVALIDE;
        } else {
            $this->email = $email;
        }
    }


    /**
     * @access public
     * @param string $description 
     * @return void
     */

    public  function setDescription($description)
    {
        if (empty($description) || strlen($description) >= 250) {
            $this->erreurs[] = self::DESCRIPTION_INVALIDE;
        } else {
            $this->description = $description;
        }
    }


    /**
     * @access public
     * @param string $categorie 
     * @return void
     */

    public  function setCategorie($categorie)
    {
        if (!is_numeric($categorie)) { // id numérique
            $this->erreurs[] = self::CATEGORIE_INVALIDE;
        } else {
            $this->categorie = $categorie;
        }
    }


    /**
     * @access public
     * @param int $valide 
     * @return void
     */

    public  function setValide($valide)
    {
        if ($valide != 0 && $valide != 1) { // O ou 1
            $this->erreurs[] = self::VALIDE_INVALIDE;
        } else {
            $this->valide = $valide;
        }
    }


    /**
     * @access public
     * @return void
     */

    public  function setErreurs()
    {
        $this->erreurs = array();
    }

    /**
     * @access public
     * @return int
     */

    public  function id()
    {
        return $this->id;
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

    public  function adresse()
    {
        return $this->adresse;
    }


    /**
     * @access public
     * @return string
     */

    public  function tel()
    {
        return $this->tel;
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

    public  function description()
    {
        return $this->description;
    }


    /**
     * @access public
     * @return int
     */

    public  function valide()
    {
        return $this->valide;
    }


    /**
     * @access public
     * @return string
     */

    public  function categorie()
    {
        return $this->categorie;
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
