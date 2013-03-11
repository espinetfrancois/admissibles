<?php
require_once(APPLICATION_PATH.'/inc/sql.php');
require_once(APPLICATION_PATH.'/inc/fkz_auth.php');

$eleveManager = new EleveManager($db);

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (isset($_GET['response'])) {
    $auth = frankiz_get_response();
    $_SESSION['eleve'] = $eleveManager->getUnique($auth['hruid']);
    if ($_SESSION['eleve'] == NULL) {
        $_SESSION['new'] = 1; // Première connexion de l'élève
        $_SESSION['eleve'] = $eleve = new Eleve(
                array('user' => $auth['hruid'], 'email' => $auth['email'], 'promo' => $auth['promo'], 'section' => $auth['sport'])); //***
    }
    Logs::logger(1, 'Connexion de l\'eleve ' . $auth['hruid'] . ' reussie');
    if (in_array('admin',$auth['rights']['admissibles'])) {
        $_SESSION['administrateur'] = true;
        Logs::logger(1, 'Connexion a l\'interface d\'administration reussie');
    }
    header("Location:" .$auth['location'], true);
    exit();
} else {
    frankiz_do_auth();
}
