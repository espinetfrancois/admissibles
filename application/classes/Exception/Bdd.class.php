<?php

/**
 * Exception lancées lors des appels à la base de données.
 *
 * @author francois.espinet
 *
 */
class Exception_Bdd extends Exception_Projet
{

    const Bdd_Unreachable = 64;

    protected $log_file = "bdd.log";

    public function __destruct()
    {
        $this->log();
    }

    public function log()
    {
        parent::log();
        if ($this->getPrevious() != null)
            error_log($this->getPrevious()->getMessage() . "\n\n\n", 3, LOGS_PATH . '/' . $this->log_file);
    }

}
