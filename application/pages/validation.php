<?php
/**
 * Page de validation d'une demande faite par un admissible
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo logs
 * @todo envoi mail
 */

require_once(APPLICATION_PATH.'/inc/autoload.php');

$demandeManager = new DemandeManager($db);

if (isset($_GET['code']) && preg_match("#^[0-9a-f]{32}$#i",$_GET['code'])) {
    $demande = $demandeManager->getUnique($_GET['code']);
    $demande->$demandeManager->updateStatus($_GET['code'], "1");
    // Envoi d'un mail � l'X correspondant lui indiquant une demande � accepter sur son espace
    echo "<h2>Demande d'h�bergement chez un �l�ve pendant la p�riode des oraux</h2>";
    echo "<p>Votre adresse email a bien �t� <strong>valid�e</strong>.<br/>";
    echo "Vous recevrez un email de confirmation lorsque l'�l�ve que vous avez contact� acceptera votre demande.<br/><br/>";
    echo "Si l'�l�ve semble ne pas r�pondre dans le temps imparti, merci d'annuler votre demande (voir l'email pr�cedemment re�u) afin d'en faire une nouvelle...</p>";
} else {
    throw new RuntimeException('Erreur dans le processus de demande'); // Ne se produit jamais en ex�cution courante
}
?>