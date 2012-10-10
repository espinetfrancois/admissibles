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
    const ETABLISSEMENT = 1;
    const FILIERE = 2;
    const PROMO = 3;
    const SECTION = 4;
    const SERIE = 5;

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
     * Méthode retournant le nom d'utilisateur de l'administrateur
     * @access public
     * @return string
     */
    
    public  function getAdmin() {
        $requete = $this->db->prepare('SELECT VALEUR AS admin
                                       FROM administration
                                       WHERE PARAMETRE = "administrateur"');
        $requete->execute();
        if ($requete->rowCount() != 1) {
            throw new RuntimeException('Corruption de la table des paramètres'); // Ne se produit jamais en exécution courante
        }
        $res = $requete->fetch();
        if (!preg_match('#^[a-z0-9_-]+\.[a-z0-9_-]+(\.?[0-9]{4})?$#',$res['admin'])) {
            throw new RuntimeException('Corruption de la table des paramètres'); // Ne se produit jamais en exécution courante
        }
        return $res['admin'];
    }


    /**
     * Méthode retournant la serie pour laquelle l'interface est actuellement en ligne
     * @access public
     * @return int
     */

    public  function getSerie() {
        $requete = $this->db->prepare('SELECT ID AS id,
                                              INTITULE AS intitule,
                                              DATE_DEBUT AS debut,
                                              DATE_FIN AS fin
                                       FROM series
                                       WHERE OUVERTURE <= :time
                                       AND FERMETURE >= :time');
        $requete->bindValue(':time', time());
        $requete->execute();
        $n = $requete->rowCount();
        if ($n == 0) {
            return (array("id" => -1));
        } elseif ($n == 1) {
            return $requete->fetch();
        } else {
            throw new RuntimeException('Chevauchement de séries'); // Ne se produit jamais en exécution courante
        }
    }


    /**
     * Méthode retournant les valeurs prédéfinies des listes de formulaires
     * @access public
     * @param int $type 
     * @return array
     */

    public  function getList($type) {
        switch ($type) {
        case self::ETABLISSEMENT:
            $champs = "ID AS id, NOM AS nom, COMMUNE AS ville";
            $table = "ref_etablissements";
            $order = "COMMUNE, NOM";
            break;

        case self::FILIERE:
            $champs = "ID AS id, NOM AS nom";
            $table = "ref_filieres";
            $order = "NOM";
            break;

        case self::PROMO:
            $champs = "ID AS id, NOM AS nom";
            $table = "ref_promotions";
            $order = "NOM";
            break;

        case self::SECTION:
            $champs = "ID AS id, NOM AS nom";
            $table = "ref_sections";
            $order = "NOM";
            break;

        case self::SERIE:
            $champs = "ID AS id, INTITULE AS intitule, DATE_DEBUT AS date_debut, DATE_FIN AS date_fin, OUVERTURE AS ouverture, FERMETURE AS fermeture";
            $table = "series";
            $order = "DATE_DEBUT";
            break;
        
        default:
            throw new RuntimeException('Mauvais type de liste'); // Ne se produit jamais en exécution courante
            break;
        }
        $requete = $this->db->prepare('SELECT '.$champs.'
                                       FROM '.$table.'
                                       ORDER BY '.$order.'');
        $requete->execute();
        $liste = $requete->fetchAll();
        $requete->closeCursor();
        return $liste;
    }
    

    /**
     * Méthode ajoutant un élément à une liste
     * @access public
     * @param int $type 
     * @param array $liste 
     * @return void
     */

    public  function addToList($type, $donnees) {
        switch ($type) {
        case self::ETABLISSEMENT:
            $valeurs = "NOM = :nom, COMMUNE = :commune";
            $table = "ref_etablissements";
            $array = array("nom" => $donnees['nom'], "commune" => $donnees['commune']);
            break;
        
        case  self::FILIERE:
            $valeurs = "NOM = :nom";
            $table = "ref_filieres";
            $array = array("nom" => $donnees['nom']);
            break;
            
        case  self::PROMO:
            $valeurs = "NOM = :nom";
            $table = "ref_promotions";
            $array = array("nom" => $donnees['nom']);
            break;
            
        case  self::SECTION:
            $valeurs = "NOM = :nom";
            $table = "ref_sections";
            $array = array("nom" => $donnees['nom']);
            break;
            
        case  self::SERIE:
            $valeurs = "INTITULE = :intitule, DATE_DEBUT = :date_debut, DATE_FIN = :date_fin, OUVERTURE = :ouverture, FERMETURE = :fermeture";
            $table = "series";
            $array = array("intitule" => $donnees['intitule'], "date_debut" => $donnees['date_debut'], "date_fin" => $donnees['date_fin'], "ouverture" => $donnees['ouverture'], "fermeture" => $donnees['fermeture']);
            break;
        
        default:
            throw new RuntimeException('Mauvais type de liste'); // Ne se produit jamais en exécution courante
            break;
        }
        $requete = $this->db->prepare('INSERT INTO '.$table.'
                                       SET '.$valeurs);
        foreach($array as $key => $value) {
            $requete->bindValue(':'.$key, $value);
        }
        $requete->execute();
    }
    
    
    /**
     * Méthode retirant un élément à une liste
     * @access public
     * @param int $type 
     * @param array $id 
     * @return void
     */

    public  function deleteFromList($type, $id) {
        switch ($type) {
        case self::ETABLISSEMENT:
            $table = "ref_etablissements";
            break;
        
        case  self::FILIERE:
            $table = "ref_filieres";
            break;
            
        case  self::PROMO:
            $table = "ref_promotions";
            break;
            
        case  self::SECTION:
            $table = "ref_sections";
            break;
            
        case  self::SERIE:
            $table = "series";
            break;
        
        default:
            throw new RuntimeException('Mauvais type de liste'); // Ne se produit jamais en exécution courante
            break;
        }
        $requete = $this->db->prepare('SELECT ID
                                       FROM '.$table.'
                                       WHERE ID = :id');
        $requete->bindValue(':id', $id);
        $requete->execute();
        
        if ($requete->rowCount() != 1) {
            throw new RuntimeException('Element de liste inconnu'); // Ne se produit jamais en exécution courante
        }
        $requete->closeCursor();
        
        $requete = $this->db->prepare('DELETE FROM '.$table.'
                                       WHERE ID = :id');
        $requete->bindValue(':id', $id);
        $requete->execute();
    }
    
    
    /**
     * Méthode vérifiant l'utilisation d'un paramètre
     * @access public
     * @param int $type 
     * @param array $id 
     * @return bool
     */

    public  function isUsedList($type, $id) {
        switch ($type) {
        case self::ETABLISSEMENT:
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
            
            if ($requete->rowCount() + $requete2->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
            break;
        
        case self::FILIERE:
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
            
            if ($requete->rowCount() + $requete2->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
            break;
            
        case self::PROMO:
            $requete = $this->db->prepare('SELECT *
                                           FROM x
                                           WHERE ID_PROMOTION = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();
            
            if ($requete->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
            break;
            
        case self::SECTION:
            $requete = $this->db->prepare('SELECT *
                                           FROM x
                                           WHERE ID_SECTION = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();
            
            if ($requete->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
            break;
            
        case self::SERIE:
            $requete = $this->db->prepare('SELECT *
                                           FROM admissible, disponibilites
                                           WHERE admissible.SERIE = :id
                                           OR disponibilites.ID_SERIE = :id');
            $requete->bindValue(':id', $id);
            $requete->execute();
            
            if ($requete->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
            break;
        
        default:
            throw new RuntimeException('Mauvais type de liste'); // Ne se produit jamais en exécution courante
            break;
        }
    }
    
    
    /**
     * Méthode insérant les listes d'admissibilité en BDD
     * @access public
     * @param int $serie 
     * @param string $donnees 
     * @return void
     */

    public  function parseADM($serie, $donnees) {
        // vérification du paramètre $serie
        $requete = $this->db->prepare('SELECT ID
                                       FROM series
                                       WHERE ID = :id');
        $requete->bindValue(':id', $serie);
        $requete->execute();
        
        if ($requete->rowCount() != 1) {
            throw new RuntimeException("Série d'admissibilité inconnue"); // Ne se produit jamais en exécution courante
        }
        $requete->closeCursor();
        
        // parsage du paramètre $donnees
        $ligne = explode(";;", $donnees);
        foreach ($ligne as $value) {
            $col = explode(";", $value);
            // traitement des donnees : minuscules et sans accents
            $nom = strtolower(strtr($col[0],'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
                                          'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));
            $prenom = strtolower(strtr($col[1],'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
                                             'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));
            $requete = $this->db->prepare('INSERT INTO admissibles
                                           SET NOM = :nom,
                                                  PRENOM = :prenom
                                               SERIE = :serie');
            $requete->bindValue(':nom', $nom);
            $requete->bindValue(':prenom', $prenom);
            $requete->bindValue(':serie', $serie);
            $requete->execute();
        }
    }

}
?>