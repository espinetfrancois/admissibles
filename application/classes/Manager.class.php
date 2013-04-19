<?php

/**
 * Class abstraite pour les managers de l'application
 * @author francois.espinet
 */
abstract class Manager
{
    /**
     * Connexion à la BDD
     * @var PDO
     * @access protected
     */
    protected  $db;

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
}