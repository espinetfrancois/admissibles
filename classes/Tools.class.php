<?php
/**
 * Librairie de fonctions statiques
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 0
 *
 */

class Tools {

    /**
     * Méthode d'envoi de mails formattés
     * @access public
     * @param string $adresse 
     * @param string $sujet 
     * @param string $message 
     * @return bool
     */

    public static  function sendMail($adresse, $sujet, $message) {
        $headers ='From: "Hébergement Polytechnique"<>'."\n";
         $headers .='Reply-To: '."\n";
         $headers .='Content-Type: text/plain; charset="iso-8859-1"'."\n";
         $headers .='Content-Transfer-Encoding: 8bit';
        
        //formatage à ajouter
        
        return mail($adresse, $sujet, $message, $headers);
    }


    /**
     * Méthode de sauvegarde des fichiers log
     * @access public
     * @param int $level 
     * @param string $erreur 
     * @return void
     */

    public static  function log($level, $erreur) {

    }


}
?>