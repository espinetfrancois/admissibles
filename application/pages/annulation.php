<?php
/**
 * Page d'annulation d'une demande faite par un admissible
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo logs
 * @todo envoi mail
 */

include_once(APPLICATION_PATH.'/inc/sql.php');

$demandeManager = new DemandeManager($db);

if (isset($_GET['code']) && preg_match("#^[0-9a-f]{32}$#i",$_GET['code'])) {
    $demande = $demandeManager->getUnique($_GET['code']);
    $demande->$demandeManager->updateStatus($_GET['code'], "3");
    // Envoi d'un mail � l'X lui indiquant l'annulation de la demande
    echo "<h2>Demande d'h�bergement chez un �l�ve pendant la p�riode des oraux</h2>";
    echo "<p>Votre demande a bien �t� <strong>annul�e</strong>.<br/>";
    echo "Vous pouvez d�sormais cr�er une nouvelle demande sur la page <a href='index.php?page=admissible'>suivante</a></p>";
} else {
    throw new RuntimeException('Erreur dans le processus de demande'); // Ne se produit jamais en ex�cution courante
}