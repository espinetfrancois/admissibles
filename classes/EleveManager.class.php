<?php
/**
 * Classe de gestion BDD de la classe Eleve
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 0.5
 *
 * @todo : gestion des erreurs
 */

class EleveManager {

    /**
     * Connexion à la BDD
     * @var PDO
     * @access protected
     */
    protected  $db;


    /**
     * Méthode permettant d'ajouter un élève
     * @access protected
     * @param Eleve $eleve 
     * @return void
     */

    protected  function add(Eleve $eleve) {
        $requete = $this->db->prepare('INSERT INTO x 
                                       SET USER = :user,
                                           SEXE = :sexe,
                                           ID_SECTION = :section,
                                           ADRESSE_MAIL = :email,
                                           ID_FILIERE = :filiere,
                                           ID_PROMOTION = :promo,
                                           ID_ETABLISSEMENT = :prepa'); 
        $requete->bindValue(':user', $eleve->user());
        $requete->bindValue(':sexe', $eleve->sexe());
        $requete->bindValue(':section', $eleve->section());
        $requete->bindValue(':email', $eleve->email());
        $requete->bindValue(':filiere', $eleve->filiere());
        $requete->bindValue(':promo', $eleve->promo());
        $requete->bindValue(':prepa', $eleve->prepa());
        $requete->bindValue(':serie1', $eleve->serie1());
        $requete->bindValue(':serie2', $eleve->serie2());
        $requete->bindValue(':serie3', $eleve->serie3());
        $requete->bindValue(':serie4', $eleve->serie4());
        $requete->execute();
    }


    /**
     * Méthode permettant de modifier un élève
     * @access protected
     * @param Eleve $eleve 
     * @return void
     */

    protected  function update(Eleve $eleve) {
        $requete = $this->db->prepare('UPDATE x 
                                       SET SEXE = :sexe,
                                           ID_SECTION = :section,
                                           ADRESSE_MAIL = :email,
                                           ID_FILIERE = :filiere,
                                           ID_PROMOTION = :promo,
                                           ID_ETABLISSEMENT = :prepa,
                                           DISPO_S1 = :serie1,
                                           DISPO_S2 = :serie2,
                                           DISPO_S3 = :serie3,
                                           DISPO_S4 = :serie4
                                       WHERE USER = :user'); 
        $requete->bindValue(':user', $eleve->user());
        $requete->bindValue(':sexe', $eleve->sexe());
        $requete->bindValue(':section', $eleve->section());
        $requete->bindValue(':email', $eleve->email());
        $requete->bindValue(':filiere', $eleve->filiere());
        $requete->bindValue(':promo', $eleve->promo());
        $requete->bindValue(':prepa', $eleve->prepa());
        $requete->bindValue(':serie1', $eleve->serie1());
        $requete->bindValue(':serie2', $eleve->serie2());
        $requete->bindValue(':serie3', $eleve->serie3());
        $requete->bindValue(':serie4', $eleve->serie4());
        $requete->execute();
    }


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
     * Méthode permettant d'enregistrer l'élève s'il est valide
     * @access public
     * @param Eleve $eleve 
     * @return void
     */

    public  function save(Eleve $eleve) {
        if ($eleve->isValid()) {
            $eleve->isNew() ? $this->add($eleve) : $this->update($eleve);
        } else {
            throw new RuntimeException('Les champs doivent être valides pour être enregistrés'); // Ne se produit jamais en exécution courante
        }
    }


    /**
     * Méthode retournant un élève en particulier
     * @access public
     * @param string $user 
     * @return Eleve
     */

    public  function getUnique($user) {
        if (!preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#',$user)) { // de la forme prenom.nom
            throw new RuntimeException('Utilisateur invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('SELECT *
                                       FROM x
                                       WHERE USER = :user');
        $requete->bindValue(':user', $user);
        $requete->execute();
        if ($requete->rowCount() != 1) {
            throw new RuntimeException('Plusieurs utilisateurs possèdent le même nom'); // Ne se produit jamais en exécution courante
        }
            
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Eleve');
            
        return $requete->fetch();
    }


    /**
     * Méthode retournant la liste de tous les élèves
     * @access public
     * @return array
     */

    public  function getList() {
        $requete = $this->db->prepare('SELECT *
                                       FROM x');
        $requete->execute();
        
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Eleve');
            
        return $requete->fetch();
    }


    /**
     * Méthode  retournant l'élève disponible compatible avec la demande
     * @access public
     * @param Demande $demande 
     * @param $limit 
     * @return Eleve
     */

    public  function getFavorite(Demande $demande, $limit) {
        if (!$demande->isValid() || !is_numeric($limit) || !in_array($demande->serie(), array(1, 2, 3, 4))) {
            throw new RuntimeException('getFavorite : mauvais paramétrage'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('SELECT USER,
                                              SEXE,
                                              ID_SECTION,
                                              ADRESSE_MAIL,
                                              ID_FILIERE,
                                              ID_PROMOTION,
                                              ID_ETABLISSEMENT,
                                              (3*(SEXE=:sexe)+(ID_SECTION=:section)+6*(ID_ETABLISSEMENT=:prepa)+2*(ID_FILIERE=:filiere)) 
                                              AS MATCH
                                       FROM x
                                       WHERE `DISPO_S'.$demande->serie().'`=1
                                       ORDER BY MATCH DESC
                                       LIMIT :limit');
        $requete->bindValue(':sexe', $demande->sexe());
        $requete->bindValue(':section',  $demande->sport());
        $requete->bindValue(':prepa',  $demande->prepa());
        $requete->bindValue(':filiere',  $demande->filiere());
        $requete->bindValue(':limit',  $limit);
        $requete->execute();
        
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Eleve');
            
        return $requete->fetch();
    }

}
?>
