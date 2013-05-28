<?php

/**
 * Gestionnaire BDD de la classe Adresse
 *
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 * @package Manager
 */
class Manager_Adresse extends Manager
{

    /**
     * Méthode permettant d'ajouter une adresse.
     *
     * @access protected
     * @param Model_Adresse $adresse
     * @throws Exception_Bdd_Query
     * @return void
     */
    protected function add(Model_Adresse $adresse)
    {
        try {
            $requete = $this->_db->prepare(
                          'INSERT INTO annonces
                           SET NOM = :nom,
                               TELEPHONE = :tel,
                               DESCRIPTION = :description,
                               VALIDATION = :valid,
                               ADRESSE_MAIL = :email,
                               ADRESSE = :adresse,
                               ID_CATEGORIE = :categorie'
                    );
            $requete->bindValue(':nom', $adresse->nom());
            $requete->bindValue(':tel', $adresse->tel());
            $requete->bindValue(':description', $adresse->description());
            $requete->bindValue(':valid', $adresse->valide());
            $requete->bindValue(':email', $adresse->email());
            $requete->bindValue(':adresse', $adresse->adresse());
            $requete->bindValue(':categorie', $adresse->categorie());
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Adresse::add', Exception_Bdd_Query::Level_Major, $e);
        }
    }

    /**
     * Méthode permettant de modifier une adresse.
     *
     * @access protected
     * @param Model_Adresse $adresse
     * @throws Exception_Bdd_Query
     * @return void
     */
    protected function update(Model_Adresse $adresse)
    {
        try {
            $requete = $this->_db->prepare(
                          'UPDATE annonces
                           SET NOM = :nom,
                               TELEPHONE = :tel,
                               DESCRIPTION = :description,
                               VALIDATION = :valid,
                               ADRESSE_MAIL = :email,
                               ADRESSE = :adresse,
                               ID_CATEGORIE = :categorie
                           WHERE ID = :id'
                    );
            $requete->bindValue(':id', $adresse->id());
            $requete->bindValue(':nom', $adresse->nom());
            $requete->bindValue(':tel', $adresse->tel());
            $requete->bindValue(':description', $adresse->description());
            $requete->bindValue(':valid', $adresse->valide());
            $requete->bindValue(':email', $adresse->email());
            $requete->bindValue(':adresse', $adresse->adresse());
            $requete->bindValue(':categorie', $adresse->categorie());
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Adresse::update', Exception_Bdd_Query::Level_Major, $e);
        }
    }

    /**
     * Méthode permettant d'enregistrer une adresse valide.
     *
     * @access public
     * @param Model_Adresse $adresse
     * @throws Exception_Bdd_Query
     * @return void
     */

    public final function save(Model_Adresse $adresse)
    {
        if ($adresse->isValid() === true) {
            $adresse->isNew() === true ? $this->_add($adresse) : $this->_update($adresse);
        } else {
            throw new Exception_Bdd_Query("Une adresse invalide à été présenté pour l'enregistrement", Exception_Bdd_Query::Currupt_Params);
        }
    }

