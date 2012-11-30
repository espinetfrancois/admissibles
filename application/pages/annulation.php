<?php
/**
 * Page d'annulation d'une demande faite par un admissible
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo envoi mail
 */
require_once(APPLICATION_PATH.'/inc/sql.php');

$demandeManager = new DemandeManager($db);
$eleveManager = new EleveManager($db);

if (isset($_GET['code']) && preg_match('#^[0-9a-f]{32}$#i', $_GET['code'])) {
    $demande = $demandeManager->getUnique($_GET['code']);
    if ($demande->status() != 3) {
        $demandeManager->updateStatus($_GET['code'], '3');
        $eleveManager->addDispo($demande->userEleve(), $demande->serie());
        // Envoi d'un mail à l'X lui indiquant l'annulation de la demande
        echo '<h2>Demande d\'hébergement chez un élève pendant la période des oraux</h2>';
        echo '<p>Votre demande a bien été <strong>annulée</strong>.<br/>';
        echo 'Vous pouvez désormais créer une nouvelle demande sur la page <a href=\'demande\'>suivante</a></p>';
        Logs::logger(1, 'Annulation d\'une demande de logement (id : '.$demande->id().')');
    } else {
        echo 'Cette demande a déjà été annulée';
        Logs::logger(2, 'Re-annulation d\'une demande de logement (id : '.$demande->id().')');
    }
} else {
    Logs::logger(3, 'Corruption des parametres. annulation.php::GET');
}