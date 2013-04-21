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

    /**
     * Méthode échapant une chaine (pour la protection contre xss par exemple)
     * Personnalisation de htmlentities
     * @see htmlentities
     * @author francois.espinet
     * @param string $string
     * @param string $flags
     * @param string $enc
     * @return string
     */
    public static function escape($string, $flags = ENT_HTML5, $enc = 'UTF-8') {
        return htmlentities($string, $flags, $enc);
    }


    /**
     * Méthode retirant les accents
     * @access public
     * @access static
     * @param text $str
     * @param text $charset
     * @return text
     */
    public static function wd_remove_accents($str, $charset='utf-8')
    {
    	$str = htmlentities($str, ENT_NOQUOTES, $charset);

    	$str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    	$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    	$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

    	return $str;
    }

    /**
     * Méthode pour le traitement des noms et des prénoms
     * @author francois.espinet
     * @param string $string
     * @return string
     */
    public static function traitementNomPropres($string) {
        return strtolower(self::wd_remove_accents($string));
    }
}