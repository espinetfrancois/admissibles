<?php
/**
 * Page de gestion des Eleves X
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo identification LDAP
 * @todo proposition d'une bonne adresse
 * @todo gestion du mail d'acceptation
 * @todo logs
 * @todo gerer le problème de db
 */

include_once(APPLICATION_PATH.'/inc/sql.php');

$eleveManager = new EleveManager($db);
$demandeManager = new DemandeManager($db);

// Identification
if (isset($_POST['user']) && isset($_POST['pass']) && !empty($_POST['user']) && !empty($_POST['pass']))
{
    if (true) { // identification LDAP ***
        $_SESSION['eleve'] = $eleveManager->getUnique($_POST['user']);
        if ($_SESSION['eleve'] == NULL) {
            $_SESSION['new'] = 1; // Première connexion de l'élève
            $_SESSION['eleve'] = new Eleve(array("user" => $_POST['user'], "email" => "LDAP@poly.edu")); //***
        }
    }
    else {
        $erreurID = true;
    }
}
//Modification des informations personnelles
if (isset($_SESSION['eleve']) && isset($_POST['sexe']) && isset($_POST['promo']) && isset($_POST['section']) && isset($_POST['filiere']) && isset($_POST['prepa'])) {
    $_SESSION['eleve']->setErreurs();
    $_SESSION['eleve']->setPrepa($_POST['prepa']);
    $_SESSION['eleve']->setSexe($_POST['sexe']);
    $_SESSION['eleve']->setPromo($_POST['promo']);
    $_SESSION['eleve']->setSection($_POST['section']);
    $_SESSION['eleve']->setFiliere($_POST['filiere']);
    if ($_SESSION['eleve']->isValid()) {
        if (isset($_SESSION['new']) && $_SESSION['new'] == 1) {
            $eleveManager->add($_SESSION['eleve']);
            unset($_SESSION['new']);
        } else {
            $eleveManager->update($_SESSION['eleve']);
        }
    } else {
        $erreurs = $_SESSION['eleve']->erreurs();
    }
}
// Modification des disponibilitét d'acceuil
if (isset($_SESSION['eleve']) && isset($_POST['serie']) && $_POST['serie'] == "1") {
    $series = $parametres->getList(Parametres::SERIE);
    $dispo = array();
    foreach ($series as $value) {
		if ($value['ouverture'] > time()) {
        	if (isset($_POST["serie".$value['id']])) {
                $eleveManager->addDispo($_SESSION['eleve']->user(), $value['id']);
            } else {
				$eleveManager->deleteDispo($_SESSION['eleve']->user(), $value['id']);
			}
        }
    }
}
// Acceptation d'une demande de logement
if (isset($_POST['code']) && !empty($_POST['code'])) {
	$demande = $demandeManager->getUnique($_POST['code']);
	$demande->setCode($demandeManager->updateStatus($_POST['code'], "2"));
	// envoi d'un mail de confirmation à l'admissible contenant un dernier lien d'annulation
}

