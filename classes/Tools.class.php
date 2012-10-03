<?php
/**
 * Librairie de fonctions statiques
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 0.5
 *
 */

class Tools {

    /**
     * Chemin d'accès au fichier log
     */
    const LOG_PATH = "../log.txt";


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
        $message = "[".date('Y-m-d H:i:s', time())."] ".$_SERVER['REMOTE_ADDR']." - ".$erreur."\n";
        error_log($message, 3, self::LOG_PATH);
    }

}
?>