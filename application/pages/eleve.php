<?php
/**
 * Page de gestion des Eleves X
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo gestion du mail d'acceptation
 */

// require_once(APPLICATION_PATH.'/inc/sql.php');
require_once(APPLICATION_PATH.'/inc/fkz_auth.php');

$eleveManager = new EleveManager(Registry::get('db'));
$demandeManager = new DemandeManager(Registry::get('db'));
$adresseManager = new AdresseManager(Registry::get('db'));
$parametres = Registry::get('parametres');

// Identification
if (!isset($_SESSION['eleve'])) {
    frankiz_do_auth("/x/donnees-personnelles");
}
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
    Logs::logger(1, 'Acceptation d\'une demande de logement (user : '.$_SESSION['eleve']->user().')');
}

// Propostion d'une adresse
if (isset($_SESSION['eleve']) && isset($_POST['adr_nom'])) {
    $adresse = new Adresse(array('nom' => $_POST['adr_nom'],
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

// Interface Elève
    if ((isset($_GET['action']) && $_GET['action'] == 'modify') || !$_SESSION['eleve']->isValid()) { // on teste si l'élève a déjà entré ses infos personnelles
        $prepas = $parametres->getList(Parametres::Etablissement);
        $filieres = $parametres->getList(Parametres::Filiere);
        ?>

        <h2>Modifier mes informations personnelles</h2>
        <p>Merci de renseigner les informations qui permettront aux admissibles de vous identifier :</p>
        <form action="/x/connexion" method="post">
        <p id="champ-sexe" class="champ"><label for="sexe">Sexe</label> <label>: M <input type="radio" name="sexe" value="M"
    <?php
        if ($_SESSION['eleve']->sexe() == "M" || $_SESSION['eleve']->sexe() == "") {
            echo 'checked="checked"';
        }?>/></label> <label>/ F</label><input type="radio" name="sexe" value="F"
        <?php
        if ($_SESSION['eleve']->sexe() == "F") {
            echo 'checked="checked"';
        }?>/>
        <?php if (isset($erreurs) && in_array(Eleve::Sexe_Invalide, $erreurs)) echo '<span style="color:red;">Merci de renseigner ce champ</span>'; ?>
        </p>
        <p id="champ-prepa" class="champ">
        <label for="prepa">Etablissement d'origine : </label><select name="prepa">
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
        <?php if (isset($erreurs) && in_array(Eleve::Prepa_Invalide, $erreurs)) echo '<span style="color:red;">Merci de renseigner ce champ</span>'; ?>
        </p>
        <p id="champ-filiere" class="champ"> <label for="filiere">Filière : </label><select name="filiere">
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
        <?php if (isset($erreurs) && in_array(Eleve::Filiere_Invalide, $erreurs)) echo '<span style="color:red;">Merci de renseigner ce champ</span>'; ?>
        </p>
        <br/>
        <input type="submit" value="Modifier mes informations personnelles"/>
        </form>
        <?php
    } else {
        $series = $parametres->getList(Parametres::Serie);
        $dispos = $eleveManager->getDispo($_SESSION['eleve']->user());
        ?>
        <h2>Disponibilité d'accueil</h2>
        <p>Bienvenue <?php echo $_SESSION['eleve']->user(); ?></p>
        <a href="/deconnexion">Se déconnecter</a> -- <a href="/x/connexion?&action=modify">Modifier mes informations personnelles</a>
        <hr/>
        <?php
        if (!empty($series)) {
            ?>
            <h3>Gestion de vos disponibilités</h3>
            <p>Cochez ci-dessous les semaines pour lesquelles vous êtes disposés à accueillir un admissible.</p>
            <p>Vous pourrez modifier vos choix jusqu'à la publication des listes d'admissibilité de chaque série.</p>
            <p>Dès lors, vous serez tenus d'héberger tout admissible vous contactant via cette interface : la validation de ce formulaire tient lieu d'engagement vis à vis de l'admissible qui fera sa demande.
            <span style="color:red;">Pensez donc à venir vous désinscrire dans les temps si vous n'êtes plus disponible !</span></p>
            <form action="/x/connexion" method="post">
            <input type="hidden" name="serie" value="1"/>
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
            echo '<p>Vous n\'avez reçu aucune demande jusqu\'à présent. Vous recevrez une alerte email pour toute demande à valider...</p>';
        } else {
            echo '<p>Ci-dessous sont listées toutes les demandes que vous avez reçues. Vérifiez selon leur statut quelle action vous devez faire.</p>';
            echo '<p>Vous devez obligatoirement valider les demandes reçues. Si vous ne pouvez tenir votre engagement,
                    vous devez accepter la demande et prendre contact avec l\'admissible pour lui trouver un hébergement de substitution sur le platal.
                Par esprit de solidarité, merci de ne pas laisser sans logement un admissible alors que vous vous étiez engagés pour cette période...</p>';
            echo '<table border=1 cellspacing=0>';
            echo '<tr>
                      <td>Nom</td>
                      <td>Prérom</td>
                      <td>Sexe</td>
                      <td>Etablissement</td>
                      <td>Filière</td>
                      <td>Série</td>
                      <td>Statut</td>
                      <td>Action à mener</td>
                  </tr>';
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
            echo '</table>';
        }
        ?>
        <hr/>
        <h3>Proposer un hébergement :</h3>
        <p>Vous avez dormi à proximité de l'école durant vos oraux de concours ?<br/>
        N'hésitez pas à partager avec les futurs admissibles les adresses qui vous ont aidées !</p>
        <?php
        if (isset($successAjoutAdresse)) {
            echo '<p style="color:red;">Votre annonce a bien été prise en compte. Elle sera examinée par un administrateur avant d\'être publiée sur le site du concours.</p>';
        }
        $categories = $adresseManager->getCategories();
        ?>
        <form action="/x/connexion" method="post">
        <p class="champ" id="champ-adr_nom"><label for="adr_nom"> Nom : </label><input type="text" name="adr_nom" value="<?php if (isset($adresse)) { echo $adresse->nom(); } ?>"/> <?php if (isset($erreurAjoutAdresse) && in_array(Adresse::Nom_Invalide, $erreurAjoutAdresse)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/>
        <p class="champ" id="champ-adr_adresse"><label for="adr_adresse"> Adresse : </label><input type="text" name="adr_adresse" value="<?php if (isset($adresse)) { echo $adresse->adresse(); } ?>"/> <?php if (isset($erreurAjoutAdresse) && in_array(Adresse::Adresse_Invalide, $erreurAjoutAdresse)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/>
        <p class="champ" id="champ-adr_tel"><label for="adr_tel">Téléphone : </label><input type="text" name="adr_tel" value="<?php if (isset($adresse)) { echo $adresse->tel(); } ?>"/> <?php if (isset($erreurAjoutAdresse) && in_array(Adresse::Tel_Invalide, $erreurAjoutAdresse)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/>
        <p class="champ" id="champ-adr_email"><label for="adr_email">Email : </label><input type="text" name="adr_email" value="<?php if (isset($adresse)) { echo $adresse->email(); } ?>"/> <?php if (isset($erreurAjoutAdresse) && in_array(Adresse::Email_Invalide, $erreurAjoutAdresse)) echo '<span style="color:red;">Champ invalide</span>'; ?></p>
        <p class="champ" id="champ-adr_description"><label for="adr_description">Description : </label><?php if (isset($erreurAjoutAdresse) && in_array(Adresse::Description_Invalide, $erreurAjoutAdresse)) echo '<span style="color:red;">Champ invalide</span>'; ?>
        <textarea name="adr_description" cols="20" rows="4"><?php if (isset($adresse)) { echo $adresse->description(); } ?></textarea><p/>
        <p class="champ" id="champ-adr_categorie"><label for="adr_categorie">Catégorie : </label><select name="adr_categorie">
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
        </select> <?php if (isset($erreurAjoutAdresse) && in_array(Adresse::Categorie_Invalide, $erreurAjoutAdresse)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/><br/>
        <input type="submit" value="Proposer cet établissement" />
        </form>
        <?php
    }
?>
<span id="page_id">3</span>
