<?php
require_once(APPLICATION_PATH.'/inc/fkz_auth.php');
$parametres = Registry::get('parametres');
$db = Registry::get('db');
$stat = new Statistiques($db);
// Identification
if (! (isset($_SESSION['administrateur']) && $_SESSION['administrateur'] === true)) {
	frankiz_do_auth('/administration/demandes');
	return;
}

//detection du post
if (isset($_POST['destinataire']) && isset($_POST['sujet']) && isset($_POST['corps'])) {
    if ($_POST['sujet'] != '') {
        $failed_mails = array();
        try {
            if ($_POST['destinataire'] == 'x') {
                $mails = $stat->getMailsX();

            } elseif ($_POST['destinataire'] == 'admissibles' ) {
                $mails = $stat->getMailsAdmissibles();

            } else {
                return;
            }
            foreach ($mails as $personne) {
            	try {
            		$mail = new Mail_Sondage($personne['MAIL'], $personne['NOM'], $personne['PRENOM']);
            		$mail->sondage($_POST['sujet'], $_POST['corps']);
            	} catch (Exception_Mail $e) {
            		$failed_mails[] = '<span class="message error">'.$email.'</span>';
            	}
            }

            if (count($failed_mails) > 0) {
            	echo join(' ', $failed_mails);
            } else {
            	echo '<span class="ok">Mail envoyés avec succés</span>';
            }
            Registry::get('layout')->disableLayout();
            return;
        } catch (Exception_Bdd $e) {
            echo '<span class="error">Impossible de récupérer les emails des destinataires.</span>';
        }
    } else {
        Registry::get('layout')->disableLayout();
        echo '<span class="error">Le sujet est vide</span>';
    }
} else {
    Registry::get('layout')->addMessage('Pas de post!', MSG_LEVEL_ERROR);
}
return;