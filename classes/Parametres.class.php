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
            
        case  self::SERIE:
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

    public  function addToList($type, $donnees) { // $donnees a controler à la validation du formulaire
        switch ($type) {
        case self::ETABLISSEMENT:
            $valeurs = "NOM =".$donnees['nom'].", COMMUNE =".$donnees['commune'];
            $table = "ref_etablissements";
            break;
        
        case  self::FILIERE:
            $valeurs = "NOM =".$donnees['nom'];
            $table = "ref_filieres";
            break;
            
        case  self::PROMO:
            $valeurs = "NOM =".$donnees['nom'];
            $table = "ref_promotions";
            break;
            
        case  self::SECTION:
            $valeurs = "NOM =".$donnees['nom'];
            $table = "ref_sections";
            break;
            
        case  self::SERIE:
            $valeurs = "INTITULE =".$donnees['intitule'].", DATE_DEBUT =".$donnees['date_debut'].", DATE_FIN =".$donnees['date_fin'].", OUVERTURE =".$donnees['ouverture'].", FERMETURE =".$donnees['fermeture'];
            $table = "series";
            break;
        
        default:
            throw new RuntimeException('Mauvais type de liste'); // Ne se produit jamais en exécution courante
            break;
        }
        $requete = $this->db->prepare('INSERT INTO '.$table.'
                                       SET '.$valeurs);
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