<?php

/**
 * Exception lancée en cas de problème dans l'execution requête.
 * @author francois.espinet
 *
 */
class Exception_Bdd_Integrity extends Exception_Bdd
{
    const Duplicate_Entry    = 1;
}