    /**
     * Méthode permettant de supprimer une adresse.
     *
     * @access public
     * @param integer $id
     * @throws Exception_Bdd_Query
     * @return void
     */
    public final function delete($id)
    {
        if (!is_numeric($id))
            throw new Exception_Bdd_Query('Corruption des parametres : Manager_Adresse::delete', Exception_Bdd_Query::Currupt_Params);

        try {
            $requete = $this->_db->prepare('DELETE FROM annonces
                                           WHERE ID = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Adresse::delete', Exception_Bdd_Query::Level_Minor, $e);
        }

    }

    /**
     * Méthode retournant une adresse en particulier.
     * @access public
     * @param integer $id
     * @throws Exception_Bdd_Query
     * @return Model_Adresse
     */
    public function getUnique($id)
    {
        if (!is_numeric($id)) {
            throw new Exception_Bdd_Query('Corruption des parametres : Manager_Adresse::getUnique', Exception_Bdd_Query::Currupt_Params);
        }
        try {
            $requete = $this->_db->prepare(
                      'SELECT ID AS id,
                              NOM AS nom,
                              TELEPHONE AS tel,
                              DESCRIPTION AS description,
                              VALIDATION AS valide,
                              ADRESSE_MAIL AS email,
                              ADRESSE AS adresse,
                              ID_CATEGORIE AS categorie
                       FROM annonces
                       WHERE ID = :id'
                    );
            $requete->bindValue(':id', $id);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Adresse::getUnique', Exception_Bdd_Query::Level_Major, $e);
        }
        if ($requete->rowCount() != 1) {
            throw new Exception_Bdd_Integrity('Corruption de la table "annonces". ID non unique');
        }

        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_Adresse');

        return $requete->fetch();
    }

    /**
     * Méthode retournant la liste d'affichage des adresses.
     *
     * @access public
     * @throws Exception_Bdd_Query
     * @return array
     */
    public function getListAffiche($valid = 1)
    {
        try {
            $requete = $this->_db->prepare(
                          'SELECT annonces.ID AS id,
                                  annonces.NOM AS nom,
                                  annonces.TELEPHONE AS tel,
                                  annonces.DESCRIPTION AS description,
                                  annonces.ADRESSE_MAIL AS email,
                                  annonces.ADRESSE AS adresse,
                                  ref_categories.NOM AS categorie
                           FROM annonces
                           INNER JOIN ref_categories
                           ON ref_categories.ID = annonces.ID_CATEGORIE
                           WHERE annonces.VALIDATION = :valid
                           ORDER BY ref_categories.NOM'
                    );
            $requete->bindValue(':valid', $valid);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Adresse::getListAffiche', Exception_Bdd_Query::Level_Blocker, $e);
        }
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_Adresse'); // les champs références contiennent maintenant la valeur

        $listeAdresse = $requete->fetchAll();
        $requete->closeCursor();

        return $listeAdresse;
    }

    /**
     * Méthode retournant la liste des catégories d'hébergement.
     *
     * @access public
     * @throws Exception_Bdd_Query
     * @return array
     */
    public function getCategories()
    {
        try {
            $requete = $this->_db->prepare('SELECT ID AS id,
                                                  NOM AS nom
                                           FROM ref_categories');
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Adresse::getCategories', Exception_Bdd_Query::Level_Critical, $e);
        }

        return $requete->fetchAll();
    }

    /**
     * Méthode ajoutant une categorie.
     *
     * @access public
     * @param string $nom
     * @throws Exception_Bdd_Query
     * @return void
     */
    public function addCategorie($nom)
    {
        try {
            $requete = $this->_db->prepare('INSERT INTO ref_categories
                                           SET NOM = :nom');
            $requete->bindValue(':nom', $nom);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Adresse::addCategorie', Exception_Bdd_Query::Level_Major, $e);
        }
    }

    /**
     * Méthode suppriment une categorie.
     *
     * @access public
     * @param integer $id
     * @throws Exception_Bdd_Query
     * @return void
     */
    public function deleteCategorie($id)
    {
        try {
            $requete = $this->_db->prepare('DELETE
                                           FROM ref_categories
                                           WHERE ID = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Adresse::deleteCategorie', Exception_Bdd_Query::Level_Minor, $e);
        }
    }

    /**
     * Méthode vérifiant s'il y a des adresses dans une catégorie donnée.
     *
     * @access public
     * @param integer $id
     * @throws Exception_Bdd_Query
     * @return boolean
     */
    public function isUsedCat($id)
    {
        try {
            $requete = $this->_db->prepare('SELECT *
                                           FROM annonces
                                           WHERE ID_CATEGORIE = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de la requête : Manager_Adresse::isUserCat', Exception_Bdd_Query::Level_Minor, $e);
        }
        if ($requete->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
