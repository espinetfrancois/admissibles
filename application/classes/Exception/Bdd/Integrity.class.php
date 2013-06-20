<?php

/**
 * Exception lancée en cas de constatation d'un problème d'intégrité dans la base de donnée.
 * Cette exception indique que la base de donnée ne devrait pas se trouver dans cet état.
 * Elle déclenche l'envoi d'un mail aux administrateurs techniques pour qu'ils puissent réparer la base de donnée.
 *
 * @author francois.espinet
 * @version 1.1
 * @package Exception
 */
class Exception_Bdd_Integrity extends Exception_Bdd
{

    const Duplicate_Entry = 1;

    public function __destruct()
    {
        try {
            $mail = new Mail_AdminTech();
            $mail->warning("La base de donnée doit être réparée : " . $this->getMessage());
        } catch (Exception_Mail $e) {
            $e->log();
        }
        parent::__destruct();
    }
}
