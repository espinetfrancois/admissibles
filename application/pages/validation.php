<?php
/**
 * Page de validation d'une demande faite par un admissible
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo envoi mail
 */
include_once(APPLICATION_PATH.'/inc/sql.php');

$demandeManager = new DemandeManager($db);

if (isset($_GET['code']) && preg_match('#^[0-9a-f]{32}$#i', $_GET['code'])) {
    echo '<h2>Demande d\'hébergement chez un élève pendant la période des oraux</h2>';
    $demande = $demandeManager->getUnique($_GET['code']);
	if ($demande->status() == 0) {
        $demandeManager->updateStatus($_GET['code'], '1');
		// Envoi d'un mail à l'X correspondant lui indiquant une demande à accepter sur son espace
   		echo '<p>Votre adresse email a bien été <strong>validée</strong>.<br/>';
   		echo 'Vous recevrez un email de confirmation lorsque l\'élève que vous avez contacté acceptera votre demande.<br/><br/>';
    	echo 'Si l\'élève semble ne pas répondre dans le temps imparti, merci d\'annuler votre demande (voir l\'email précedemment reçu) afin d\'en faire une nouvelle...</p>';
    	Logs::logger(1, 'Validation d\'une demande de logement (id : '.$demande->id().')');
	} else {
		echo 'Cette demande a déjà été validée';
		Logs::logger(2, 'Re-validation d\'une demande de logement (id : '.$demande->id().')');
	}
} else {
    Logs::logger(3, 'Corruption des parametres. validation.php::GET');
}
?>