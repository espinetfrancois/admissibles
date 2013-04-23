<?php
/**
 * Page de validation d'une demande faite par un admissible
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

$demandeManager = new Manager_Demande(Registry::get('db'));

if (isset($_GET['code']) && preg_match('#^[0-9a-f]{32}$#i', $_GET['code'])) {
    echo '<h2>Demande d\'hébergement chez un élève pendant la période des oraux</h2>';
    try {
        $demande = $demandeManager->getUnique($_GET['code']);
    } catch (Exception_Bdd $e) {
        Registry::get('layout')->addMessage('Impossible de récupérer votre demande dans la base.', MSG_LEVEL_ERROR);
    }
    if ($demande->status() == 0) {
        try {
            $demandeManager->updateStatus($_GET['code'], '1');
        } catch (Exception_Bdd $e) {
            //rethrow
            Registry::get('layout')->addMessage("Impossible de mettre à jour l'état de votre demande.", MSG_LEVEL_ERROR);
        }
        //préparation de l'envoi du mail : récupération des informations de l'X
        $elevem = new Manager_Eleve(Registry::get('config'));
        try {
            $eleve = $elevem->getUnique($demande->userEleve());

            $mail = new Mail_X($eleve->email());
            //envoi du mail d'avertissement à l'X
            $mail->nouvelleDemande();
        } catch (Exception_Mail $e) {
            Registry::get('layout')->addMessage("Impossible d'envoyer le mail d'annulation à l'élève", MSG_LEVEL_ERROR);
        } catch (Exception_Bdd $e) {
            Registry::get('layout')->addMessage("Impossible de retrouver l'élève dans la base de données.", MSG_LEVEL_ERROR);
        }

        //eventuellement envoyer un mail de confirmation à l'admissible
           echo '<p>Votre adresse email a bien été <strong>validée</strong>.<br/>';
           echo 'Vous recevrez un email de confirmation lorsque l\'élève que vous avez contacté acceptera votre demande.<br/><br/>';
        echo 'Si l\'élève semble ne pas répondre dans le temps imparti, merci d\'annuler votre demande (lien l\'email précedemment reçu) afin d\'en faire une nouvelle...</p>';
        Logs::logger(1, 'Validation d\'une demande de logement (id : '.$demande->id().')');
    } else {
        echo 'Cette demande a déjà été validée';
        Logs::logger(2, 'Re-validation d\'une demande de logement (id : '.$demande->id().')');
    }
} else {
    throw new Exception_Page('Corruption des parametres. validation.php::GET', 'Le code proposé n\'est pas valide');
//     Logs::logger(3, 'Corruption des parametres. validation.php::GET');
}
?>