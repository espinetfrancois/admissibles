<?php
/**
 * Classe de gestion BDD de la classe Eleve
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */
class Manager_Eleve extends Manager {

    /**
     * Méthode permettant d'ajouter un élève
     * @access public
     * @param Model_Eleve $eleve
     * @return void
     */
    public  function add(Model_Eleve $eleve)
    {
        try {
            $requete = $this->db->prepare('INSERT INTO x
                                           SET USER = :user,
                                               SEXE = :sexe,
											   SEXE_ADM = :sexeAdm,
                                               SECTION = :section,
                                               ADRESSE_MAIL = :email,
                                               ID_FILIERE = :filiere,
                                               PROMOTION = :promo,
                                               ID_ETABLISSEMENT = :prepa');
            $requete->bindValue(':user', $eleve->user());
            $requete->bindValue(':sexe', $eleve->sexe());
			$requete->bindValue(':sexeAdm', $eleve->sexeAdm());
            $requete->bindValue(':section', $eleve->section());
            $requete->bindValue(':email', $eleve->email());
            $requete->bindValue(':filiere', $eleve->filiere());
            $requete->bindValue(':promo', $eleve->promo());
            $requete->bindValue(':prepa', $eleve->prepa());
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Erreur lors de la requête : Manager_Eleve::add", Exception_Bdd_Query::Level_Critical, $e);
        }
    }


    /**
     * Méthode permettant de modifier un élève
     * @access public
     * @param Model_Eleve $eleve
     * @return void
     */
    public  function update(Model_Eleve $eleve)
    {
        if ($eleve->isValid()) {
            try {
                $requete = $this->db->prepare('UPDATE x
                                               SET SEXE = :sexe,
											   	   SEXE_ADM = :sexeAdm
                                                   SECTION = :section,
                                                   ADRESSE_MAIL = :email,
                                                   ID_FILIERE = :filiere,
                                                   PROMOTION = :promo,
                                                   ID_ETABLISSEMENT = :prepa
                                               WHERE USER = :user');
                $requete->bindValue(':user', $eleve->user());
                $requete->bindValue(':sexe', $eleve->sexe());
				$requete->bindValue(':sexeAdm', $eleve->sexeAdm());
                $requete->bindValue(':section', $eleve->section());
                $requete->bindValue(':email', $eleve->email());
                $requete->bindValue(':filiere', $eleve->filiere());
                $requete->bindValue(':promo', $eleve->promo());
                $requete->bindValue(':prepa', $eleve->prepa());
                $requete->execute();
            } catch (Exception $e) {
                throw new Exception_Bdd_Query("Erreur lors de la requête : Manager_Eleve::update", Exception_Bdd_Query::Level_Major, $e);
            }
        } else {
            throw new Exception_Bdd_Query('Corruption des paramètres : Manager_Eleve::update', Exception_Bdd_Query::Currupt_Params);
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
            throw new Exception_Bdd_Query('Corruption des parametres : Manager_Eleve::addDispo', Exception_Bdd_Query::Currupt_Params);
        }
        try {
            $requete = $this->db->prepare('INSERT INTO disponibilites
                                           SET ID_X = :user,
                                           ID_SERIE = :serie');
            $requete->bindValue(':user', $user);
            $requete->bindValue(':serie', $serie);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Erreur lors de la requête : Manager_Eleve::addDispo", Exception_Bdd_Query::Level_Major, $e);
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
            throw new Exception_Bdd_Query('Corruption des parametres : Manager_Eleve::deleteDispo', Exception_Bdd_Query::Currupt_Params);
        }
        try {
            $requete = $this->db->prepare('DELETE FROM disponibilites
                                           WHERE ID_X = :user
                                           AND ID_SERIE = :serie');
            $requete->bindValue(':user', $user);
            $requete->bindValue(':serie', $serie);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Erreur lors de la requête : Manager_Eleve::addDispo", Exception_Bdd_Query::Level_Major, $e);
        }
    }

    /**
     * Méthode retournant un élève en particulier
     * @access public
     * @param string $user
     * @return Model_Eleve
     */
    public  function getUnique($user)
    {
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$user)) { // de la forme prenom.nom(.2011)
            throw new Exception_Bdd_Query('Corruption des parametres : Manager_Eleve::getUnique', Exception_Bdd_Query::Currupt_Params);
        }
        try {
            $requete = $this->db->prepare('SELECT USER AS user,
                                                  SEXE AS sexe,
												  SEXE_ADM AS sexeAdm,
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
            throw new Exception_Bdd_Query("Erreur lors de la requête : Manager_Eleve::getUnique", Exception_Bdd_Query::Level_Major, $e);
        }
        if ($requete->rowCount() > 1) {
            // Ne se produit jamais en exécution courante
            throw new Exception_Bdd_Integrity('Plusieurs utilisateurs possèdent le même nom', Exception_Bdd_Integrity::Duplicate_Entry);
        }

        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_Eleve');

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
            throw new Exception_Bdd_Query('Corruption des parametres : Manager_Eleve::getDispo', Exception_Bdd_Query::Currupt_Params);
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
            throw new Exception_Bdd_Query("Erreur lors de la requête : Manager_Eleve::getDispo", Exception_Bdd_Query::Level_Major, $e);
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
												  SEXE_ADM AS sexeAdm,
                                                  SECTION AS section,
                                                  ADRESSE_MAIL AS email,
                                                  ID_FILIERE AS filiere,
                                                  PROMOTION AS promo,
                                                  ID_ETABLISSEMENT AS prepa,
                                           FROM x');
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Erreur lors de la requête : Manager_Eleve::getList", Exception_Bdd_Query::Level_Minor, $e);
        }

        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_Eleve');

        $listeX = $requete->fetchAll();
        $requete->closeCursor();

        return $listeX;
    }


    /**
     * Méthode  retournant l'élève disponible compatible avec la demande
     * @access public
     * @param Model_Demande $demande
     * @param $limit
     * @return array(Model_Eleve)
     */
    public  function getFavorite(Model_Demande $demande, $limit)
    {
        if (!$demande->isValid() || !is_numeric($limit)) {
            throw new Exception_Bdd_Query('Corruption des parametres : Manager_Eleve::getFavorite', Exception_Bdd_Query::Currupt_Params);
        }
        try {
            $requete = $this->db->prepare('SELECT x.USER AS user,
                                                  x.SEXE AS sexe,
												  x.SEXE_ADM AS sexeAdm,
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
										   AND (x.SEXE_ADM = 1 OR x.SEXE = :sexe)
                                           ORDER BY pertinent DESC
                                           LIMIT '.$limit);
            $requete->bindValue(':sexe', $demande->sexe());
            $requete->bindValue(':section',  $demande->sport());
            $requete->bindValue(':prepa',  $demande->prepa());
            $requete->bindValue(':filiere',  $demande->filiere());
            $requete->bindValue(':serie',  $demande->serie());
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Erreur lors de la requête : Manager_Eleve::getFavorite", Exception_Bdd_Query::Level_Blocker, $e);
        }

        $requete->setFetchMode(PDO::FETCH_CLASS, 'Model_Eleve'); // Attention : les champs référencées contiennent les valeurs affichables

        return $requete->fetchAll();
    }

}
