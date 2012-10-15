<?php
/**
 * Classe de gestion BDD de la classe Demande
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo logs
 */

class DemandeManager {

    /**
     * Connexion à la BDD
     * @var PDO
     * @access protected
     */
    protected  $db;


    /**
     * Constructeur étant chargé d'enregistrer l'instance de PDO dans l'attribut $db
     * @access public
     * @param PDO $db 
     * @return void
     */

    public  function __construct(PDO $db) {
        $this->db = $db;
    }


    /**
     * Méthode permettant d'ajouter une demande
     * @access public
     * @param Demande $demande 
     * @return void
     */

    public  function add(Demande $demande) {
        if (!$demande->isValid()) {
            throw new RuntimeException('Les champs doivent être valides pour être enregistrés'); // Ne se produit jamais en exécution courante
        } else {
            $requete = $this->db->prepare('UPDATE admissibles 
                                           SET SEXE = :sexe,
                                               ADRESSE_MAIL = :email,
                                               ID_FILIERE = :filiere,
                                               ID_ETABLISSEMENT = :prepa
                                           WHERE ID = :id;
                                           INSERT INTO demandes 
                                           SET ID_ADMISSIBLE = :id,
                                               USER_X = :user,
                                               LIEN = :code,
                                               ID_STATUS = :status');
            $requete->bindValue(':id', $demande->id());
            $requete->bindValue(':sexe', $demande->sexe());
            $requete->bindValue(':email', $demande->email());
            $requete->bindValue(':filiere', $demande->filiere());
            $requete->bindValue(':prepa', $demande->prepa());
            $requete->bindValue(':user', $demande->userEleve());
            $requete->bindValue(':code', $demande->code());
            $requete->bindValue(':status', $demande->status());
            $requete->execute();
        }
    }


    /**
     * Méthode retournant l'id de l'admissible si présent dans les listes d'admissibilités et -1 sinon
     * @access public
     * @param string $nom
     * @param string $prenom
     * @param int $serie
     * @return int
     */

    public  function isAdmissible($nom, $prenom, $serie) {
        $nom = strtolower(strtr($nom,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
                                     'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));
        $prenom = strtolower(strtr($prenom,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
                                     'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));
        $requete = $this->db->prepare('SELECT ID
                                       FROM admissibles
                                       WHERE NOM = :nom
                                       AND PRENOM = :prenom
                                       AND SERIE = :serie');
        $requete->bindValue(':nom', $nom);
        $requete->bindValue(':prenom', $prenom);
        $requete->bindValue(':serie', $serie);
        $requete->execute();
        if ($requete->rowCount() == 0) {
            return -1;
        } elseif ($requete->rowCount() == 1) {
            $result = $requete->fetch(PDO::FETCH_ASSOC);
            $requete->closeCursor();
            return $result['ID'];
        } else {
            throw new RuntimeException('Plusieurs admissibles ont le meme nom'); // Ne se produit jamais en exécution courante
        }
    }


    /**
     * Méthode renvoyant false si l'admissible a déjà une demande en cours
     * @access public
     * @param Demande $demande 
     * @return bool
     */

    public  function autorisation($demande) {
        $requete = $this->db->prepare('SELECT demandes.ID
                                       FROM demandes
                                       INNER JOIN admissibles
                                       ON demandes.ID_ADMISSIBLE = admissibles.ID
                                       WHERE admissibles.NOM = :nom
                                       AND admissibles.PRENOM = :prenom
                                       AND admissibles.ADRESSE_MAIL = :email
                                       AND admissibles.SERIE = :serie
                                       AND admissibles.FILIERE = :filiere
                                       AND demandes.ID_STATUS <= 2');
        $requete->bindValue(':nom', $demande->nom());
        $requete->bindValue(':prenom', $demande->prenom());
        $requete->bindValue(':email', $demande->email());
        $requete->bindValue(':serie', $demande->serie());
        $requete->bindValue(':filiere', $demande->filiere());
        $requete->execute();
        return ($requete->rowCount() == 0);
    }
    

    /**
     * Méthode permettant de mettre à jour le status d'une demande
     * @access public
     * @param string $code
     * @return string
     */

    public  function updateStatus($code, $status) {
        if (!is_integer($status) || !preg_match('#^[a-z0-9A-Z](32)#',$code)) {
            throw new RuntimeException('updateStatus : mauvais paramétrage'); // Ne se produit jamais en exécution courante
        }

        $newCode = md5(sha1(time().$code));
        $requete = $this->db->prepare('UPDATE demandes
                                       SET ID_STATUS = :status,
                                           LIEN = :newCode,
                                       WHERE LIEN = :code'); 
        $requete->bindValue(':status', $status);
        $requete->bindValue(':newCode', $newCode);
        $requete->bindValue(':code', $code);

        $requete->execute();
        return $newCode;
    }


    /**
     * Méthode retournant un élève en particulier
     * @access public
     * @param string $code 
     * @return Demande
     */

    public  function getUnique($code) {
        if (!preg_match('#^[a-z0-9A-Z](32)#',$code)) {
            throw new RuntimeException('Code invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('SELECT admissibles.ID AS id,
                                              admissibles.NOM AS nom,
                                              admissibles.PRENOM AS prenom,
                                              admissibles.ADRESSE_MAIL AS email,
                                              admissibles.SEXE AS sexe,
                                              admissibles.ID_ETABLISSEMENT AS prepa,
                                              admissibles.ID_FILIERE AS filiere,
                                              admissibles.SERIE AS serie,
                                              demandes.USER_X AS userEleve,
                                              demandes.ID_STATUS AS status,
                                              demandes.LIEN code
                                       FROM demandes
                                       INNER JOIN admissibles
                                       ON demandes.ID_ADMISSIBLE = admissibles.ID
                                       WHERE LIEN = :code');
        $requete->bindValue(':code', $code);
        $requete->execute();
        if ($requete->rowCount() != 1) {
            throw new RuntimeException('Plusieurs demandes possèdent le même code'); // Ne se produit jamais en exécution courante
        }
            
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Demande');
            
        return $requete->fetch();
    }


    /**
     * Méthode retournant la liste de toutes les demandes
     * @access public
     * @return array
     */

    public  function getList() {
        $requete = $this->db->prepare('SELECT admissibles.ID AS id,
                                              admissibles.NOM AS nom,
                                              admissibles.PRENOM AS prenom,
                                              admissibles.ADRESSE_MAIL AS email,
                                              admissibles.SEXE AS sexe,
                                              admissibles.ID_ETABLISSEMENT AS prepa,
                                              admissibles.ID_FILIERE AS filiere,
                                              admissibles.SERIE AS serie,
                                              demandes.USER_X AS userEleve,
                                              demandes.ID_STATUS AS status,
                                              demandes.LIEN code
                                       FROM demandes
                                       INNER JOIN admissibles
                                       ON demandes.ID_ADMISSIBLE = admissibles.ID');
        $requete->execute();

        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Demande');
            
        $listeDemandes = $requete->fetchAll();
        $requete->closeCursor();
        return $listeDemandes;
    }
    

    /**
     * Méthode retournant la liste des demandes d'un X en particulier
     * @access public
     * @param string $user
     * @return array
     */

    public  function getDemandes($user) {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#', $user)) { // de la forme prenom.nom(.2011)
            throw new RuntimeException('Utilisateur invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('SELECT admissibles.ID AS id,
                                              admissibles.NOM AS nom,
                                              admissibles.PRENOM AS prenom,
                                              admissibles.ADRESSE_MAIL AS email,
                                              admissibles.SEXE AS sexe,
                                              CONCAT(ref_etablissements.COMMUNE," - ",ref_etablissements.NOM) AS prepa,
                                              ref_filieres.NOM AS filiere,
                                              series.INTITULE AS serie,
                                              demandes.ID_STATUS AS status,
                                              demandes.LIEN code
                                       FROM demandes
                                       INNER JOIN admissibles
                                       ON demandes.ID_ADMISSIBLE = admissibles.ID
                                       INNER JOIN ref_etablissements
                                       ON admissibles.ID_ETABLISSEMENT = ref_etablissements.ID
                                       INNER JOIN ref_filieres
                                       ON admissibles.ID_FILIERE = ref_filieres.ID
                                       INNER JOIN series
                                       ON admissibles.SERIE = series.ID
                                       WHERE demandes.USER_X = :user');
        $requete->bindValue(':user', $user);
        $requete->execute();

        $requete->setFetchMode(PDO::FETCH_CLASS, 'Demande'); // Attention, les champs référencés contiennent les noms et non les valeurs
            
        return $requete->fetchAll();
    }

}