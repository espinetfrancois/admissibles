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
            return $this->_db->query(  'SELECT rc.NOM,COUNT(*)
                                        FROM `annonces` AS `a`
                                        INNER JOIN `ref_categories` AS `rc` ON `rc`.`ID` = `a`.`ID_CATEGORIE`
                                        GROUP BY `ID_CATEGORIE` '
                                    );
        } catch (PDOException $e) {
            throw new Exception_Bdd('Erreur lors de la récupération du nombre d\'annonces');
        }
    }


    function getNbTotalDemandes() {
        try {
        	return $this->_db->query(  'SELECT rs.NOM,COUNT(*)
                                        FROM `demandes` AS `d`
                                        INNER JOIN `ref_statuts` AS `rs` ON `rs`.`ID` = `d`.`ID_STATUS`
        	                            GROUP BY `ID_STATUS`'
        	);
        } catch (PDOException $e) {
        	throw new Exception_Bdd('Erreur lors de la récupération des totaux des demandes.');
        }
    }

    function getNbDemandesSeries() {
        try {
        	return $this->_db->query(  'SELECT s.INTITULE, rs.NOM,COUNT(*)
                                        FROM `demandes` AS `d`
                                        INNER JOIN `ref_statuts` AS `rs` ON `rs`.`ID` = `d`.`ID_STATUS`
                                        INNER JOIN `admissibles` AS `a` ON `a`.`ID` = `d`.`ID_ADMISSIBLE`
                                        INNER JOIN `series` as `s`ON `a`.`SERIE` = `s`.`ID`
                                        GROUP BY `a`.`SERIE`,`d`.`ID_STATUS`'
        	);
        } catch (PDOException $e) {
        	throw new Exception_Bdd('Erreur lors de la récupération des totaux des demandes.');
        }
    }
}