// Interface de connexion
if (!isset($_SESSION['eleve']) || (isset($_GET['action']) && $_GET['action']=="deconnect")) { // Eleve non identifié
    session_destroy();
?>
<!-- <div class="form"> -->
<h2>Connexion</h2>
<p>Connectez-vous à l'aide de vos identifiants LDAP (DSI) :</p>
<?php if (isset($erreurID)) { echo '<p style="color:red;">Erreur d\'identification !</p>'; } ?>
<form action="/x/connexion" method="post">
<p id="champ-user" class="champ" class="champ"><label for="user">Utilisateur : </label><input type="text" name="user"/></p>
<p id="champ-pass" class="champ"><label for="pass">Mot de passe : </label><input type="password" name="pass"/></p>
<br/>
<input type="submit" value="Se connecter"/>
<span class="clearfloat"></span>
</form>

<!-- </div> -->

<?php
} else { // Eleve identifié
    if ((isset($_GET['action']) && $_GET['action'] == "modify") || !$_SESSION['eleve']->isValid()) { // on teste si l'élève a déjà entré ses infos personnelles
        $promos = $parametres->getList(Parametres::PROMO);
        $sections = $parametres->getList(Parametres::SECTION);
        $prepas = $parametres->getList(Parametres::ETABLISSEMENT);
        $filieres = $parametres->getList(Parametres::FILIERE);
        ?>

<h2>Modifier mes informations personnelles</h2>
<p>Merci de renseigner les informations qui permettront aux admissibles de vous identifier :</p>
<form action="/x/connexion" method="post">
<p id="champ-sexe" class="champ"><label for="sexe">Sexe</label> <label>: M <input type="radio" name="sexe" value="M"
    <?php 
        if ($_SESSION['eleve']->sexe() == "M" || $_SESSION['eleve']->sexe() == "") { 
            echo 'checked="checked"'; 
        }?>/></label> <label>/ F<input type="radio" name="sexe" value="F"
        <?php
        if ($_SESSION['eleve']->sexe() == "F") {
            echo 'checked="checked"'; 
        }?>/></label>
<?php if (isset($erreurs) && in_array(Eleve::SEXE_INVALIDE, $erreurs)) echo '<span style="color:red;">Merci de renseigner ce champ</span>'; ?>
</p>
<p id="champ-promo" class="champ"> <label for="promo">Promotion : </label><select name="promo">
            <option value=""></option>
        <?php
        foreach ($promos as $value) {
            if ($_SESSION['eleve']->promo() == $value['id']) {
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }
            echo '<option value="'.$value['id'].'"'.$selected.'>'.$value['nom'].'</option>';
        }
        ?>
        </select>
<?php if (isset($erreurs) && in_array(Eleve::PROMO_INVALIDE, $erreurs)) echo '<span style="color:red;">Merci de renseigner ce champ</span>'; ?>
</p>
<p id="champ-section" class="champ">
<label for="section">Section : </label><select name="section">
            <option value=""></option>
        <?php
        foreach ($sections as $value) {
            if ($_SESSION['eleve']->section() == $value['id']) {
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }
            echo '<option value="'.$value['id'].'"'.$selected.'>'.$value['nom'].'</option>';
        }
        ?>
        </select>
<?php if (isset($erreurs) && in_array(Eleve::SECTION_INVALIDE, $erreurs)) echo '<span style="color:red;">Merci de renseigner ce champ</span>'; ?>
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
<?php if (isset($erreurs) && in_array(Eleve::PREPA_INVALIDE, $erreurs)) echo '<span style="color:red;">Merci de renseigner ce champ</span>'; ?>
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
<?php if (isset($erreurs) && in_array(Eleve::FILIERE_INVALIDE, $erreurs)) echo '<span style="color:red;">Merci de renseigner ce champ</span>'; ?>
</p>
<br/>
<input type="submit" value="Modifier mes informations personnelles"/>
</form>

        <?php
    } else {
        $series = $parametres->getList(Parametres::SERIE);
        $dispos = $eleveManager->getDispo($_SESSION['eleve']->user());
        ?>
<h2>Disponibilitét d'accueil</h2>
<p>Bienvenue <?php echo $_SESSION['eleve']->user(); ?></p>
<a href="/x/connexion?action=deconnect">Se déconnecter</a> -- <a href="/x/connexion?&action=modify">Modifier mes informations personnelles</a>
<hr/>
        <?php 
        if (!empty($series)) {
        ?>
<p>Cochez ci-dessous les semaines pour lesquelles vous êtes disposés à accueillir un admissible :</p>
<form action="/x/connexion" method="post">
<input type="hidden" name="serie" value="1"/>
        <?php
        foreach ($series as $value) {
            if ($value['ouverture'] < time()) {
                $disabled = "disabled";
                $name = "";
            } else {
                $disabled = "";
                $name = "name='serie".$value['id']."'";
            }
            if (in_array($value['id'], $dispos)) {
                $checked = "checked";
            } else {
                $checked = "";
            }
            echo $value['intitule'].' (du '.date("d.m.Y", $value['date_debut']).' au '.date("d.m.Y", $value['date_fin']).') : <input type="checkbox" '.$name.' '.$checked.' '.$disabled.'/><br/>';
        }
        ?>
<br/>
<input type="submit" value="Modifier mes disponibilités d'accueil"/>
</form>
        <?php
        } else {
            echo "<p>Il n'est pas encore possible de mettre à jour vos disponibilitées d'hébergement. Merci de repasser plus tard...</p>";
        }
        ?>
<hr/>
<p>Récapitulatif de vos demandes :</p>
        <?php
        $demandes = $demandeManager->getDemandes($_SESSION['eleve']->user());
        echo '<table border=1 cellspacing=0>';
        echo '<tr>
                  <td>Nom</td>
                  <td>Prérom</td>
                  <td>Sexe</td>
                  <td>Etablissement</td>
                  <td>Filière</td>
                  <td>Série</td>
                  <td>Statut</td>
                  <td>Action possible</td>
              </tr>';
        foreach ($demandes as $demande) {
            switch ($demande->status()) {
			case 0:
				$status_libele = "En cours de validation par l'admissible";
				$action = "Merci d'attendre que l'admissible ait vérifié son adresse email. Vous ne recevrez pas d'autre demande que celle-ci pour cette série.";
				break;
			case 1:
				$status_libele = "En attente d'acceptation";
				$action = "<form action='index.php?page=eleve' method='post'><input type='hidden' name='accept' value='".$demande->code()."'><input type='submit' value='Accepter la demande'/></form>";
				break;
			case 2:
				$status_libele = "Validée";
				$action = "Prendre contact avec l'admissible pour définir les modalités de son arrivée : ".$demande->email();
				break;
			case 3:
				$status_libele = "Annulée";
				$action = "";
				break;
			default:
            	throw new RuntimeException('Statut erroné'); // Ne se produit jamais en exécution courante
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
}
?>
<span id="page_id">3</span>
