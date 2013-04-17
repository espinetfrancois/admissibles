<?php

/**
 * Exception lancée en cas de problème dans l'execution requête.
 * @author francois.espinet
 *
 */
class Exception_Bdd_Query extends Exception_Bdd
{

    const Level_Blocker  = 1;
    const Level_Critical = 2;
    const Level_Major    = 4;
    const Level_Minor    = 8;

    const Currupt_Params = 100;
}