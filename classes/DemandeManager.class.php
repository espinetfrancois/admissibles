<?php
/**
 * Classe de gestion BDD de la classe Demande
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 0.5
 *
 *@todo : gestion des erreurs
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
            $requete = $this->db->prepare('INSERT INTO admissibles 
                                           SET NOM = :nom,
                                               PRENOM = :prenom,
                                               SEXE = :sexe,
                                               ADRESSE_MAIL = :email,
                                               SERIE = :serie,
                                               ID_FILIERE = :filiere,
                                               ID_ETABLISSEMENT = :prepa');
            $requete->bindValue(':nom', $eleve->user());
            $requete->bindValue(':prenom', $eleve->section());
            $requete->bindValue(':sexe', $eleve->sexe());
            $requete->bindValue(':email', $eleve->email());
            $requete->bindValue(':serie', $eleve->promo());
            $requete->bindValue(':filiere', $eleve->filiere());
            $requete->bindValue(':prepa', $eleve->prepa());
            $requete->execute();
            
            $requete = $this->db->prepare('INSERT INTO admissibles 
                                           SET NOM = :nom,
                                               PRENOM = :prenom,
                                               SEXE = :sexe,
                                               ADRESSE_MAIL = :email,
                                               SERIE = :serie,
                                               ID_FILIERE = :filiere,
                                               ID_ETABLISSEMENT = :prepa;
                                           SELECT LAST_INSERT_ID()
                                           AS id;
                                           INSERT INTO demandes 
                                           SET ID_ADMISSIBLE = id,
                                               USER_X = :user,
                                               LIEN = :code,
                                               ID_STATUS = :status');
            $requete->bindValue(':nom', $demande->nom());
            $requete->bindValue(':prenom', $demande->prenom());
            $requete->bindValue(':sexe', $demande->sexe());
            $requete->bindValue(':email', $demande->email());
            $requete->bindValue(':serie', $demande->serie());
            $requete->bindValue(':filiere', $demande->filiere());
            $requete->bindValue(':prepa', $demande->prepa());
            $requete->bindValue(':user', $demande->userEleve());
            $requete->bindValue(':code', $demande->code());
            $requete->bindValue(':status', $demande->status());
            $requete->execute();
        }
    }


    /**
     * Méthode renvoyant false si l'admissible a déjà une demande en cours
     * @access public
     * @param string $email 
     * @return bool
     */

    public  function autorisation($email) {
        $requete = $this->db->prepare('SELECT demandes.ID
                                       FROM demandes
                                       INNER JOIN admissibles
                                       ON demandes.ID_ADMISSIBLE = admissibles.ID
                                       WHERE admissibles.ADRESSE_MAIL = :email
                                       AND demandes.ID_STATUS <= 2');
        $requete->bindValue(':email', $email);
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
        $newCode = md5(sha1(time().$code);
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
        $requete = $this->db->prepare('SELECT admissibles.ID AS id
                                              admissibles.NOM AS nom
                                              admissibles.PRENOM AS prenom
                                              admissibles.ADRESSE_MAIL AS email
                                              admissibles.SEXE AS sexe
                                              admissibles.ID_ETABLISSEMENT AS prepa
                                              admissibles.ID_FILIAIRE AS filiere
                                              admissibles.SERIE AS serie
                                              demandes.USER_X AS userEleve
                                              demandes.ID_STATUS AS status
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
        $requete = $this->db->prepare('SELECT admissibles.ID AS id
                                              admissibles.NOM AS nom
                                              admissibles.PRENOM AS prenom
                                              admissibles.ADRESSE_MAIL AS email
                                              admissibles.SEXE AS sexe
                                              admissibles.ID_ETABLISSEMENT AS prepa
                                              admissibles.ID_FILIAIRE AS filiere
                                              admissibles.SERIE AS serie
                                              demandes.USER_X AS userEleve
                                              demandes.ID_STATUS AS status
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

}
?>
