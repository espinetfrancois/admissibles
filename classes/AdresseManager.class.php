<?php
/**
 * Gestionnaire BDD de la classe Adresse
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 0.5
 *
 *@todo : gestion des erreurs
 */

class AdresseManager {

    /**
     * Connexion à la BDD
     * @var PDO
     * @access protected
     */
    protected  $db;


    /**
     * Méthode permettant d'ajouter une adresse
     * @access protected
     * @param Adresse $adresse 
     * @return void
     */

    protected  function add(Adresse $adresse) {
        $requete = $this->db->prepare('INSERT INTO annonces 
                                       SET NOM = :nom,
                                           RANG = :rang,
                                           TELEPHONE = :tel,
                                           DESCRIPTION = :description,
                                           VALIDATION = :valid,
                                           ADRESSE_MAIL = :email,
                                           ADRESSE = :adresse,
                                           ID_CATEGORIE = :categorie'); 
        $requete->bindValue(':nom', $adresse->nom());
        $requete->bindValue(':rang', $adresse->ordre());
        $requete->bindValue(':tel', $adresse->tel());
        $requete->bindValue(':description', $adresse->description());
        $requete->bindValue(':valid', $adresse->valid());
        $requete->bindValue(':email', $adresse->email());
        $requete->bindValue(':adresse', $adresse->adresse());
        $requete->bindValue(':categorie', $adresse->categorie());
        $requete->execute();
    }


    /**
     * Méthode permettant de modifier une adresse
     * @access protected
     * @param Adresse $adresse 
     * @return void
     */

    protected  function update(Adresse $adresse) {
        $requete = $this->db->prepare('UPDATE annonces 
                                       SET NOM = :nom,
                                           RANG = :rang,
                                           TELEPHONE = :tel,
                                           DESCRIPTION = :description,
                                           VALIDATION = :valid,
                                           ADRESSE_MAIL = :email,
                                           ADRESSE = :adresse,
                                           ID_CATEGORIE = :categorie
                                       WHERE ID = :id'); 
        $requete->bindValue(':id', $adresse->id());
        $requete->bindValue(':nom', $adresse->nom());
        $requete->bindValue(':rang', $adresse->ordre());
        $requete->bindValue(':tel', $adresse->tel());
        $requete->bindValue(':description', $adresse->description());
        $requete->bindValue(':valid', $adresse->valid());
        $requete->bindValue(':email', $adresse->email());
        $requete->bindValue(':adresse', $adresse->adresse());
        $requete->bindValue(':categorie', $adresse->categorie());
        $requete->execute();
    }


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
     * Méthode permettant d'enregistrer une adresse valide
     * @access public
     * @param Adresse $adresse 
     * @return void
     */

    public final  function save(Adresse $adresse) {
        if ($adresse->isValid()) {
            $adresse->isNew() ? $this->add($adresse) : $this->update($adresse);
        } else {
            throw new RuntimeException('Les champs doivent être valides pour être enregistrés'); // Ne se produit jamais en exécution courante
        }
    }


    /**
     * Méthode retournant une adresse en particulier
     * @access public
     * @param int $id 
     * @return Adresse
     */

    public  function getUnique($id) {
        if (!is_numeric($id)) {
            throw new RuntimeException('ID invalide'); // Ne se produit jamais en exécution courante
        }
        $requete = $this->db->prepare('SELECT ID AS id,
                                              NOM AS nom,
                                              RANG AS ordre,
                                              TELEPHONE AS tel,
                                              DESCRIPTION AS description,
                                              VALIDATION AS valid,
                                              ADRESSE_MAIL AS email,
                                              ADRESSE AS adresse,
                                              ID_CATEGORIE AS categorie
                                       FROM annonces
                                       WHERE ID = :id');
        $requete->bindValue(':id', $id);
        $requete->execute();
        if ($requete->rowCount() != 1) {
            throw new RuntimeException('Plusieurs annoncent ont le même identifiant'); // Ne se produit jamais en exécution courante
        }
            
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Adresse');
            
        return $requete->fetch();
    }


    /**
     * Méthode retournant la liste complète des adresses
     * @access public
     * @return array
     */

    public  function getList() {
        $requete = $this->db->prepare('SELECT ID AS id,
                                              NOM AS nom,
                                              RANG AS ordre,
                                              TELEPHONE AS tel,
                                              DESCRIPTION AS description,
                                              VALIDATION AS valid,
                                              ADRESSE_MAIL AS email,
                                              ADRESSE AS adresse,
                                              ID_CATEGORIE AS categorie
                                       FROM annonces
                                       ORDER BY valid DESC,
                                                   ordre');
        $requete->execute();
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Adresse');
            
        $listeAdresse = $requete->fetchAll();
        $requete->closeCursor();
        return $listeAdresse;
    }


    /**
     * Méthode retournant la liste d'affichage des adresses
     * @access public
     * @return array
     */

    public  function getListValid() {
        $requete = $this->db->prepare('SELECT annonces.ID AS id,
                                              annonces.NOM AS nom,
                                              annonces.RANG AS ordre,
                                              annonces.TELEPHONE AS tel,
                                              annonces.DESCRIPTION AS description,
                                              annonces.ADRESSE_MAIL AS email,
                                              annonces.ADRESSE AS adresse,
                                              ref_categories.NOM AS categorie
                                       INNER JOIN ref_categories
                                       ON ref_categories.ID = annonces.ID_CATEGORIE
                                       FROM annonces
                                       WHERE VALIDATION = 1
                                       ORDER BY ordre');
        $requete->execute();
        $requete->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Adresse'); // les champs références contiennent maintenant la valeur
            
        $listeAdresse = $requete->fetchAll();
        $requete->closeCursor();
        return $listeAdresse;
    }


}
?>