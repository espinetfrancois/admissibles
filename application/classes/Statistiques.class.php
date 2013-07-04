<?php

/**
 * Classe de gestion des parametres de l'interface.
 *
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */
class Statistiques extends Manager
{
    function getNbAnnonces() {
        try {
            $requete = $this->_db->query(  'SELECT rc.NOM,COUNT(*) as NOMBRE
                                        FROM `annonces` AS `a`
                                        INNER JOIN `ref_categories` AS `rc` ON `rc`.`ID` = `a`.`ID_CATEGORIE`
                                        GROUP BY `ID_CATEGORIE` '
                                    );
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_NAMED);
        } catch (PDOException $e) {
            throw new Exception_Bdd('Erreur lors de la récupération du nombre d\'annonces');
        }
    }


    function getNbTotalDemandes() {
        try {
        	$requete = $this->_db->query(  'SELECT rs.NOM,COUNT(*) as NOMBRE
                                        FROM `demandes` AS `d`
                                        INNER JOIN `ref_statuts` AS `rs` ON `rs`.`ID` = `d`.`ID_STATUS`
        	                            GROUP BY `ID_STATUS`'
        	);
        	$requete->execute();
        	return $requete->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_NAMED);
        } catch (PDOException $e) {
        	throw new Exception_Bdd('Erreur lors de la récupération des totaux des demandes.');
        }
    }

    function getNbDemandesSeries() {
        try {
        	$requete = $this->_db->query(  'SELECT s.INTITULE, rs.NOM,COUNT(*) as NOMBRE
                                        FROM `demandes` AS `d`
                                        INNER JOIN `ref_statuts` AS `rs` ON `rs`.`ID` = `d`.`ID_STATUS`
                                        INNER JOIN `admissibles` AS `a` ON `a`.`ID` = `d`.`ID_ADMISSIBLE`
                                        INNER JOIN `series` as `s`ON `a`.`SERIE` = `s`.`ID`
                                        GROUP BY `a`.`SERIE`,`d`.`ID_STATUS`'
        	);
        	$requete->execute();
        	return $requete->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);
        } catch (PDOException $e) {
        	throw new Exception_Bdd('Erreur lors de la récupération des totaux des demandes.');
        }
    }

    function getMailsAdmissibles() {
        try {
        	$requete = $this->_db->query(  'SELECT `NOM`,
                                               `PRENOM`,
                                               `ADRESSE_MAIL` AS MAIL,
                                               s.`INTITULE`
                                        FROM `admissibles` as a
                                        INNER JOIN `series` as s ON s.`ID` = a.`SERIE`
        	                            WHERE `ADRESSE_MAIL` <> ""'
        	);
        	$requete->execute();
        	return $requete->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_NAMED);
        } catch (PDOException $e) {
        	throw new Exception_Bdd('Erreur lors de la récupération des adresses email des admissibles.');
        }
    }

    function getMailsX() {
        try {
        	$requete = $this->_db->query(  'SELECT `USER`,
                                               `ADRESSE_MAIL` AS MAIL,
                                               `PROMOTION`
                                        FROM `x` as x
                                        WHERE 1'
        	);
        	$requete->execute();
        	return $requete->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_NAMED);
        } catch (PDOException $e) {
        	throw new Exception_Bdd('Erreur lors de la récupération des adresses email des X.');
        }
    }
}
