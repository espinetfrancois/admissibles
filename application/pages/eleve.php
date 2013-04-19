<?php
/**
 * Page de gestion des Eleves X
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

require_once(APPLICATION_PATH.'/inc/fkz_auth.php');

$eleveManager = new Manager_Eleve(Registry::get('db'));
$demandeManager = new Manager_Demande(Registry::get('db'));
$adresseManager = new Manager_Adresse(Registry::get('db'));
$parametres = Registry::get('parametres');

// Identification
if (!isset($_SESSION['eleve'])) {
    frankiz_do_auth("/x/donnees-personnelles");
    return;
}

/**
 * Gestion des posts
 */
echo '<span id="page_id">3</span>';
//Modification des informations personnelles
if (isset($_SESSION['eleve']) && isset($_POST['sexe']) && isset($_POST['filiere']) && isset($_POST['prepa'])) {
    $_SESSION['eleve']->setErreurs();
    $_SESSION['eleve']->setPrepa($_POST['prepa']);
    $_SESSION['eleve']->setSexe($_POST['sexe']);
    $_SESSION['eleve']->setFiliere($_POST['filiere']);
    if ($_SESSION['eleve']->isValid()) {
        if (isset($_SESSION['new']) && $_SESSION['new'] == 1) {
            $eleveManager->add($_SESSION['eleve']);
            unset($_SESSION['new']);
        } else {
            $eleveManager->update($_SESSION['eleve']);
        }
        Logs::logger(1, 'Modification des informations personnelles eleve (user : '.$_SESSION['eleve']->user().')');
    } else {
        $erreurs = $_SESSION['eleve']->erreurs();
        Logs::logger(2, 'Erreur de remplissage du formulaire informations personnelles eleve (user : '.$_SESSION['eleve']->user().')');
    }
}

// Modification des disponibilités d'acceuil
if (isset($_SESSION['eleve']) && isset($_POST['serie']) && $_POST['serie'] == "1") {
    $series = $parametres->getList(Parametres::Serie);
    $dispo = array();
    foreach ($series as $value) {
        if ($value['ouverture'] > time()) {
            if (isset($_POST['serie'.$value['id']]) && $_POST['serie'.$value['id']]) {
                $eleveManager->addDispo($_SESSION['eleve']->user(), $value['id']);
            } else {
                $eleveManager->deleteDispo($_SESSION['eleve']->user(), $value['id']);
            }
        }
    }
    Logs::logger(1, 'Modification des disponibilites eleve (user : '.$_SESSION['eleve']->user().')');
}

// Acceptation d'une demande de logement
if (isset($_POST['accept']) && !empty($_POST['accept'])) {
    $demande = $demandeManager->getUnique($_POST['accept']);
    $demande->setCode($demandeManager->updateStatus($_POST['accept'], "2"));
    // envoi d'un mail de confirmation à l'admissible contenant un dernier lien d'annulation
    //préparation de l'envoi du mail : récupération des informations de l'X
    $elevem = new Manager_Eleve(Registry::get('config'));
    $eleve = $elevem->getUnique($demande->userEleve());
    $mail = new Mail_Admissible($demande->nom(), $demande->prenom(), $demande->email());
    $mail->demandeConfirmee($eleve->email(), "/admissible/annulation-demande?code=".$demande->code(), $demande->userEleve());

    Logs::logger(1, 'Acceptation d\'une demande de logement (user : '.$_SESSION['eleve']->user().')');
}

// Proposition d'une adresse
if (isset($_SESSION['eleve']) && isset($_POST['adr_nom'])) {
    $adresse = new Model_Adresse(array('nom' => $_POST['adr_nom'],
                                 'adresse' => $_POST['adr_adresse'],
                                 'tel' => $_POST['adr_tel'],
                                 'email' => $_POST['adr_email'],
                                 'description' => $_POST['adr_description'],
                                 'categorie' => $_POST['adr_categorie'],
                                 'valide' => "0"));
    if ($adresse->isValid()) {
        $adresseManager->save($adresse);
        unset($adresse);
        $successAjoutAdresse = 1;
        Logs::logger(1, 'Proposition d\'une adresse (user : '.$_SESSION['eleve']->user().')');
    } else {
        $erreurAjoutAdresse = $adresse->erreurs();
        Logs::logger(2, 'Erreur de remplissage du formulaire de proposition d\'une adresse (user : '.$_SESSION['eleve']->user().')');
    }
}

