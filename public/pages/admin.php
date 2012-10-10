<?php
/**
 * Page d'administration de la plate-forme d'hébergement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 0
 *
 * @todo identification LDAP
 * @todo gestion hotels
 */

// Identification
if (isset($_POST['user']) && !empty($_POST['user']) && !empty($_POST['pass']))
{
    if ($_POST['user'] == $parametres->getAdmin() && true) { // LDAP
        $_SESSION['administrateur'] = true;
    }
    else {
        $erreurID = 1;
    }
}


// Interface de connexion
if (!isset($_SESSION['administrateur']) || (isset($_GET['action']) && $_GET['action']=="deconnect")) { 
session_destroy();
?>
    <h1>Connexion</h1>
    <?php if (isset($erreurID)) { echo '<p style="color:red;">Erreur d\'identification !</p>'; } ?>
    <form action="./index_dev.php" method="post">
    Utilisateur : <input type="text" name="user"/><br/>
    Mot de passe : <input type="password" name="pass"/><br/>
    <input type="submit" value="Se connecter"/>
    </form>
<?php
}
else {
    echo "<h1>Interface d'administration</h1>";
    if (isset($_GET['action']) && $_GET['action'] == "param" && isset($_GET['type'])) { // Gestion des listes de paramètres
        echo "<a href='./index_dev.php'>Retour à l'accueil</a>";
        switch ($_GET['type']) {
            case Parametres::PROMO: 
                echo "<h2>Promotions présentes sur le platâl</h2>";
                $form = "<input type='text' name='nom' maxlength='50'/>";
                break;

            case Parametres::ETABLISSEMENT: 
                echo "<h2>Etablissements de provenance des élèves</h2><p>Les élèves gardent la possibilité d'entrer une autre valeur que celles proposées ci-dessous.</p>";
                $form = "<input type='text' name='ville' value='VILLE' size='10' maxlength='50'/> - <input type='text' name='nom' value=\"Nom de l'établissement\" size='30' maxlength='50'/>";
                break;

            case Parametres::FILIERE:
                echo "<h2>Filières d'entrée des élèves</h2>";
                $form = "<input type='text' name='nom' maxlength='50'/>";
                break;

            case Parametres::SECTION:
                echo "<h2>Sections sportives</h2>";
                $form = "<input type='text' name='nom' maxlength='50'/>";
                break;

            default: 
                $erreurP = 1; echo "<h2>Erreur de paramétrage...</h2>";
                break;
        }
        if (!isset($erreurP)) { // Si aucune erreure de paramétrage
            if (isset($_GET['suppr'])) { // Suppression d'un élément de liste
                if (!is_numeric($_GET['suppr'])) {
                    throw new RuntimeException('Corruption des paramètres GET'); // Ne se produit jamais en exécution courante
                }
                if (!$parametres->isUsedList($_GET['type'], $_GET['suppr'])) {
                    $parametres->deleteFromList($_GET['type'], $_GET['suppr']);
                } else {
                    $erreurA = "Vous ne pouvez supprimer cet élément tant qu'il est utilisé dans le profil d'un élève ou d'un admissible";
                }
            }
            if (isset($_POST['nom']) && isset($_POST['ville'])) { // Ajout d'un élément de liste (Etablissement)
                if (!empty($_POST['nom']) && !empty($_POST['ville']) && strlen($_POST['nom']) <= 50 && strlen($_POST['ville']) <= 50) {
                    $parametres->addToList($_GET['type'], array("nom" => $_POST['nom'], "commune" => $_POST['ville']));
                } else {
                    $erreurA = "Erreur lors de l'ajout d'un nouvel élément";
                }
            } elseif (isset($_POST['nom'])) { // Ajout d'un élément de liste (autre)
                if (!empty($_POST['nom']) && strlen($_POST['nom']) <= 50) {
                    $parametres->addToList($_GET['type'], array("nom" => $_POST['nom']));
                } else {
                    $erreurA = "Erreur lors de l'ajout d'un nouvel élément";
                }
            }
            $liste = $parametres->getList($_GET['type']);
            echo "<span style='color:red;'>".@$erreurA."</span>";
            echo "<form action='index_dev.php?action=param&type=".$_GET['type']."' method='post'>";
            echo "<table border=1 cellspacing=0>";
            echo "<tr><td>Valeur</td><td>Action</td></tr>";
            foreach ($liste as $res) {
                if ($_GET['type'] == Parametres::ETABLISSEMENT) {
                    $res['nom'] = $res['ville']." - ".$res['nom'];
                }
                echo "<tr>";
                    echo "<td>".$res['nom']."</td><td><a href='index_dev.php?action=param&type=".$_GET['type']."&suppr=".$res['id']."'>Suppr</a></td>";
                echo "</tr>";
            }
            echo "<tr>";
            echo "<td>".$form."</td>";
            echo "<td><input type='submit' value='Ajouter'/></td>";
            echo "</tr>";
            echo "</table>";
            echo "</form>";
        }
    } elseif (isset($_GET['action']) && $_GET['action'] == "series") { // Modification des séries d'admissibilité
        if (isset($_GET['suppr'])) { // Suppression d'une série
            if (!is_numeric($_GET['suppr'])) {
                throw new RuntimeException('Corruption des paramètres GET'); // Ne se produit jamais en exécution courante
            }
            if (!$parametres->isUsedList(Parametres::SERIE, $_GET['suppr'])) {
                $parametres->deleteFromList(Parametres::SERIE, $_GET['suppr']);
            } else {
                $erreurA = "Vous ne pouvez supprimer cette série tant qu'elle est utilisée dans le profil d'un élève ou d'un admissible";
            }
        }
        if (isset($_POST['intitule']) && isset($_POST['date_debut']) && isset($_POST['date_fin'])) { // Insertion d'une nouvelle série
            if (!empty($_POST['intitule']) && strlen($_POST['intitule']) <= 50 && preg_match("#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#",$_POST['date_debut']) && preg_match("#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#",$_POST['date_fin'])) {
                $expDateD = explode("/",$_POST['date_debut']);
                $expDateF = explode("/",$_POST['date_fin']);
                $date_debut = mktime(0,0,0,$expDateD[1],$expDateD[0],$expDateD[2]);
                $date_fin = mktime(0,0,0,$expDateF[1],$expDateF[0],$expDateF[2]);
                // L'ouverture des demandes sera réglée lors de l'insertion de la liste des admissibles
                // La fermeture des demandes correspond à minuit la veille du début des oraux
                $parametres->addToList(Parametres::SERIE, array("intitule" => $_POST['intitule'], "date_debut" => $date_debut, "date_fin" => $date_fin, "ouverture" => $date_debut, "fermeture" => $date_debut));
            } else {
                $erreurA = "Erreur lors de l'ajout d'une nouvelle série";
            }
        }
        echo "<a href='./index_dev.php'>Retour à l'accueil</a>";
        echo "<h2>Séries d'admissibilité</h2>";
        $series = $parametres->getList(Parametres::SERIE);
        echo "<span style='color:red;'>".@$erreurA."</span>";
        echo "<form action='index_dev.php?action=series' method='post'>";
        echo "<table border=1 cellspacing=0>";
        echo "<tr><td>Intitulé</td><td>Date de début des oraux</td><td>Date de fin des oraux</td><td>Action</td></tr>";
        foreach ($series as $value) {
            echo "<tr>";
                echo "<td>".$value['intitule']."</td></td>";
                echo "<td>".date("d/m/Y", $value['date_debut'])."</td>";
                echo "<td>".date("d/m/Y", $value['date_fin'])."</td>";
                echo "<td><a href='index_dev.php?action=series&suppr=".$value['id']."'>Suppr</a></td>";
            echo "</tr>";
        }
        echo "<tr>";
        echo "<td><input type='text' name='intitule'/></td>";
        echo "<td><input type='text' name='date_debut' value='00/00/0000'/></td>";
        echo "<td><input type='text' name='date_fin' value='00/00/0000'/></td>";
        echo "<td><input type='submit' value='Ajouter'/></td>";
        echo "</tr>";
        echo "</table>";
        echo "</form>";
    } elseif (isset($_GET['action']) && $_GET['action'] == "admissibles") { // Modification des listes d'admissibilité
        if (isset($_POST['serie']) && isset($_POST['filiere']) && isset($_POST['liste'])) { // Traitement de la liste ajoutée
            if (is_numeric($_POST['serie']) && is_numeric($_POST['filiere']) && preg_match("#^(.+\s\(.+\)(\r)?(\n)?)+$#", $_POST['liste'])) {
                $parametres->parseADM($_POST['serie'],$_POST['filiere'],$_POST['liste']);
                $erreurA = "Ajout des admissibles réussi !";
            } else {
                $erreurA = "Mauvais formatage de la liste";
            }
        }
        echo "<a href='./index_dev.php'>Retour à l'accueil</a>";
        echo "<h2>Insertion d'une liste d'admissibilité</h2>";
        echo "<span style='color:red;'>".@$erreurA."</span>";
        echo "<p>Attention : l'insertion d'une liste d'admissibilité marque l'ouverture des demandes d'hébergement pour la série considérée !</p>";
        $filieres = $parametres->getList(Parametres::FILIERE);
        $series = $parametres->getList(Parametres::SERIE);
        ?>
        <form action="index_dev.php?action=admissibles" method="post">
            Série d'admissibilité : <select name="serie">
                <option value="" selected></option>
        <?php
        foreach ($series as $value) {
            if ($value['fermeture'] > time()) { // On n'affiche que les séries non encore commencées
                echo '<option value="'.$value['id'].'">'.$value['intitule'].' (du '.date("d.m.Y", $value['date_debut']).' au '.date("d.m.Y", $value['date_fin']).')</option>';
            }
        }
        ?>
            </select><br/><br/>
            Filière : <select name="filiere">
                <option value=""></option>
        <?php
        foreach ($filieres as $value) {
            echo '<option value="'.$value['id'].'">'.$value['nom'].'</option>';
        }
        ?>
            </select><br/><br/>
            Liste des candidats reçus de la forme suivante :<br/>
            <i>Nom (Prénom)<br/>
            Nom (Prénom)<br/>
            Nom (Prénom)</i><br/>
            <textarea name="liste" rows="10" cols="40"></textarea><br/><br/>
            En validant ce formulaire, vous publiez cette liste d'admissibilité et ouvrez les demandes d'hébergement pour ces admissibles :
            <input type="submit" value="Valider"/>
        </form>
        <?php
    } elseif (isset($_GET['action']) && $_GET['action'] == "RAZ") { // Interface de remise à zéro de la plate-forme
        echo "<a href='./index_dev.php'>Retour à l'accueil</a>";
        if (isset($_POST['raz']) && $_POST['raz']) {
            $parametres->remiseAZero();
            echo "<h3 style='color:red;'>Remise à zéro effectuée</h3>";
        }
        ?>
        <p style="color:red;">Attention : la remise à zéro de l'interface est irréversible.</p>
        <p>Cette action efface toutes les informations relatives aux séries, aux admissibles, aux élèves, et aux demandes d'hébergement.</p>
        <form action="./index_dev.php?action=RAZ" method="post">
        Cocher cette case si vous êtes certain de vouloir effectuer une remmise à zéro de l'interface :
        <input type="checkbox" name="raz"/><br/>
        <input type="submit" value="Effectuer la remise à zéro"/>
        </form>
        <?php
    } else { // Interface de gestion courante
        ?>
        <a href="./index_dev.php?action=deconnect">Se déconnecter</a><br/>
        <a href="./index_dev.php?action=RAZ">Remise à zéro de l'interface d'hébergement</a><br/>
        <a href="./index_dev.php?action=series">Modifier les séries d'admissibilités (dates d'ouverture du site)</a><br/>
        <a href="./index_dev.php?action=param&type=<?php echo Parametres::PROMO; ?>">Modifier les promotions présentes sur le platâl</a><br/>
        <a href="./index_dev.php?action=param&type=<?php echo Parametres::ETABLISSEMENT; ?>">Modifier les établissements de provenance des élèves</a><br/>
        <a href="./index_dev.php?action=param&type=<?php echo Parametres::FILIERE; ?>">Modifier les filières d'entrée des élèves</a><br/>
        <a href="./index_dev.php?action=param&type=<?php echo Parametres::SECTION; ?>">Modifier les sections sportives des élèves</a><br/>
        <a href="./index_dev.php?action=admissibles">Entrer la liste des admissibles pour la prochaine série</a><br/>
        <a href="./index_dev.php?action=hotel">Modifier la liste des hébergements à proximité de l'école</a><br/>
        <?php
    }
}
?>