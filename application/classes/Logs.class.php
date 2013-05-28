<?php

/**
 * Librairie de fonctions statiques.
 *
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 */
class Logs
{

    /**
     * Chemin d'accès au fichier log
     * @var string
     */
    const Log_Path = '/application.log';

    /**
     * Méthode de sauvegarde des fichiers log.
     *
     * @access public
     * @param integer    $level   le niveau de log entre 1 et 3
     * @param string $message le message à logger
     */
    public static function logger($level, $message)
    {
        switch ($level) {
            case 1:
                $niveau = 'Normal      : ';
                break;

            case 2:
                $niveau = 'Warning     : ';
                break;

            case 3:
                $niveau = 'Fatal error : ';
                break;

        }
        $texte = $niveau . ' [' . date('Y-m-d H:i:s', time()) . '] ' . $_SERVER['REMOTE_ADDR'] . ' - ' . htmlentities($message) . "\n";
        error_log($texte, 3, LOGS_PATH . self::Log_Path);
        if ($level == 3) {
            // Envoi d'un mail a l'admin -> erreur ou tentative de piratage
            $mail = new Mail_AdminTech();
            try {
                $mail->fatalError($message);
            } catch (Exception_Mail $e) {
                $e->log();
            }
            session_destroy();
            die("L'application s'est brutalement stoppée suite à une action illicite.");
        }
    }

}
