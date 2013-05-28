<?php

/**
 * Class abstraite pour les modèles de l'application.
 *
 * @author francois.espinet
 *
 */
abstract class Model
{

    /**
     * Constructeur de la classe qui assigne les données spécifiées en paramètre aux attributs correspondants.
     *
     * @access public
     * @param array $valeurs Les valeurs à assigner
     */
    public function __construct($valeurs = array())
    {
        if (empty($valeurs) === false) { // Si on a spécifié des valeurs, alors on hydrate l'objet
            $this->hydrate($valeurs);
        }
    }

    /**
     * Méthode assignant les valeurs spécifiées aux attributs correspondant.
     *
     * @access public
     * @param array $donnees Les données à assigner
     * @return void
     */
    public function hydrate($donnees)
    {
        foreach ($donnees as $attribut => $valeur) {
            $methode = 'set' . ucfirst($attribut);
            if (is_callable(array($this, $methode)) === true) {
                $this->$methode($valeur);
            }
        }
    }

    /**
     * Méthode disant si l'objet est valide ou non.
     *
     * @author francois.espinet
     * @return boolean
     */
    abstract public function isValid();

}
