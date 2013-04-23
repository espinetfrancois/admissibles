<?php

class Exception_Bdd extends Exception_Projet
{
    const Bdd_Unreachable = 64;

    protected $log_file = "bdd.log";

    public function __destruct() {
        $this->log();
    }

    public function log() {
        parent::log();
        error_log($this->getPrevious()."\n\n\n", 3, LOGS_PATH.'/'.$this->log_file);
    }

}