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
                                           ID_ETABLISSEMENT = :prepa,s
                                       WHERE USER = :user'); 
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
     * Méthode permettant de mettre à jour les disponibilités d'un élève
     * @access public
     * @param int $id
     * @param array $dispo
     * @return void
     */

    public  function updateDispo($id, $dispo) {
        if (!is_numeric($id) || !is_array($dispo)) {
            throw new RuntimeException('update Dispo : parametres invalides'); // Ne se produit jamais en exécution courante
        }
        foreach ($dispo as $serie) {
            if (!is_numeric($serie)) {
                throw new RuntimeException('update Dispo : parametres invalides'); // Ne se produit jamais en exécution courante
            }
        }
        $requete = $this->db->prepare('SELECT ID
                                       FROM x
                                       WHERE ID = :id');
        $requete->bindValue(':id', $id);
        $requete->execute();
        $requete->closeCursor();
        if ($requete->rowCount() != 1) {
            throw new RuntimeException('Utilisateur invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('DELETE
                                       FROM disponibilites
                                       WHERE ID_X = :id');
        $requete->bindValue(':id', $id);
        $requete->execute();
        foreach ($dispo as $serie) {
            $requete = $this->db->prepare('INSERT INTO disponibilites
                                           SET ID_X = :id, ID_SERIE = :serie');
            $requete->bindValue(':id', $id);
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
        if (!preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#',$user)) { // de la forme prenom.nom
            throw new RuntimeException('Utilisateur invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('SELECT ID AS id,
                                              USER AS user,
                                              SEXE AS sexe,
                                              ID_SECTION AS section,
                                              ADRESSE_MAIL AS email,
                                              ID_FILIERE AS filiere,
                                              ID_PROMOTION AS promo,
                                              ID_ETABLISSEMENT AS prepa,
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
     * Méthode retournant les disponibilités d'un élève en particulier
     * @access public
     * @param int $id 
     * @return array
     */

    public  function getDispo($id) {
        if (!is_numeric($id)) { // id numérique
            throw new RuntimeException('Identifiant invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('SELECT serie.INTITULE AS intitule
                                              disponibilites.ID_SEIE AS serie
                                       FROM disponibilites
                                       INNER JOIN series
                                       ON series.ID = disponibilites.ID_SERIE
                                       WHERE ID_X = :id');
        $requete->bindValue(':id', $id);
        $requete->execute();
        
        $listeDispo = $requete->fetchAll();
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
