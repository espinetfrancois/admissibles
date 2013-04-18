<?php
/**
 * Classe de gestion des parametres de l'interface
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */
class Parametres {

    /**
     * Connexion à la base de données
     * @var PDO
     * @access protected
     */
    protected  $db;

    /**
     * Constantes relatives aux types de données
     */
    const Etablissement = 1;
    const Filiere = 2;
    const Serie = 3;
    const Section = 4;
    const Promo = 5;

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
     * Méthode de remise à zéro de l'interface
     * @access public
     * @return void
     */
    public  function remiseAZero()
    {
        try {
            $requete = $this->db->query('DELETE FROM disponibilites');
            $requete = $this->db->query('DELETE FROM series');
            $requete = $this->db->query('DELETE FROM demandes');
            $requete = $this->db->query('DELETE FROM x');
            $requete = $this->db->query('DELETE FROM admissibles');
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Erreur lors de l'execution de la requête SQL Parametres::remiseAZero", Exception_Bdd_Query::Level_Critical, $e);
        }
    }


    /**
     * Méthode retournant les series pour lesquelles l'interface est actuellement en ligne
     * @access public
     * @return array(int)
     */
    public  function getCurrentSeries()
    {
        try {
            $requete = $this->db->prepare('SELECT ID AS id,
                                                  INTITULE AS intitule,
                                                  DATE_DEBUT AS date_debut,
                                                  DATE_FIN AS date_fin
                                           FROM series
                                           WHERE OUVERTURE <= :time
                                           AND FERMETURE >= :time');
            $requete->bindValue(':time', time());
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Erreur lors de l'execution de la requête SQL Parametres::getCurrentSeries", Exception_Bdd_Query::Level_Blocker, $e);
        }
        $n = $requete->rowCount();
        if ($n == 0) {
            return array();
        } else {
            return $requete->fetchAll();
        }
    }


    /**
     * Méthode retournant les valeurs prédéfinies des listes de formulaires
     * @access public
     * @param int $type
     * @return array
     */
    public  function getList($type)
    {
        switch ($type) {
        case self::Etablissement:
            $champs = 'ID AS id, NOM AS nom, COMMUNE AS ville';
            $table = 'ref_etablissements';
            $order = 'COMMUNE, NOM';
            break;

        case self::Filiere:
            $champs = 'ID AS id, NOM AS nom';
            $table = 'ref_filieres';
            $order = 'NOM';
            break;

        case self::Serie:
            $champs = 'ID AS id, INTITULE AS intitule, DATE_DEBUT AS date_debut, DATE_FIN AS date_fin, OUVERTURE AS ouverture, FERMETURE AS fermeture';
            $table = 'series';
            $order = 'DATE_DEBUT';
            break;

        case self::Section:
            $champs = 'DISTINCT SECTION AS section';
            $table = 'x';
            $order = 'SECTION';
            break;

        case self::Promo:
            $champs = 'DISTINCT PROMOTION';
            $table = 'x';
            $order = 'PROMOTION';
            break;

        default:
            throw new Exception_Bdd_Query('Corruption des parametres. Parametres::getList', Exception_Bdd_Query::Currupt_Params);
            break;
        }
        try {
            $requete = $this->db->prepare('SELECT '.$champs.'
                                           FROM '.$table.'
                                           ORDER BY '.$order.'');
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Erreur lors de l'execution de la requête SQL Parametres::getList", Exception_Bdd_Query::Level_Blocker, $e);
        }
        $liste = $requete->fetchAll();
        $requete->closeCursor();

        return $liste;
    }


    /**
     * Méthode ajoutant un élément à une liste
     * @access public
     * @param int $type
     * @param array $donnees
     * @return void
     */
    public  function addToList($type, $donnees)
    {
        switch ($type) {
        case self::Etablissement:
            $valeurs = 'NOM = :nom, COMMUNE = :commune';
            $table = 'ref_etablissements';
            $array = array('nom' => htmlentities($donnees['nom']), 'commune' => htmlentities($donnees['commune']));
            break;

        case  self::Filiere:
            $valeurs = 'NOM = :nom';
            $table = 'ref_filieres';
            $array = array('nom' => htmlentities($donnees['nom']));
            break;

        case  self::Serie:
            $valeurs = 'INTITULE = :intitule, DATE_DEBUT = :date_debut, DATE_FIN = :date_fin, OUVERTURE = :ouverture, FERMETURE = :fermeture';
            $table = 'series';
            $array = array('intitule' => htmlentities($donnees['intitule']), 'date_debut' => $donnees['date_debut'], 'date_fin' => $donnees['date_fin'], 'ouverture' => $donnees['ouverture'], 'fermeture' => $donnees['fermeture']);
            break;

        default:
            throw new Exception_Bdd_Query('Corruption des parametres. Parametres::addToList', Exception_Bdd_Query::Currupt_Params);
            break;
        }
        try {
            $requete = $this->db->prepare('INSERT INTO '.$table.'
                                           SET '.$valeurs);
            foreach($array as $key => $value) {
                $requete->bindValue(':'.$key, $value);
            }
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Erreur lors de l\'execution de la requête :  Parametres::addToList', Exception_Bdd_Query::Level_Major, $e);
        }
    }


    /**
     * Méthode retirant un élément à une liste
     * @access public
     * @param int $type
     * @param array $id
     * @return void
     */
    public  function deleteFromList($type, $id)
    {
        switch ($type) {
        case self::Etablissement:
            $table = 'ref_etablissements';
            break;

        case  self::Filiere:
            $table = 'ref_filieres';
            break;

        case  self::Serie:
            $table = 'series';
            break;

        default:
            throw new Exception_Bdd_Query('Corruption des parametres. Parametres::deleteFromList', Exception_Bdd_Query::Currupt_Params);
            break;
        }
        try {
            $requete = $this->db->prepare('SELECT ID
                                           FROM '.$table.'
                                           WHERE ID = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Erreur lors de l'execution de la requête : Parametres::deleteFromList", Exception_Bdd_Query::Level_Minor, $e);
        }

        if ($requete->rowCount() != 1) {
            throw new Exception_Bdd_Query( 'Corruption de la table '.$table.'. Tentative de suppression d\'un element inexistant', Exception_Bdd_Query::Level_Minor, $e);
        }
        $requete->closeCursor();
        try {
            $requete = $this->db->prepare('DELETE FROM '.$table.'
                                           WHERE ID = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query( 'Erreur lors de l\'execution de la requête : Parametres::deleteFromList', Exception_Bdd_Query::Level_Minor, $e);
        }
    }


    /**
     * Méthode vérifiant l'utilisation d'un paramètre
     * @access public
     * @param int $type
     * @param array $id
     * @return bool
     */
    public  function isUsedList($type, $id)
    {
        switch ($type) {
        case self::Etablissement:
            try {
                $requete = $this->db->prepare('SELECT *
                                               FROM x
                                               WHERE x.ID_ETABLISSEMENT = :id');
                $requete->bindValue(':id', $id);
                $requete->execute();
                $requete2 = $this->db->prepare('SELECT *
                                               FROM admissibles
                                               WHERE admissibles.ID_ETABLISSEMENT = :id');
                $requete2->bindValue(':id', $id);
                $requete2->execute();
            } catch (Exception $e) {
                throw new Exception_Bdd_Query('Erreur lors de l\'execution de la requête : Parametres::isUsedList', Exception_Bdd_Query::Level_Major, $e);
            }

            return ($requete->rowCount() + $requete2->rowCount() > 0);
            break;

        case self::Filiere:
            try {
                $requete = $this->db->prepare('SELECT *
                                               FROM x
                                               WHERE x.ID_FILIERE = :id');
                $requete->bindValue(':id', $id);
                $requete->execute();
                $requete2 = $this->db->prepare('SELECT *
                                               FROM admissibles
                                               WHERE admissibles.ID_FILIERE = :id');
                $requete2->bindValue(':id', $id);
                $requete2->execute();
            } catch (Exception $e) {
                throw new Exception_Bdd_Query('Erreur lors de l\'execution de la requête : Parametres::isUsedList', Exception_Bdd_Query::Level_Major, $e);
            }

            return ($requete->rowCount() + $requete2->rowCount() > 0);
            break;

        case self::Serie:
            try {
                $requete = $this->db->prepare('SELECT *
                                               FROM admissibles
                                               WHERE admissibles.SERIE = :id');
                $requete->bindValue(':id', $id);
                $requete->execute();
                $requete2 = $this->db->prepare('SELECT *
                                               FROM disponibilites
                                               WHERE disponibilites.ID_SERIE = :id');
                $requete2->bindValue(':id', $id);
                $requete2->execute();
            } catch (Exception $e) {
                throw new Exception_Bdd_Query('Erreur lors de l\'execution de la requête : Parametres::isUsedList', Exception_Bdd_Query::Level_Major, $e);
                            }

            return ($requete->rowCount() + $requete2->rowCount() > 0);
            break;

        default:
            throw new Exception_Bdd_Query('Corruption des parametres : Parametres::isUsedList', Exception_Bdd_Query::Currupt_Params);
            break;
        }

    }


    /**
     * Méthode insérant les listes d'admissibilité en BDD
     * @access public
     * @param int $serie
     * @param int $filiere
     * @param string $donnees
     * @return void
     */
    public  function parseADM($serie, $filiere, $donnees)
    {
        // vérification du paramètre $serie
        try {
            $requete = $this->db->prepare('SELECT ID
                                           FROM series
                                           WHERE ID = :id
                                           AND FERMETURE > '.time());
            $requete->bindValue(':id', $serie);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Problème lors de l\'execution de la requête : Parametres::parseADM', Exception_Bdd_Query::Level_Major, $e);
        }

        if ($requete->rowCount() != 1) {
            throw new Exception_Bdd_Query("La série demandée n'a pas été trouvée dans la base de donnée.", Exception_Bdd_Query::Level_Major);
        }
        $requete->closeCursor();

        // vérification du paramètre $filiere
        try {
            $requete = $this->db->prepare('SELECT ID
                                           FROM ref_filieres
                                           WHERE ID = :id');
            $requete->bindValue(':id', $filiere);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query('Problème lors de l\'execution de la requête : Parametres::parseADM', Exception_Bdd_Query::Level_Major, $e);
        }

        if ($requete->rowCount() != 1) {
            throw new Exception_Bdd_Query("La filière demandée n'a pas été trouvée dans la base de donnée.", Exception_Bdd_Query::Level_Major);
        }
        $requete->closeCursor();

        // parsage du paramètre $donnees
        $ligne = explode(PHP_EOL, $donnees);
        foreach ($ligne as $value) {
            // Séparation des noms de la forme : 'Nom (Prénom)'
            $value = preg_replace('#(.+)\s\((.+)\)$#','$1///$2',htmlentities($value));
            $col = explode('///', $value);
            // traitement des donnees : minuscules et sans accents
            $nom = strtolower(Parametres::wd_remove_accents($col[0]));
            $prenom = strtolower(Parametres::wd_remove_accents($col[1]));
            try {
                $requete = $this->db->prepare('INSERT INTO admissibles
                                               SET NOM = :nom,
                                                   PRENOM = :prenom,
                                                   ID_FILIERE = :filiere,
                                                   SERIE = :serie');
                $requete->bindValue(':nom', $nom);
                $requete->bindValue(':prenom', $prenom);
                $requete->bindValue(':serie', $serie);
                $requete->bindValue(':filiere', $filiere);
                $requete->execute();
            } catch (Exception $e) {
                throw new Exception_Bdd_Query("Impossible d'inserer les admissibles dans la base de donnée.", Exception_Bdd_Query::Level_Critical, $e);
            }
        }

        // Ouverture des demandes d'hébergement pour la série considérée
        try {
            $requete = $this->db->prepare('UPDATE series
                                           SET OUVERTURE = '.time().'
                                           WHERE ID = :id');
            $requete->bindValue(':id', $serie);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Impossible d'ouvrir les admissibilité pour la liste choisie.", Exception_Bdd_Query::Level_Major, $e);
        }
    }


    /**
     * Méthode retirant les accents
     * @access public
     * @access static
     * @param text $str
     * @param text $charset
     * @return text
     */
    public static  function wd_remove_accents($str, $charset='utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

        return $str;
    }


    /**
     * Méthode retournant la liste des admissibles pour une série/filière
     * @access public
     * @param int $serie
     * @param int $filiere
     * @return void
     */
    public  function getAdmissibles($serie, $filiere)
    {
        try {
            $requete = $this->db->prepare('SELECT ID AS id,
                                                  NOM AS nom,
                                                  PRENOM AS prenom,
                                                  ADRESSE_MAIL AS mail
                                           FROM admissibles
                                           WHERE SERIE = :serie
                                           AND ID_FILIERE = :filiere
                                           ORDER BY NOM, PRENOM');
            $requete->bindValue(':serie', $serie);
            $requete->bindValue(':filiere', $filiere);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Impossible de récupérer les admissibles.", Exception_Bdd_Query::Level_Major, $e);
        }

        return $requete->fetchAll();
    }


    /**
     * Méthode supprimant définitivement un admissible de la base de données
     * @access public
     * @param int $id
     * @return void
     */
    public  function supprAdmissible($id)
    {
        try {
            $requete = $this->db->prepare('DELETE FROM demandes
                                           WHERE ID_ADMISSIBLE = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();

            $requete = $this->db->prepare('DELETE FROM admissibles
                                           WHERE ID = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();
        } catch (Exception $e) {
            throw new Exception_Bdd_Query("Impossible de supprimer un admissible", Exception_Bdd_Query::Level_Minor, $e);
        }
    }
}