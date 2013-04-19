<?php

abstract class Model {
    /**
     * Constructeur de la classe qui assigne les données spécifiées en paramètre aux attributs correspondants
     * @access public
     * @param array $valeurs Les valeurs à assigner
     * @return void
     */
    public  function __construct($valeurs = array())
    {
    	if (!empty($valeurs)) { // Si on a spécifié des valeurs, alors on hydrate l'objet
    		$this->hydrate($valeurs);
    	}
    }


    /**
     * Méthode assignant les valeurs spécifiées aux attributs correspondant
     * @access public
     * @param array $donnees Les données à assigner
     * @return void
     */
    public  function hydrate($donnees)
    {
    	foreach ($donnees as $attribut => $valeur) {
    		$methode = 'set'.ucfirst($attribut);
    		if (is_callable(array($this, $methode))) {
    			$this->$methode($valeur);
    		}
    	}
    }

    abstract public function isValid();

}