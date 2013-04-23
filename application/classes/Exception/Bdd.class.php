<?php

class Exception_Bdd extends Exception_Projet
{
    const Bdd_Unreachable = 64;

    protected $log_file = "bdd.log";

    public function __destruct() {
        $this->log();
    }


}