<?php
/**
 * Page de gestion des Eleves X
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 0
 *
 * @todo : identification LDAP
 * @todo : action des formulaires (prod)
 * @todo : affichage et gestion des demandes
 */

$eleveManager = new EleveManager($db);

// Identification
if (isset($_POST['user']) && isset($_POST['pass']) && !empty($_POST['user']) && !empty($_POST['pass']))
{
    if (true) { // identification LDAP ***
        $_SESSION['eleve'] = $eleveManager->getUnique($_POST['user']);
        if ($_SESSION['eleve'] == NULL) {
            $_SESSION['new'] = 1;
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
// Modification des disponibilit�s d'acceuil
if (isset($_SESSION['eleve']) && isset($_POST['serie']) && $_POST['serie'] == "1") {
    $series = $parametres->getList(Parametres::SERIE);
    $dispo = array();
    foreach ($series as $value) {
        if (isset($_POST["serie".$value['id']])) {
            if ($value['ouverture'] > time()) {
                $dispo[] = $value['id'];
            } else {
                throw new RuntimeException('S�rie invalide'); // Ne se produit jamais en ex�cution courante
            }
        }
    }
    $eleveManager->updateDispo($_SESSION['eleve']->user(), $dispo);
}
// Interface de connexion
if (!isset($_SESSION['eleve']) || (isset($_GET['action']) && $_GET['action']=="deconnect")) { // Eleve non identifi�
    session_destroy();
?>
<h1>Connexion</h1>
<p>Connectez-vous � l'aide de vos identifiants LDAP (DSI) :</p>
<?php if (isset($erreurID)) { echo '<p style="color:red;">Erreur d\'identification !</p>'; } ?>
<form action="./index_dev.php" method="post">
Utilisateur : <input type="text" name="user"/><br/>
Mot de passe : <input type="password" name="pass"/><br/>
<input type="submit" value="Se connecter"/>
</form>


<?php
} else { // Eleve identifi�
    if ((isset($_GET['action']) && $_GET['action'] == "modify") || !$_SESSION['eleve']->isValid()) { // on teste si l'�l�ve a d�j� entr� ses infos personnelles
        $promos = $parametres->getList(Parametres::PROMO);
        $sections = $parametres->getList(Parametres::SECTION);
        $prepas = $parametres->getList(Parametres::ETABLISSEMENT);
        $filieres = $parametres->getList(Parametres::FILIERE);
        ?>

<h1>Modifier mes informations personnelles</h1>
<p>Merci de renseigner les informations qui permettront aux admissibles de vous identifier :</p>
<form action="./index_dev.php" method="post">
Sexe : M <input type="radio" name="sexe" value="M"
    <?php 
        if ($_SESSION['eleve']->sexe() == "M" || $_SESSION['eleve']->sexe() == "") { 
            echo 'checked="checked"'; 
        }?>/> / F<input type="radio" name="sexe" value="F"
        <?php
        if ($_SESSION['eleve']->sexe() == "F") {
            echo 'checked="checked"'; 
        }?>/>
<?php if (isset($erreurs) && in_array(Eleve::SEXE_INVALIDE, $erreurs)) echo '<span style="color:red;">Merci de renseigner ce champ</span>'; ?>
<br/>
Promotion : <select name="promo">
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
<br/>
Section : <select name="section">
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
<br/>
Etablissement d'origine : <select name="prepa">
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
<br/>
Fili�re : <select name="filiere">
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
<br/><br/>
<input type="submit" value="Modifier mes informations personnelles"/>
</form>

        <?php
    } else {
        $series = $parametres->getList(Parametres::SERIE);
        $dispos = $eleveManager->getDispo($_SESSION['eleve']->user());
        ?>
<h1>Disponibilit�s d'accueil</h1>
<p>Bienvenue <?php echo $_SESSION['eleve']->user(); ?></p>
<a href="./index_dev.php?action=deconnect">Se d�connecter</a> -- <a href="./index_dev.php?action=modify">Modifier mes informations personnelles</a>
<hr/>
        <?php 
        if (!empty($series)) {
        ?>
<p>Cochez ci-dessous les semaines pour lesquelles vous �tes dispos�s � accueillir un admissible :</p>
<form action="./index_dev.php" method="post">
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
<input type="submit" value="Modifier mes disponibilit�s d'accueil"/>
</form>
        <?php
        } else {
            echo "<p>Il n'est pas encore possible de mettre � jour vos disponibilit�s d'h�bergement. Merci de repasser plus tard...</p>";
        }
        ?>
<hr/>
<p>R�capitulatif de vos demandes :</p>
        <?php
        $demandeManager = new DemandeManager($db);
        $demandes = $demandeManager->getDemandes($_SESSION['eleve']->user());
        echo '<table border=1 cellspacing=0>';
        echo '<tr>
                  <td>Nom</td>
                  <td>Pr�nom</td>
                  <td>Sexe</td>
                  <td>Adresse email</td>
                  <td>Etablissement</td>
                  <td>Fili�re</td>
                  <td>S�rie</td>
                  <td>Statut</td>
                  <td>Action possible</td>
              </tr>';
        foreach ($demandes as $demande) {
            //switch ($demande->status())
            
            echo '<tr>
                    <td>'.$demande->nom().'</td>
                    <td>'.$demande->prenom().'</td>
                    <td>'.$demande->sexe().'</td>
                    <td>'.$demande->email().'</td>
                    <td>'.$demande->prepa().'</td>
                    <td>'.$demande->filiere().'</td>
                    <td>'.$demande->serie().'</td>
                    <td>'.$status.'</td>
                    <td>'.$action.'</td>
                  </tr>';
        }
        echo '</table>';
    }
}

?>