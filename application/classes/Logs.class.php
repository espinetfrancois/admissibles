<?php
/**
 * Librairie de fonctions statiques
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

class Logs {

    /**
     * Chemin d'accès au fichier log
     * @var string
     */
    const Log_Path = '/application.log';

    /**
     * Méthode de sauvegarde des fichiers log
     * @access public
     * @param int $level
     * @param string $message
     * @return void
     */

    public static  function logger($level, $message)
    {
        switch ($level) {
        case 1:
            $niveau = 'Normal     ';
            break;
        case 2:
            $niveau = 'Warning    ';
            break;
        case 3:
            $niveau = 'Fatal error';
            // Envoi d'un mail a l'admin -> erreur ou tentative de piratage
            $mail = new Mail_AdminTech();
            $mail->fatalError($message);
            break;
        }
        $texte = $niveau.' ['.date('Y-m-d H:i:s', time()).'] '.$_SERVER['REMOTE_ADDR'].' - '.$message."\n";
        error_log($texte, 3, LOGS_PATH.self::Log_Path);
        if ($level == 3) {
            session_destroy();
            die('Session compromise');
        }

    }

}
