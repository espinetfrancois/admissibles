<?php
/**
 * Connexion a la BDD
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */
 
$config = $_SESSION['config'];
try {
    $db     = new PDO('mysql:host='.$config->get_dbhost().';dbname='.$config->get_dbbase(),$config->get_dblogin(),$config->get_dbpass());
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
        Logs::logger(3, "Connexion a la base de donnees echouee : ".$e->getMessage());
}

$parametres = new Parametres($db);
