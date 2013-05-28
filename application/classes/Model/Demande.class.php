<?php

/**
 * Classe représentant un admissible et sa demande de logement.
 *
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */
class Model_Demande extends Model {

    /**
     * Identifiant unique.
     *
     * @var integer
     * @access protected
     */
    protected $id;

    /**
     * Le nom.
     *
     * @var string
     * @access protected
     */
    protected $nom;

    /**
     * Le prénom.
     *
     * @var string
     * @access protected
     */
    protected $prenom;

    /**
     * Adresse email de contact de l'admissible.
     *
     * @var string
     * @access protected
     */
    protected $email;

    /**
     * M || F
     *
     * @var string
     * @access protected
     */
    protected $sexe;

    /**
     * Etablissement scolaire d'origine.
     *
     * @var string
     * @access protected
     */
    protected $prepa;

    /**
     * Filière d'origine.
     *
     * @var string
     * @access protected
     */
    protected $filiere;

    /**
     * Série d'admissibilité.
     *
     * @var integer
     * @access protected
     */
    protected $serie;

    /**
     * Sport préféré.
     *
     * @var text
     * @access protected
     */
    protected $sport;

    /**
     * Nom d'utilisateur de l'élève polytechnicien accueillant l'admissible.
     *
     * @var string
     * @access protected
     */
    protected $userEleve;

    /**
     * Statut de la demande de logement.
     *
     * @var integer
     * @access protected
     */
    protected $status;

    /**
     * Identifiant unique transmis lors des requêtes GET.
     *
     * @var string
     * @access protected
     */
    protected $code;

    /**
     * Erreurs de remplissage des attributs.
     *
     * @var array
     * @access protected
     */
    protected $erreurs;

    /**
     * Constantes relatives aux erreurs possibles rencontrées lors de l'exécution de la méthode
     */
    const Id_Invalide = 1;
    const Nom_Invalide = 2;
    const Prenom_Invalide = 3;
    const Email_Invalide = 4;
    const Sexe_Invalide = 5;
    const Prepa_Invalide = 6;
    const Filiere_Invalide = 7;
    const Serie_Invalide = 8;
    const Sport_Invalide = 9;
    const User_Invalide = 10;
    const Status_Invalide = 11;
    const Code_Invalide = 12;
    const Non_Admissible = 13;

    /**
     * Méthode permettant de savoir si les attributs sont valides.
     *
     * @access public
     * @return bool
     */
    public final function isValid()
    {
        return !(empty($this->nom) || empty($this->prenom) || empty($this->email)
                || empty($this->sexe) || empty($this->prepa) || empty($this->filiere)
                || empty($this->sport));
    }

    /**
     * Setter id.
     *
     * @access public
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Setter nom.
     *
     * @access public
     * @param string $nom
     * @return void
     */
    public function setNom($nom)
    {
        if (preg_match('#[a-zA-Zéèàêâùïüë_-]+#', $nom) === false) { // lettres seulement
            $this->erreurs[] = self::Nom_Invalide;
        } else {
            $this->nom = $nom;
        }
    }

    /**
     * Setter prenom.
     *
     * @access public
     * @param string $prenom
     * @return void
     */
    public function setPrenom($prenom)
    {
        if (preg_match('#[a-zA-Zéèàêâùïüë_-]+#', $prenom) === false) { // lettres seulement
            $this->erreurs[] = self::Prenom_Invalide;
        } else {
            $this->prenom = $prenom;
        }
    }

    /**
     * Setter email.
     *
     * @access public
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        if (preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#', $email) === false) { // adresse email
            $this->erreurs[] = self::Email_Invalide;
        } else {
            $this->email = $email;
        }
    }

    /**
     * Setter sexe.
     *
     * @access public
     * @param string $sexe
     * @return void
     */
    public function setSexe($sexe)
    {
        if ($sexe != 'M' && $sexe != 'F') { // de la forme M ou F
            $this->erreurs[] = self::Sexe_Invalide;
        } else {
            $this->sexe = $sexe;
        }
    }

    /**
     * Setter prepa.
     *
     * @access public
     * @param integer $prepa
     * @return void
     */
    public function setPrepa($prepa)
    {
        if (is_numeric($prepa) === false) { // id numérique
            $this->erreurs[] = self::Prepa_Invalide;
        } else {
            $this->prepa = $prepa;
        }
    }

    /**
     * Setter filiere.
     *
     * @access public
     * @param integer $filiere
     * @return void
     */
    public function setFiliere($filiere)
    {
        if (is_numeric($filiere) === false) { // id numérique
            $this->erreurs[] = self::Filiere_Invalide;
        } else {
            $this->filiere = $filiere;
        }
    }

    /**
     * Setter serie.
     *
     * @access public
     * @param integer $serie
     * @return void
     */
    public function setSerie($serie)
    {
        if (is_numeric($serie) === false) {
            $this->erreurs[] = self::Serie_Invalide;
        } else {
            $this->serie = $serie;
        }
    }

    /**
     * Setter sport.
     *
     * @access public
     * @param text $sport
     * @return void
     */
    public function setSport($sport)
    {
        $this->sport = $sport;
    }

    /**
     * Setter userEleve.
     *
     * @access public
     * @param string $user
     * @return void
     */
    public function setUserEleve($user)
    {
        if (preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#', $user) === false) { // de la forme prenom.nom
            $this->erreurs[] = self::User_Invalide;
        } else {
            $this->userEleve = $user;
        }
    }

    /**
     * Setter status.
     *
     * @access public
     * @param integer $status
     * @return void
     */
    public function setStatus($status)
    {
        if (is_numeric($status) === false) { // id numérique
            $this->erreurs[] = self::Status_Invalide;
        } else {
            $this->status = $status;
        }
    }

    /**
     * Setter code.
     *
     * @access public
     * @param string $code
     * @return void
     */
    public function setCode($code)
    {
        if (strlen($code) != 32) { // id numérique
            $this->erreurs[] = self::Code_Invalide;
        } else {
            $this->code = $code;
        }

    }

    /**
     * Getter id.
     *
     * @access public
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Getter nom.
     *
     * @access public
     * @return string
     */
    public function nom()
    {
        return $this->nom;
    }

    /**
     * Getter prenom.
     *
     * @access public
     * @return string
     */
    public function prenom()
    {
        return $this->prenom;
    }

    /**
     * Getter email.
     *
     * @access public
     * @return string
     */
    public function email()
    {
        return $this->email;
    }

    /**
     * Getter sexe.
     *
     * @access public
     * @return string
     */
    public function sexe()
    {
        return $this->sexe;
    }

    /**
     * Getter prepa.
     *
     * @access public
     * @return int
     */
    public function prepa()
    {
        return $this->prepa;
    }

    /**
     * Getter filiere.
     *
     * @access public
     * @return int
     */
    public function filiere()
    {
        return $this->filiere;
    }

    /**
     * Getter serie.
     *
     * @access public
     * @return int
     */
    public function serie()
    {
        return $this->serie;
    }

    /**
     * Getter sport.
     *
     * @access public
     * @return text
     */
    public function sport()
    {
        return $this->sport;
    }

    /**
     * Getter userEleve.
     *
     * @access public
     * @return string
     */
    public function userEleve()
    {
        return $this->userEleve;
    }

    /**
     * Getter status.
     *
     * @access public
     * @return int
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Getter code.
     *
     * @access public
     * @return string
     */
    public function code()
    {
        return $this->code;
    }

    /**
     * Getter erreurs.
     *
     * @access public
     * @return array
     */
    public function erreurs()
    {
        return $this->erreurs;
    }

}