/**
 * Interface Elève
 */

// on teste si l'élève a déjà entré ses infos personnelles
if ((isset($_GET['action']) && $_GET['action'] == 'modify') || !$_SESSION['eleve']->isValid()) {
    $prepas = $parametres->getList(Parametres::Etablissement);
    $filieres = $parametres->getList(Parametres::Filiere);
    $champInvalide = '<span class="error">Merci de renseigner ce champ</span>'
    ?>
    <h2>Modifier mes informations personnelles</h2>
    <p>Merci de renseigner les informations qui permettront aux admissibles de vous identifier :</p>
    <form action="/x/espace-personnel" method="post">
    <p id="champ-sexe" class="champ radio">
        <label for="sexe">Sexe: </label>
        <label> Masculin <input type="radio" name="sexe" value="M"
        <?php
        if ($_SESSION['eleve']->sexe() == "M" || $_SESSION['eleve']->sexe() == "")
            echo 'checked="checked"';
        echo "/>";
        ?>
        </label> <label>Féminin<input type="radio" name="sexe" value="F"
        <?php
        if ($_SESSION['eleve']->sexe() == "F")
            echo 'checked="checked"';
        echo '/>';
        if (isset($erreurs) && in_array(Model_Eleve::Sexe_Invalide, $erreurs))
            echo '<span style="color:red;">Merci de renseigner ce champ</span>';
    ?>
        </label>
    </p>
    <p id="champ-prepa" class="champ">
        <label for="prepa">Etablissement d'origine : </label>
        <select name="prepa">
            <option value=""></option>
            <?php
            foreach ($prepas as $value) {
                if ($_SESSION['eleve']->prepa() == $value['id']) {
                    $selected = ' selected="selected"';
                } else {
                    $selected = '';
                }
                echo '<option value="'.$value['id'].'"'.$selected.'>'.$value['ville'].' - '.$value['nom'].'</option>';
            }
            ?>
        </select>
        <?php if (isset($erreurs) && in_array(Model_Eleve::Prepa_Invalide, $erreurs))
            echo $champInvalide;
        ?>
    </p>
    <p id="champ-filiere" class="champ">
        <label for="filiere">Filière : </label>
        <select name="filiere">
            <option value=""></option>
            <?php
            foreach ($filieres as $value) {
                if ($_SESSION['eleve']->filiere() == $value['id']) {
                    $selected = ' selected="selected"';
                } else {
                    $selected = '';
                }
                echo '<option value="'.$value['id'].'"'.$selected.'>'.$value['nom'].'</option>';
            }
            ?>
            </select>
        <?php if (isset($erreurs) && in_array(Model_Eleve::Filiere_Invalide, $erreurs)) echo $champInvalide; ?>
    </p>
    <br/>
    <input type="submit" value="Modifier mes informations personnelles"/>
    </form>
    <?php
    return;
}

//informations personnelles déjà rentrées, interface de gestion

