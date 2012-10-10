<?php
/**
 * Page d'administration de la plate-forme d'h�bergement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 0
 *
 * @todo identification LDAP
 * @todo gestion series
 * @todo gestion listes d'admissibilite
 * @todo gestion h�bergement
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
    if (isset($_GET['action']) && $_GET['action'] == "param" && isset($_GET['type'])) {
        echo "<a href='./index_dev.php'>Retour � l'accueil</a>";
        switch ($_GET['type']) {
            case Parametres::PROMO: 
                echo "<h2>Promotions pr�sentes sur le plat�l</h2>";
                $form = "<input type='text' name='nom' maxlength='50'/>";
                break;

            case Parametres::ETABLISSEMENT: 
                echo "<h2>Etablissements de provenance des �l�ves</h2><p>Les �l�ves gardent la possibilit� d'entrer une autre valeur que celles propos�es ci-dessous.</p>";
                $form = "<input type='text' name='ville' value='VILLE' size='10' maxlength='50'/> - <input type='text' name='nom' value=\"Nom de l'�tablissement\" size='30' maxlength='50'/>";
                break;

            case Parametres::FILIERE:
                echo "<h2>Fili�res d'entr�e des �l�ves</h2>";
                $form = "<input type='text' name='nom' maxlength='50'/>";
                break;

            case Parametres::SECTION:
                echo "<h2>Sections sportives</h2>";
                $form = "<input type='text' name='nom' maxlength='50'/>";
                break;

            default: 
                $erreurP = 1; echo "<h2>Erreur de param�trage...</h2>";
                break;
        }
        if (!isset($erreurP)) { // Si aucune erreure de param�trage
            if (isset($_GET['suppr'])) { // Suppression d'un �l�ment de liste
                if (!is_numeric($_GET['suppr'])) {
                    throw new RuntimeException('Corruption des param�tres GET'); // Ne se produit jamais en ex�cution courante
                }
                if (!$parametres->isUsedList($_GET['type'], $_GET['suppr'])) {
                    $parametres->deleteFromList($_GET['type'], $_GET['suppr']);
                } else {
                    $erreurA = "Vous ne pouvez supprimer cet �l�ment tant qu'il est utilis� dans le profil d'un �l�ve ou d'un admissible";
                }
            }
            if (isset($_POST['nom']) && isset($_POST['ville'])) { // Ajout d'un �l�ment de liste (Etablissement)
                if (!empty($_POST['nom']) && !empty($_POST['ville']) && strlen($_POST['nom']) <= 50 && strlen($_POST['ville']) <= 50) {
                    $parametres->addToList($_GET['type'], array("nom" => $_POST['nom'], "commune" => $_POST['ville']));
                } else {
                    $erreurA = "Erreur lors de l'ajout d'un nouvel �l�ment";
                }
            } elseif (isset($_POST['nom'])) { // Ajout d'un �l�ment de liste (autre)
                if (!empty($_POST['nom']) && strlen($_POST['nom']) <= 50) {
                    $parametres->addToList($_GET['type'], array("nom" => $_POST['nom']));
                } else {
                    $erreurA = "Erreur lors de l'ajout d'un nouvel �l�ment";
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
    } elseif (isset($_GET['action']) && $_GET['action'] == "RAZ") { // Interface de remise � z�ro de la plate-forme
        echo "<a href='./index_dev.php'>Retour � l'accueil</a>";
        if (isset($_POST['raz']) && $_POST['raz']) {
            $sup_dispos = $bdd->query('DELETE FROM disponibilites');
            $sup_series = $bdd->query('DELETE FROM series');
            $sup_demandes = $bdd->query('DELETE FROM demandes');
            $sup_eleves = $bdd->query('DELETE FROM x');
            $sup_admissibles = $bdd->query('DELETE FROM admissibles');
            echo "<h2 style='color:red;'>Remise � z�ro effectu�e</h2>";
        }
        ?>
        <p style="color:red;">Attention : la remise � z�ro de l'interface est irr�versible.</p>
        <p>Cette action efface toutes les informations relatives aux admissibles, aux �l�ves, et aux demandes d'h�bergement.</p>
        <form action="./index_dev.php?action=RAZ" method="post">
        Cocher cette case si vous �tes certain de vouloir effectuer une remmise � z�ro de l'interface :
        <input type="checkbox" name="raz"/><br/>
        <input type="submit" value="Effectuer la remise � z�ro"/>
        </form>
        <?php
    } else { // Interface de gestion courante
        ?>
        <a href="./index_dev.php?action=deconnect">Se d�connecter</a><br/>
        <a href="./index_dev.php?action=RAZ">Remise � z�ro de l'interface d'h�bergement</a><br/>
        <a href="./index_dev.php?action=series">Modifier les s�ries d'admissibilit�s (dates d'ouverture du site)</a><br/>
        <a href="./index_dev.php?action=param&type=<?php echo Parametres::PROMO; ?>">Modifier les promotions pr�sentes sur le plat�l</a><br/>
        <a href="./index_dev.php?action=param&type=<?php echo Parametres::ETABLISSEMENT; ?>">Modifier les �tablissements de provenance des �l�ves</a><br/>
        <a href="./index_dev.php?action=param&type=<?php echo Parametres::FILIERE; ?>">Modifier les fili�res d'entr�e des �l�ves</a><br/>
        <a href="./index_dev.php?action=param&type=<?php echo Parametres::SECTION; ?>">Modifier les sections sportives des �l�ves</a><br/>
        <a href="./index_dev.php?action=admissibles">Entrer la liste des admissibles pour la s�rie en cours</a><br/>
        <a href="./index_dev.php?action=hotel">Modifier la liste des h�bergements � proximit� de l'�cole</a><br/>
        <?php
    }
}
?>