<?php

/**
 * Classe de gestion BDD de la classe Demande.
 *
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 * @package Manager
 */
class Manager_Demande extends Manager
{

    /**
     * Méthode permettant d'ajouter une demande.
     *
     * @access public
     * @param Model_Demande $demande
     * @throws Exception_Bdd_Query
     * @return void
     */
    public function add(Model_Demande $demande)
    {
        if (!$demande->isValid()) {
            throw new Exception_Bdd_Query("La demande est invalide, elle ne peut-être enregistrée.", Exception_Bdd_Query::Currupt_Params);
        } else {
            try {
                $requete = $this->_db->prepare(
                                  'UPDATE admissibles
                                   SET SEXE = :sexe,
                                       ADRESSE_MAIL = :email,
                                       ID_FILIERE = :filiere,
                                       ID_ETABLISSEMENT = :prepa
                                   WHERE ID = :id'
                            );
                $requete->bindValue(':id', $demande->id());
                $requete->bindValue(':sexe', $demande->sexe());
                $requete->bindValue(':email', $demande->email());
                $requete->bindValue(':filiere', $demande->filiere());
                $requete->bindValue(':prepa', $demande->prepa());
                $requete->execute();
                $requete = $this->_db->prepare(
                                'INSERT INTO demandes
                                   SET ID_ADMISSIBLE = :id,
                                       USER_X = :user,
                                       LIEN = :code,
                                       ID_STATUS = :status');
                $requete->bindValue(':id', $demande->id());
                $requete->bindValue(':user', $demande->userEleve());
                $requete->bindValue(':code', $demande->code());
                $requete->bindValue(':status', $demande->status());
                $requete->execute();
            } catch (Exception $e) {
                throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Demande::add', Exception_Bdd_Query::Level_Blocker, $e);
            }
        }
    }

    /**
     * Méthode retournant l'id de l'admissible si présent dans les listes d'admissibilités et -1 sinon.
     *
     * @access public
     * @param string $nom
     * @param string $prenom
     * @param integer $serie
     * @throws Exception_Bdd_Query
     * @return integer
     */
    public function isAdmissible($nom, $prenom, $serie)
    {
        $nom = self::traitementNomPropres($nom);
        $prenom = self::traitementNomPropres($prenom);
        try {
            $requete = $this->_db->prepare(
                            'SELECT ID
                           FROM admissibles
                           WHERE NOM = :nom
                           AND PRENOM = :prenom
                           AND SERIE = :serie'
                    );
            $requete->bindValue(':nom', $nom);
            $requete->bindValue(':prenom', $prenom);
            $requete->bindValue(':serie', $serie);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Demande::isAdmissible', Exception_Bdd_Query::Level_Critical, $e);
        }
        if ($requete->rowCount() == 0) {
            return -1;
        } else if ($requete->rowCount() == 1) {
            $result = $requete->fetch(PDO::FETCH_ASSOC);
            $requete->closeCursor();
            return $result['ID'];
        } else {
            try {
                throw new Exception_Bdd_Integrity('Corruption de la table "admissibles". Non unicite des champs');
            } catch (Exception $e) {
            }
            Logs::logger(2, 'Corruption de la table "admissibles". Non unicite des champs');
            $result = $requete->fetch(PDO::FETCH_ASSOC);
            $requete->closeCursor();
            return $result['ID'];
        }
    }

    /**
     * Méthode renvoyant false si l'admissible a déjà une demande en cours.
     *
     * @access public
     * @param Model_Demande $demande
     * @throws Exception_Bdd_Query
     * @return bool
     */
    public function autorisation($demande)
    {
        try {
            $requete = $this->_db->prepare(
                            'SELECT demandes.ID
                           FROM demandes
                           INNER JOIN admissibles
                           ON demandes.ID_ADMISSIBLE = admissibles.ID
                           WHERE admissibles.NOM = :nom
                           AND admissibles.PRENOM = :prenom
                           AND admissibles.ADRESSE_MAIL = :email
                           AND admissibles.SERIE = :serie
                           AND admissibles.ID_FILIERE = :filiere
                           AND demandes.ID_STATUS <= 2');
            $requete->bindValue(':nom', $demande->nom());
            $requete->bindValue(':prenom', $demande->prenom());
            $requete->bindValue(':email', $demande->email());
            $requete->bindValue(':serie', $demande->serie());
            $requete->bindValue(':filiere', $demande->filiere());
            $requete->execute();
            return ($requete->rowCount() == 0);
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Demande::autorisation', Exception_Bdd_Query::Level_Major, $e);
            return false;
        }
    }

    /**
     * Méthode permettant de mettre à jour le status d'une demande.
     *
     * @access public
     * @param string $code
     * @throws Exception_Bdd_Query
     * @return void
     */
    public function updateStatus($code, $status)
    {
        if (!is_numeric($status) || preg_match('#^[a-f0-9]{32}$#', $code) != 1) {
            throw new Exception_Bdd_Query('Corruption des parametres. Manager_Demande::updateStatus', Exception_Bdd_Query::Currupt_Params);
        }
        try {
            $requete = $this->_db->prepare('UPDATE demandes
                                           SET ID_STATUS = :status
                                           WHERE LIEN = :code');
            $requete->bindValue(':status', $status);
            $requete->bindValue(':code', $code);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Demande::updateStatus', Exception_Bdd_Query::Level_Critical, $e);
        }

    }

    /**
     * Méthode retournant une demande en particulier.
     *
     * @access public
     * @param string $code
     * @throws Exception_Bdd_Query
     * @return Demande
     */
    public function getUnique($code)
    {
        if (preg_match('#^[0-9a-f]{32}$#', $code) != 1) {
            throw new Exception_Bdd_Query('Corruption des parametres. Manager_Demande::getUnique', Exception_Bdd_Query::Currupt_Params);
        }
        try {
            $requete = $this->_db->prepare(
                            'SELECT admissibles.ID AS id,
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
                           WHERE LIEN = :code'
                    );
            $requete->bindValue(':code', $code);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Demande::getUnique', Exception_Bdd_Query::Level_Critical, $e);
        }
        if ($requete->rowCount() != 1) {
            throw new Exception_Bdd_Integrity('Corruption de la table "demandes". Non unicite de "LIEN" ou lien');
        } else if ($requete->rowCount() == 0) {
            throw new Exception_Bdd_Query('Corruption des paramètres : Manager_Demande::getUnique : la recherche n\'a renvoyé aucun résultat', Exception_Bdd_Query::Currupt_Params);
        }

        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_Demande');

        return $requete->fetch();
    }

    /**
     * Méthode retournant la liste de toutes les demandes.
     *
     * @access public
     * @throws Exception_Bdd_Query
     * @return array(Demande)
     */
    public function getList()
    {
        try {
            $requete = $this->_db->prepare(
                            'SELECT admissibles.ID AS id,
                                    admissibles.NOM AS nom,
                                    admissibles.PRENOM AS prenom,
                                    admissibles.ADRESSE_MAIL AS email,
                                    admissibles.SEXE AS sexe,
                             CONCAT(ref_etablissements.COMMUNE," - ",ref_etablissements.NOM) AS prepa,
                                    ref_filieres.NOM AS filiere,
                                    series.INTITULE AS serie,
                                    demandes.USER_X AS userEleve,
                                    ref_statuts.NOM AS status,
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
                           INNER JOIN ref_statuts
                           ON demandes.ID_STATUS = ref_statuts.ID
                           ORDER BY series.DATE_DEBUT,
                                    status,
                                    ref_filieres.NOM,
                                    admissibles.NOM,
                                    admissibles.PRENOM'
                    );
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Demande::getList', Exception_Bdd_Query::Level_Minor, $e);
        }
        $requete->setFetchMode(PDO::FETCH_CLASS, 'Model_Demande'); // Attention, les champs référencés contiennent les nom
        $listeDemandes = $requete->fetchAll();
        $requete->closeCursor();

        return $listeDemandes;
    }

    /**
     * Méthode retournant la liste des demandes d'un X en particulier.
     *
     * @access public
     * @param string $user
     * @throws Exception_Bdd_Query
     * @return array
     */
    public function getDemandes($user)
    {
        if (preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#', $user) != 1) { // de la forme prenom.nom(.2011)
            throw new Exception_Bdd_Query('Corruption des parametres. Manager_Demande::getDemandes', Exception_Bdd_Query::Currupt_Params);
        }
        try {
            $requete = $this->_db->prepare(
                          'SELECT admissibles.ID AS id,
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
                           WHERE demandes.USER_X = :user'
                         );
            $requete->bindValue(':user', $user);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Demande::getDemandes', Exception_Bdd_Query::Level_Major, $e);
        }

        $requete->setFetchMode(PDO::FETCH_CLASS, 'Model_Demande'); // Attention, les champs référencés contiennent les noms et non les valeurs

        return $requete->fetchAll();
    }

}
