<?php
/**
 * Classe de gestion BDD de la classe Eleve
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

class EleveManager {

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

    public  function __construct(PDO $db)
    {
        $this->db = $db;

    }


    /**
     * Méthode permettant d'ajouter un élève
     * @access public
     * @param Eleve $eleve 
     * @return void
     */

    public  function add(Eleve $eleve)
    {
        try {
            $requete = $this->db->prepare('INSERT INTO x 
                                           SET USER = :user,
                                               SEXE = :sexe,
                                               SECTION = :section,
                                               ADRESSE_MAIL = :email,
                                               ID_FILIERE = :filiere,
                                               PROMOTION = :promo,
                                               ID_ETABLISSEMENT = :prepa'); 
            $requete->bindValue(':user', $eleve->user());
            $requete->bindValue(':sexe', $eleve->sexe());
            $requete->bindValue(':section', $eleve->section());
            $requete->bindValue(':email', $eleve->email());
            $requete->bindValue(':filiere', $eleve->filiere());
            $requete->bindValue(':promo', $eleve->promo());
            $requete->bindValue(':prepa', $eleve->prepa());
            $requete->execute();
        } catch (Exception $e) {
            Logs::logger(3, 'Erreur SQL EleveManager::add : '.$e->getMessage());
        }

    }


    /**
     * Méthode permettant de modifier un élève
     * @access public
     * @param Eleve $eleve 
     * @return void
     */

    public  function update(Eleve $eleve)
    {
        if ($eleve->isValid()) {
            try {
                $requete = $this->db->prepare('UPDATE x 
                                               SET SEXE = :sexe,
                                                   SECTION = :section,
                                                   ADRESSE_MAIL = :email,
                                                   ID_FILIERE = :filiere,
                                                   PROMOTION = :promo,
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
            } catch (Exception $e) {
                Logs::logger(3, 'Erreur SQL EleveManager::update : '.$e->getMessage());
            }
        } else {
            Logs::logger(3, 'Corruption des parametres : EleveManager::update');
        }

    }



    /**
     * Méthode permettant d'ajouter une disponibilité d'un élève pour une série
     * @access public
     * @param string $user
     * @param int $serie
     * @return void
     */

    public  function addDispo($user, $serie)
    {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user) || !is_numeric($serie)) {
            Logs::logger(3, 'Corruption des parametres : EleveManager::addDispo');
        }
        try {
            $requete = $this->db->prepare('INSERT INTO disponibilites
                                           SET ID_X = :user,
                                           ID_SERIE = :serie');
            $requete->bindValue(':user', $user);
            $requete->bindValue(':serie', $serie);
            $requete->execute();
        } catch (Exception $e) {
            Logs::logger(3, 'Erreur SQL EleveManager::addDispo : '.$e->getMessage());
        }

    }
    
    
    /**
     * Méthode supprimant la disponibilité d'un élève pour une série
     * @access public
     * @param string $user
     * @param int $serie
     * @return void
     */

    public  function deleteDispo($user, $serie)
    {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user) || !is_numeric($serie)) {
            Logs::logger(3, 'Corruption des parametres : EleveManager::deleteDispo');
        }
        try {
            $requete = $this->db->prepare('DELETE FROM disponibilites
                                           WHERE ID_X = :user
                                           AND ID_SERIE = :serie');
            $requete->bindValue(':user', $user);
            $requete->bindValue(':serie', $serie);
            $requete->execute();
        } catch (Exception $e) {
            Logs::logger(3, 'Erreur SQL EleveManager::deleteDispo : '.$e->getMessage());
        }

    }
    
    /**    
     * Méthode retournant un élève en particulier
     * @access public
     * @param string $user 
     * @return Eleve
     */

    public  function getUnique($user)
    {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user)) { // de la forme prenom.nom(.2011)
            Logs::logger(3, 'Corruption des parametres : EleveManager::getUnique');
        }
        try {
            $requete = $this->db->prepare('SELECT USER AS user,
                                                  SEXE AS sexe,
                                                  SECTION AS section,
                                                  ADRESSE_MAIL AS email,
                                                  ID_FILIERE AS filiere,
                                                  PROMOTION AS promo,
                                                  ID_ETABLISSEMENT AS prepa
                                           FROM x
                                           WHERE USER = :user');
            $requete->bindValue(':user', $user);
            $requete->execute();
        } catch (Exception $e) {
            Logs::logger(3, 'Erreur SQL EleveManager::getUnique : '.$e->getMessage());
        }
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

    public  function getDispo($user)
    {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user)) { // de la forme prenom.nom(.2011)
            Logs::logger(3, 'Corruption des parametres : EleveManager::getDispo');
        }
        try {
            $requete = $this->db->prepare('SELECT disponibilites.ID_SERIE AS serie
                                           FROM disponibilites
                                           INNER JOIN series
                                           ON series.ID = disponibilites.ID_SERIE
                                           WHERE ID_X = :user');
            $requete->bindValue(':user', $user);
            $requete->execute();
        } catch (Exception $e) {
            Logs::logger(3, 'Erreur SQL EleveManager::getDispo : '.$e->getMessage());
        }
        
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

    public  function getList()
    {
        try {
            $requete = $this->db->prepare('SELECT USER AS user,
                                                  SEXE AS sexe,
                                                  SECTION AS section,
                                                  ADRESSE_MAIL AS email,
                                                  ID_FILIERE AS filiere,
                                                  PROMOTION AS promo,
                                                  ID_ETABLISSEMENT AS prepa,
                                           FROM x');
            $requete->execute();
        } catch (Exception $e) {
            Logs::logger(3, 'Erreur SQL EleveManager::getList : '.$e->getMessage());
        }
        
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

    public  function getFavorite(Demande $demande, $limit)
    {
        if (!$demande->isValid() || !is_numeric($limit)) {
            Logs::logger(3, 'Corruption des parametres : EleveManager::getFavorite');
        }
        try {
            $requete = $this->db->prepare('SELECT x.USER AS user,
                                                  x.SEXE AS sexe,
                                                  x.SECTION AS section,
                                                  x.ADRESSE_MAIL AS email,
                                                  ref_filieres.NOM AS filiere,
                                                  x.PROMOTION AS promo,
                                                  CONCAT(ref_etablissements.COMMUNE," - ",ref_etablissements.NOM) AS prepa,
                                                  (3*(x.SEXE=:sexe)+(x.SECTION=:section)+6*(x.ID_ETABLISSEMENT=:prepa)+2*(x.ID_FILIERE=:filiere)) AS pertinent
                                           FROM x
                                           INNER JOIN disponibilites
                                           ON disponibilites.ID_X = x.USER
                                           INNER JOIN ref_etablissements
                                           ON ref_etablissements.ID = x.ID_ETABLISSEMENT
                                           INNER JOIN ref_filieres
                                           ON ref_filieres.ID = x.ID_FILIERE
                                           WHERE disponibilites.ID_SERIE = :serie
                                           ORDER BY pertinent DESC
                                           LIMIT '.$limit);
            $requete->bindValue(':sexe', $demande->sexe());
            $requete->bindValue(':section',  $demande->sport());
            $requete->bindValue(':prepa',  $demande->prepa());
            $requete->bindValue(':filiere',  $demande->filiere());
            $requete->bindValue(':serie',  $demande->serie());
            $requete->execute();
        } catch (Exception $e) {
            Logs::logger(3, 'Erreur SQL EleveManager::getFavorite : '.$e->getMessage());
        }
        
        $requete->setFetchMode(PDO::FETCH_CLASS, 'Eleve'); // Attention : les champs référencées contiennent les valeurs affichables
        
        return $requete->fetchAll();
    }

}
