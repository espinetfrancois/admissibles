<?php
/**
 * Classe de gestion BDD de la classe Eleve
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
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
     * @access public
     * @param Eleve $eleve 
     * @return void
     */

    public  function add(Eleve $eleve) {
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
        $requete->execute();
    }


    /**
     * Méthode permettant de modifier un élève
     * @access public
     * @param Eleve $eleve 
     * @return void
     */

    public  function update(Eleve $eleve) {
        if ($eleve->isValid()) {
            $requete = $this->db->prepare('UPDATE x 
                                           SET SEXE = :sexe,
                                               ID_SECTION = :section,
                                               ADRESSE_MAIL = :email,
                                               ID_FILIERE = :filiere,
                                               ID_PROMOTION = :promo,
                                               ID_ETABLISSEMENT = :prepa
                                           WHERE USER = :user'); 
            $requete->bindValue(':user', $eleve->user());
            $requete->bindValue(':sexe', $eleve->sexe());
            $requete->bindValue(':section', $eleve->section());
            $requete->bindValue(':email', $eleve->email());
            $requete->bindValue(':filiere', $eleve->filiere());
            $requete->bindValue(':promo', $eleve->promo());
            $requete->bindValue(':prepa', $eleve->prepa());
            $requete->execute();
        } else {
            throw new RuntimeException('Les champs doivent être valides pour être enregistrés'); // Ne se produit jamais en exécution courante
        }
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
     * Méthode permettant de mettre à jour les disponibilités d'un élève
     * @access public
     * @param int $id
     * @param array $dispo
     * @return void
     */

    public  function updateDispo($user, $dispo) {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user) || !is_array($dispo)) {
            throw new RuntimeException('update Dispo : parametres invalides'); // Ne se produit jamais en exécution courante
        }
        foreach ($dispo as $serie) {
            if (!is_numeric($serie)) {
                throw new RuntimeException('update Dispo : parametres invalides'); // Ne se produit jamais en exécution courante
            }
        }
        $requete = $this->db->prepare('SELECT ID
                                       FROM x
                                       WHERE USER = :user');
        $requete->bindValue(':user', $user);
        $requete->execute();
        $requete->closeCursor();
        if ($requete->rowCount() != 1) {
            throw new RuntimeException('Utilisateur invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('DELETE
                                       FROM disponibilites
                                       WHERE ID_X = :user');
        $requete->bindValue(':user', $user);
        $requete->execute();
        foreach ($dispo as $serie) {
            $requete = $this->db->prepare('INSERT INTO disponibilites
                                           SET ID_X = :user, ID_SERIE = :serie');
            $requete->bindValue(':user', $user);
            $requete->bindValue(':serie', $serie);
            $requete->execute();
        }
    }


    /**
     * Méthode retournant un élève en particulier
     * @access public
     * @param string $user 
     * @return Eleve
     */

    public  function getUnique($user) {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user)) { // de la forme prenom.nom(.2011)
            throw new RuntimeException('Utilisateur invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare("SELECT USER AS user,
                                              SEXE AS sexe,
                                              ID_SECTION AS section,
                                              ADRESSE_MAIL AS email,
                                              ID_FILIERE AS filiere,
                                              ID_PROMOTION AS promo,
                                              ID_ETABLISSEMENT AS prepa
                                       FROM x
                                       WHERE USER = :user");
        $requete->bindValue(':user', $user);
        $requete->execute();
        if ($requete->rowCount() > 1) {
            throw new RuntimeException('Plusieurs utilisateurs possèdent le même nom'); // Ne se produit jamais en exécution courante
        }
            
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Eleve');
            
        return $requete->fetch();
    }
    
    /**
     * Méthode retournant les disponibilités d'un élève en particulier
     * @access public
     * @param string $user 
     * @return array
     */

    public  function getDispo($user) {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user)) { // de la forme prenom.nom(.2011)
            throw new RuntimeException('Utilisateur invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('SELECT disponibilites.ID_SERIE AS serie
                                       FROM disponibilites
                                       INNER JOIN series
                                       ON series.ID = disponibilites.ID_SERIE
                                       WHERE ID_X = :user');
        $requete->bindValue(':user', $user);
        $requete->execute();
        
        $listeDispo = array();
        while ($res = $requete->fetch()) {
            $listeDispo[] = $res['serie'];
        }
        $requete->closeCursor();
        return $listeDispo;
    }


    /**
     * Méthode retournant la liste de tous les élèves
     * @access public
     * @return array
     */

    public  function getList() {
        $requete = $this->db->prepare('SELECT USER AS user,
                                              SEXE AS sexe,
                                              ID_SECTION AS section,
                                              ADRESSE_MAIL AS email,
                                              ID_FILIERE AS filiere,
                                              ID_PROMOTION AS promo,
                                              ID_ETABLISSEMENT AS prepa,
                                       FROM x');
        $requete->execute();
        
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Eleve');
            
        $listeX = $requete->fetchAll();
        $requete->closeCursor();
        return $listeX;
    }


    /**
     * Méthode  retournant l'élève disponible compatible avec la demande
     * @access public
     * @param Demande $demande 
     * @param $limit 
     * @return array(Eleve)
     */

    public  function getFavorite(Demande $demande, $limit) {
        if (!$demande->isValid() || !is_numeric($limit) || !in_array($demande->serie(), array(1, 2, 3, 4))) {
            throw new RuntimeException('getFavorite : mauvais paramétrage'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('SELECT x.USER AS user,
                                              x.SEXE AS sexe,
                                              x.ID_SECTION AS section,
                                              x.ADRESSE_MAIL AS email,
                                              x.ID_FILIERE AS filiere,
                                              x.ID_PROMOTION AS promo,
                                              x.ID_ETABLISSEMENT AS prepa,
                                              (3*(x.SEXE=:sexe)+(x.ID_SECTION=:section)+6*(x.ID_ETABLISSEMENT=:prepa)+2*(x.ID_FILIERE=:filiere)) AS match
                                       FROM x
                                       INNER JOIN disponibilites
                                       ON disponibilites.ID_X = x.ID
                                       WHERE disponibilites.ID_SERIE = :serie
                                       ORDER BY match DESC
                                       LIMIT :limit');
        $requete->bindValue(':sexe', $demande->sexe());
        $requete->bindValue(':section',  $demande->sport());
        $requete->bindValue(':prepa',  $demande->prepa());
        $requete->bindValue(':filiere',  $demande->filiere());
        $requete->bindValue(':serie',  $demande->serie());
        $requete->bindValue(':limit',  $limit);
        $requete->execute();
        
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Eleve', array('user','sexe','section','email','filiere','promo','prepa'));
        
        $listeEleves = $requete->fetchAll();
        $requete->closeCursor();
        return $listeEleves;
    }

}
?>
