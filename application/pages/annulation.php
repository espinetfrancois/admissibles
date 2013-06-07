<?php
/**
 * Page d'annulation d'une demande faite par un admissible
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

$demandeManager = new Manager_Demande(Registry::get('db'));
$eleveManager = new Manager_Eleve(Registry::get('db'));

if (isset($_GET['code']) && preg_match('#^[0-9a-f]{32}$#i', $_GET['code']) == 1) {
    try {
        $demande = $demandeManager->getUnique($_GET['code']);
        if ($demande->status() != 3) {
        	try {
        		$demandeManager->updateStatus($_GET['code'], '3');
        		$eleveManager->addDispo($demande->userEleve(), $demande->serie());
        	} catch (Exception_Bdd $e) {
        		Registry::get('layout')->addMessage('Impossible de mettre à jour votre demande dans la base de données.', MSG_LEVEL_ERROR);
        	}
        	//demande confirmée par l'admissible au moins
        	if ($demande->status() != 0) {

        		try {
        			//préparation de l'envoi du mail : récupération des informations de l'X
        			$elevem = new Manager_Eleve(Registry::get('db'));
        			$eleve = $elevem->getUnique($demande->userEleve());

        			$mail = new Mail_X($eleve->email());
        			// Envoi d'un mail à l'X lui indiquant l'annulation de la demande
        			$mail->demandeAnnulee();
        		} catch (Exception_Mail $e) {
        			Registry::get('layout')->addMessage('Impossible d\'envoyer le mail à l\'élève correspondant.', MSG_LEVEL_ERROR);
        		} catch (Exception_Bdd $e) {
        			Registry::get('layout')->addMessage("Impossible de récupérer les informations de l'élève dans la base de donées", MSG_LEVEL_ERROR);
        		}
        	}
        	echo '<h2>Demande d\'hébergement chez un élève pendant la période des oraux</h2>
             <p>Votre demande a bien été <span class="emph">annulée</span>.<br/>
             Vous pouvez désormais créer une nouvelle demande sur <a href=\'/demande\'>la page suivante</a>
             </p>';
        	Logs::logger(1, 'Annulation d\'une demande de logement (id : '.$demande->id().')');
        } else {
        	echo '<p>Cette demande a déjà été annulée</p>';
        	Logs::logger(2, 'Re-annulation d\'une demande de logement (id : '.$demande->id().')');
        }
    } catch (Exception_Bdd $e) {
        Registry::get('layout')->addMessage('Impossible de récupérer votre demande dans la base.', MSG_LEVEL_ERROR);
    }

} else {
    throw new Exception_Page('Corruption des parametres. validation.php::GET', 'Le code proposé n\'est pas valide');
//     Logs::logger(3, 'Corruption des parametres. annulation.php::GET');
}