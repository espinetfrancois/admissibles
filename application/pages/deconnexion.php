<?php
/**
 * Page de déconnexion
 * @author Nicolas GROROD
 * @version 1.0
 *
 */
if ($_SESSION["administrateur"] !== true) {
    Logs::logger(1, 'Deconnexion administrateur (user : '.$_SESSION['eleve']->user().')');
} else {
    Logs::logger(1, 'Deconnexion eleve (user : '.$_SESSION['eleve']->user().')');
}
session_destroy();
header('Location:'."/");
exit();
