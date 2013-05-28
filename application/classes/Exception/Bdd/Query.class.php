<?php

/**
 * Exception lancée en cas de problème dans l'execution requête.
 *
 * @author francois.espinet
 *
 */
class Exception_Bdd_Query extends Exception_Bdd
{

    const Level_Blocker = 1;
    const Level_Critical = 2;
    const Level_Major = 4;
    const Level_Minor = 8;

    const Currupt_Params = 100;

    public function __destruct()
    {
        if ($this->getCode() < 100 && $this->getCode() <= self::Level_Critical) {
            try {
                $mail = new Mail_AdminTech();
                $mail->warning("Un problème est survenu avec une requête dans la base de données : " . $this->getMessage());
            } catch (Exception_Mail $e) {
                $e->log();
            }
        }
        parent::__destruct();
    }
}
