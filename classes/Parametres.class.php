<?php
/**
 * Classe de gestion des parametres de l'interface
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 0
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
    const CATEGORIE = 5;

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
        switch (condition) {
        case self::ETABLISSEMENT:
            $champs = "ID AS id, NOM AS nom, COMMUNE AS ville";
            $table = "ref_etablissements";
            $order = "COMMUNE, NOM";
            break;
        
        case  self::FILIERE:
            $champs = "ID AS id, NOM AS nom";
            $table = "ref_filieres";
            $order = "NOM";
            break;
            
        case  self::PROMO:
            $champs = "ID AS id, NOM AS nom";
            $table = "ref_promotions";
            $order = "NOM";
            break;
            
        case  self::SECTION:
            $champs = "ID AS id, NOM AS nom";
            $table = "ref_sections";
            $order = "NOM";
            break;
            
        case  self::CATEGORIE:
            $champs = "ID AS id, NOM AS nom, ORDRE AS ordre";
            $table = "ref_categories";
            $order = "ORDRE, NOM";
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
     * Méthode modifiant les séries
     * @access public
     * @param array $values 
     * @return void
     */

    public  function setSeries($values) {

    }


    /**
     * Méthode modifiant les listes prédéfinies de formulaires
     * @access public
     * @param int $type 
     * @param array $liste 
     * @return void
     */

    public  function setList($type, $liste) {

    }

}
?>