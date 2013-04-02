<?php
/**
 * Connexion a la BDD
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

// $config = $_SESSION['config'];
require_once('autoload.php');
$config = Registry::get('config');

try {
    $db     = new PDO('mysql:host='.$config->getDbhost().';dbname='.$config->getDbbase(),$config->getDblogin(),$config->getDbpass());
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    Registry::set('db', $db);
    Registry::set('parametres', new Parametres($db));
} catch (PDOException $e) {
    throw new Exception_Bdd("Impossible de se connecter à la base de données", Exception_Bdd::Bdd_Unreachable,$e);
} catch (Exception $e) {
    throw new Exception_Bdd("Un problème est survenue lors de la tentative de connexion à la base de données", null, $e);
}