$series = $parametres->getList(Parametres::Serie);
$dispos = $eleveManager->getDispo($_SESSION['eleve']->user());
?>
<h2>Disponibilité d'accueil</h2>
<p>Bienvenue <?php echo $_SESSION['eleve']->user(); ?></p>
<a href="/deconnexion">Se déconnecter</a> -- <a href="/x/donnees-personnelles?&action=modify">Modifier mes informations personnelles</a>
<hr/>
<?php
if (!empty($series)) {
    ?>
    <h3>Gestion de vos disponibilités</h3>
    <p>Cochez ci-dessous les semaines pour lesquelles vous êtes disposés à accueillir un admissible.</p>
    <p>Vous pourrez modifier vos choix jusqu'à la publication des listes d'admissibilité de chaque série.</p>
    <p>Dès lors, vous serez tenus d'héberger tout admissible vous contactant via cette interface : la validation de ce formulaire tient lieu d'engagement vis à vis de l'admissible qui fera sa demande.</p>
    <p><span class="emph-emph">Pensez donc à venir vous désinscrire dans les temps si vous n'êtes plus disponible !</span></p>

    <form action="/x/espace-personnel" method="post">
    <input type="hidden" name="serie" value="1"/>
    <p class="champ">
    <?php
    foreach ($series as $value) {
        if ($value['ouverture'] < time()) {
            $disabled = 'disabled';
            $name = '';
        } else {
            $disabled = '';
            $name = 'name="serie'.$value['id'].'"';
        }
        if (in_array($value['id'], $dispos)) {
            $checked = 'checked';
        } else {
            $checked = '';
        }
        echo $value['intitule'].' (du '.date('d.m.Y', $value['date_debut']).' au '.date('d.m.Y', $value['date_fin']).') : <input type="checkbox" '.$name.' '.$checked.' '.$disabled.'/><br/>';
    }
    ?>
    </p>
    <br/>
    <input type="submit" value="Modifier mes disponibilités d'accueil"/>
    </form>
    <?php
} else {
    echo '<p>Il n\'est pas encore possible de mettre à jour vos disponibilitées d\'hébergement. Merci de repasser plus tard...</p>';
}
?>
<hr/>

<h3>Récapitulatif de vos demandes :</h3>
<?php
$demandes = $demandeManager->getDemandes($_SESSION['eleve']->user());
if (empty($demandes)) {
    echo '<p>Vous n\'avez reçu aucune demande jusqu\'à présent.<br/>
          Vous recevrez une alerte email pour toute demande à valider...</p>';
} else {
    echo '<p>Ci-dessous sont listées toutes les demandes que vous avez reçues. Vérifiez selon leur statut quelle action vous devez faire.</p>';
    echo '<p>Vous devez obligatoirement valider les demandes reçues. Si vous ne pouvez tenir votre engagement,
            vous devez accepter la demande et prendre contact avec l\'admissible pour lui trouver un hébergement de substitution sur le platal.
        Par esprit de solidarité, merci de ne pas laisser sans logement un admissible alors que vous vous étiez engagés pour cette période...</p>';
    echo '<table border=1 cellspacing=0>';
    echo '<thead><tr>
              <th>Nom</th>
              <th>Prérom</th>
              <th>Sexe</th>
              <th>Etablissement</th>
              <th>Filière</th>
              <th>Série</th>
              <th>Statut</th>
              <th>Action à mener</th>
          </tr></thead><tbody>';
    foreach ($demandes as $demande) {
        switch ($demande->status()) {
        case 0:
            $status_libele = 'En cours de validation par l\'admissible';
            $action = 'Merci d\'attendre que l\'admissible ait vérifié son adresse email. Vous ne recevrez pas d\'autre demande que celle-ci pour cette série.';
            break;
        case 1:
            $status_libele = 'En attente d\'acceptation';
            $action = '<form action="/x/connexion" method="post"><input type="hidden" name="accept" value="'.$demande->code().'"><input type="submit" value="Accepter la demande"/></form>';
            break;
        case 2:
            $status_libele = 'Validée';
            $action = 'Prendre contact avec l\'admissible pour définir les modalités de son arrivée : '.$demande->email();
            break;
        case 3:
            $status_libele = 'Annulée';
            $action = 'Vous pouvez recevoir une autre demande pour cette série';
            break;
        default:
            Logs::logger(3, 'Corruption des parametres. eleve.php::statut');
               break;
        }

        echo '<tr>
                <td>'.$demande->nom().'</td>
                <td>'.$demande->prenom().'</td>
                <td>'.$demande->sexe().'</td>
                <td>'.$demande->prepa().'</td>
                <td>'.$demande->filiere().'</td>
                <td>'.$demande->serie().'</td>
                <td>'.$status_libele.'</td>
                <td>'.$action.'</td>
              </tr>';
    }
    echo '</tbody></table>';
}
?>
<hr/>
<h3>Proposer un hébergement :</h3>
<p>Vous avez dormi à proximité de l'école durant vos oraux de concours ?<br/>
N'hésitez pas à partager avec les futurs admissibles les adresses qui vous ont aidées !</p>
<?php
if (isset($successAjoutAdresse)) {
    Registry::get('layout')->addMessage('Votre annonce a bien été prise en compte. Elle sera examinée par un administrateur avant d\'être publiée sur le site du concours.', MSG_LEVEL_OK);
}
//interface d'ajout d'une adresse
$categories = $adresseManager->getCategories();
$champInvalide = '<span class="error">Champ invalide</span>';
?>
<form action="/x/espace-personnel" method="post">
    <p class="champ" id="champ-adr_nom">
        <label for="adr_nom">Nom : </label>
        <input type="text" name="adr_nom" value="<?php
                  echo ( isset($adresse) ? $adresse->nom() : '').'"';
                  echo ( (isset($erreurAjoutAdresse) && in_array(Model_Adresse::Nom_Invalide, $erreurAjoutAdresse)) ? 'class="error"/>'.$champInvalide : '/>'); ?>
    </p>
    <p class="champ" id="champ-adr_adresse">
        <label for="adr_adresse">Adresse : </label>
        <input type="text" name="adr_adresse" value="<?php
                  echo ( isset($adresse) ? $adresse->adresse() : '').'"';
                  echo ( (isset($erreurAjoutAdresse) && in_array(Model_Adresse::Adresse_Invalide, $erreurAjoutAdresse)) ? 'class="error"/>'.$champInvalide : '/>');?>
    </p>
    <p class="champ" id="champ-adr_tel">
        <label for="adr_tel">Téléphone : </label>
        <input type="text" name="adr_tel" value="<?php
                  echo ( isset($adresse) ? $adresse->tel() : '').'"';
                  echo ( (isset($erreurAjoutAdresse) && in_array(Model_Adresse::Tel_Invalide, $erreurAjoutAdresse)) ? 'class="error"/>'.$champInvalide : '/>');?>
    </p>
    <p class="champ" id="champ-adr_email">
        <label for="adr_email">Email : </label>
        <input type="text" name="adr_email" value="<?php
                  echo ( isset($adresse) ? $adresse->email() : '').'"';
                  echo ( (isset($erreurAjoutAdresse) && in_array(Model_Adresse::Email_Invalide, $erreurAjoutAdresse)) ? 'class="error"/>'.$champInvalide : '/>');?>
    </p>
    <p class="champ" id="champ-adr_description">
        <label for="adr_description">Description : </label>
        <textarea name="adr_description" cols="20" rows="4"<?php
            echo ( (isset($erreurAjoutAdresse) && in_array(Model_Adresse::Description_Invalide, $erreurAjoutAdresse)) ? ' class="error">' : '>');
            if (isset($adresse)) { echo $adresse->description(); } ?>
        </textarea>
        <?php if (isset($erreurAjoutAdresse) && in_array(Model_Adresse::Description_Invalide, $erreurAjoutAdresse)) echo $champInvalide; ?>
    </p>
    <p class="champ" id="champ-adr_categorie">
        <label for="adr_categorie">Catégorie : </label>
        <select name="adr_categorie">
            <option value=""></option>
            <?php
            foreach ($categories as $value) {
                if (isset($adresse) && $adresse->categorie() == $value['id']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                echo '<option value="'.$value['id'].'" '.$selected.'>'.$value['nom'].'</option>';
            }
            ?>
        </select>
        <?php if (isset($erreurAjoutAdresse) && in_array(Model_Adresse::Categorie_Invalide, $erreurAjoutAdresse)) echo $champInvalide; ?>
    </p>
    <br/>
    <input type="submit" value="Proposer cet établissement" />
</form>