<?php
/**
 * Page d'administration de la plate-forme d'hébergement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo identification LDAP
 * @todo log
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
    <h2>Connexion</h2>
    <?php if (isset($erreurID)) { echo '<p style="color:red;">Erreur d\'identification !</p>'; } ?>
    <form action="index.php?page=admin" method="post">
    Utilisateur : <input type="text" name="user"/><br/>
    Mot de passe : <input type="password" name="pass"/><br/>
    <input type="submit" value="Se connecter"/>
    </form>
<?php
}
else {
    echo "<h2>Interface d'administration</h2>";
    if (isset($_GET['action']) && $_GET['action'] == "param" && isset($_GET['type'])) { // Gestion des listes de paramétres
        echo "<a href='index.php?page=admin'>Retour � l'accueil</a>";
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
        if (!isset($erreurP)) { // Si aucune erreure de paramétrage
            if (isset($_GET['suppr'])) { // Suppression d'un élément de liste
                if (!is_numeric($_GET['suppr'])) {
                    throw new RuntimeException('Corruption des param�tres GET'); // Ne se produit jamais en exécution courante
                }
                if (!$parametres->isUsedList($_GET['type'], $_GET['suppr'])) {
                    $parametres->deleteFromList($_GET['type'], $_GET['suppr']);
                } else {
                    $erreurA = "Vous ne pouvez supprimer cet �l�ment tant qu'il est utilis� dans le profil d'un �l�ve ou d'un admissible";
                }
            }
            if (isset($_POST['nom']) && isset($_POST['ville'])) { // Ajout d'un élément de liste (Etablissement)
                if (!empty($_POST['nom']) && !empty($_POST['ville']) && strlen($_POST['nom']) <= 50 && strlen($_POST['ville']) <= 50) {
                    $parametres->addToList($_GET['type'], array("nom" => $_POST['nom'], "commune" => $_POST['ville']));
                } else {
                    $erreurA = "Erreur lors de l'ajout d'un nouvel �l�ment";
                }
            } elseif (isset($_POST['nom'])) { // Ajout d'un élément de liste (autre)
                if (!empty($_POST['nom']) && strlen($_POST['nom']) <= 50) {
                    $parametres->addToList($_GET['type'], array("nom" => $_POST['nom']));
                } else {
                    $erreurA = "Erreur lors de l'ajout d'un nouvel �l�ment";
                }
            }
            $liste = $parametres->getList($_GET['type']);
            echo "<span style='color:red;'>".@$erreurA."</span>";
            echo "<form action='index.php?page=admin&action=param&type=".$_GET['type']."' method='post'>";
            echo "<table border=1 cellspacing=0>";
            echo "<tr><td>Valeur</td><td>Action</td></tr>";
            foreach ($liste as $res) {
                if ($_GET['type'] == Parametres::ETABLISSEMENT) {
                    $res['nom'] = $res['ville']." - ".$res['nom'];
                }
                echo "<tr>";
                    echo "<td>".$res['nom']."</td><td><a href='index.php?page=admin&action=param&type=".$_GET['type']."&suppr=".$res['id']."'>Suppr</a></td>";
                echo "</tr>";
            }
            echo "<tr>";
            echo "<td>".$form."</td>";
            echo "<td><input type='submit' value='Ajouter'/></td>";
            echo "</tr>";
            echo "</table>";
            echo "</form>";
        }
    } elseif (isset($_GET['action']) && $_GET['action'] == "series") { // Modification des s�ries d'admissibilit�
        if (isset($_GET['suppr'])) { // Suppression d'une s�rie
            if (!is_numeric($_GET['suppr'])) {
                throw new RuntimeException('Corruption des paramétres GET'); // Ne se produit jamais en ex�cution courante
            }
            if (!$parametres->isUsedList(Parametres::SERIE, $_GET['suppr'])) {
                $parametres->deleteFromList(Parametres::SERIE, $_GET['suppr']);
            } else {
                $erreurA = "Vous ne pouvez supprimer cette s�rie tant qu'elle est utilis�e dans le profil d'un �l�ve ou d'un admissible";
            }
        }
        if (isset($_POST['intitule']) && isset($_POST['date_debut']) && isset($_POST['date_fin'])) { // Insertion d'une nouvelle s�rie
            if (!empty($_POST['intitule']) && strlen($_POST['intitule']) <= 50 && preg_match("#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#",$_POST['date_debut']) && preg_match("#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#",$_POST['date_fin'])) {
                $expDateD = explode("/",$_POST['date_debut']);
                $expDateF = explode("/",$_POST['date_fin']);
                $date_debut = mktime(0,0,0,$expDateD[1],$expDateD[0],$expDateD[2]);
                $date_fin = mktime(0,0,0,$expDateF[1],$expDateF[0],$expDateF[2]);
                // L'ouverture des demandes sera r�gl�e lors de l'insertion de la liste des admissibles
                // La fermeture des demandes correspond à minuit la veille du d�but des oraux
                $parametres->addToList(Parametres::SERIE, array("intitule" => $_POST['intitule'], "date_debut" => $date_debut, "date_fin" => $date_fin, "ouverture" => $date_debut, "fermeture" => $date_debut));
            } else {
                $erreurA = "Erreur lors de l'ajout d'une nouvelle s�rie";
            }
        }
        echo "<a href='index.php?page=admin'>Retour � l'accueil</a>";
        echo "<h2>S�ries d'admissibilit�</h2>";
        $series = $parametres->getList(Parametres::SERIE);
        echo "<span style='color:red;'>".@$erreurA."</span>";
        echo "<form action='index.php?page=admin&action=series' method='post'>";
        echo "<table border=1 cellspacing=0>";
        echo "<tr><td>Intitul�</td><td>Date de d�but des oraux</td><td>Date de fin des oraux</td><td>Action</td></tr>";
        foreach ($series as $value) {
            echo "<tr>";
                echo "<td>".$value['intitule']."</td></td>";
                echo "<td>".date("d/m/Y", $value['date_debut'])."</td>";
                echo "<td>".date("d/m/Y", $value['date_fin'])."</td>";
                echo "<td><a href='index.php?page=admin&action=series&suppr=".$value['id']."'>Suppr</a></td>";
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
    } elseif (isset($_GET['action']) && $_GET['action'] == "admissibles") { // Modification des listes d'admissibilit�
        if (isset($_POST['serie']) && isset($_POST['filiere']) && isset($_POST['liste'])) { // Traitement de la liste ajout�e
            if (is_numeric($_POST['serie']) && is_numeric($_POST['filiere']) && preg_match("#^(.+\s\(.+\)(\r)?(\n)?)+$#", $_POST['liste'])) {
                $parametres->parseADM($_POST['serie'],$_POST['filiere'],$_POST['liste']);
                $erreurA = "Ajout des admissibles r�ussi !";
            } else {
                $erreurA = "Mauvais formatage de la liste";
            }
        }
        echo "<a href='index.php?page=admin'>Retour � l'accueil</a>";
        echo "<h2>Insertion d'une liste d'admissibilit�</h2>";
        echo "<span style='color:red;'>".@$erreurA."</span>";
        echo "<p>Attention : l'insertion d'une liste d'admissibilit� marque l'ouverture des demandes d'h�bergement pour la s�rie consid�r�e !</p>";
        $filieres = $parametres->getList(Parametres::FILIERE);
        $series = $parametres->getList(Parametres::SERIE);
        ?>
        <form action="index.php?page=admin&action=admissibles" method="post">
            S�rie d'admissibilit� : <select name="serie">
                <option value="" selected></option>
        <?php
        foreach ($series as $value) {
            if ($value['fermeture'] > time()) { // On n'affiche que les s�ries non encore commenc�es
                echo '<option value="'.$value['id'].'">'.$value['intitule'].' (du '.date("d.m.Y", $value['date_debut']).' au '.date("d.m.Y", $value['date_fin']).')</option>';
            }
        }
        ?>
            </select><br/><br/>
            Fili�re : <select name="filiere">
                <option value=""></option>
        <?php
        foreach ($filieres as $value) {
            echo '<option value="'.$value['id'].'">'.$value['nom'].'</option>';
        }
        ?>
            </select><br/><br/>
            Liste des candidats re�us de la forme suivante :<br/>
            <i>Nom (Pr�nom)<br/>
            Nom (Pr�nom)<br/>
            Nom (Pr�nom)</i><br/>
            <textarea name="liste" rows="10" cols="40"></textarea><br/><br/>
            En validant ce formulaire, vous publiez cette liste d'admissibilit� et ouvrez les demandes d'h�bergement pour ces admissibles :
            <input type="submit" value="Valider"/>
        </form>
        <?php
    } elseif (isset($_GET['action']) && $_GET['action'] == "RAZ") { // Interface de remise � z�ro de la plate-forme
        echo "<a href='index.php?page=admin'>Retour � l'accueil</a>";
        if (isset($_POST['raz']) && $_POST['raz']) {
            $parametres->remiseAZero();
            echo "<h3 style='color:red;'>Remise � z�ro effectu�e</h3>";
        }
        ?>
        <p style="color:red;">Attention : la remise � z�ro de l'interface est irr�versible.</p>
        <p>Cette action efface toutes les informations relatives aux s�ries, aux admissibles, aux �l�ves, et aux demandes d'h�bergement.</p>
        <form action="index.php?page=admin&action=RAZ" method="post">
        
        <p id="champ-raz"><label for="raz">Cocher cette case si vous �tes certain de vouloir effectuer une remmise � z�ro de l'interface :</label>
        <input type="checkbox" name="raz"/></p>
        <input type="submit" value="Effectuer la remise à zéro"/>
        </form>
        <?php
    } elseif (isset($_GET['action']) && $_GET['action'] == "hotel") { // Interface de gestion des h�bergements � proximit� du campus
        $adresseManager = new AdresseManager($db);
        if (isset($_GET['suppr_cat'])) { // Suppression d'une cat�gorie
            if (!is_numeric($_GET['suppr_cat'])) {
                throw new RuntimeException('Corruption des param�tres GET'); // Ne se produit jamais en ex�cution courante
            }
            if (!$adresseManager->isUsedCat($_GET['suppr_cat'])) {
                $adresseManager->deleteCategorie($_GET['suppr_cat']);
            } else {
                $erreurA = "Vous ne pouvez supprimer cette cat�gorie tant qu'elle contient des adresses";
            }
        }
        if (isset($_GET['suppr'])) { // Suppression d'une annonce
            if (!is_numeric($_GET['suppr'])) {
                throw new RuntimeException('Corruption des param�tres GET'); // Ne se produit jamais en ex�cution courante
            }
            $adresseManager->delete($_GET['suppr']);
        }
        if (isset($_POST['nom_cat'])) { // Ajout d'une cat�gorie
            if (!empty($_POST['nom_cat']) && strlen($_POST['nom_cat']) <= 100) {
                $adresseManager->addCategorie($_POST['nom_cat']);
            } else {
                $erreurA = "Erreur lors de l'ajout d'une nouvelle cat�gorie";
            }
        }
        if (isset ($_POST['nom'])) { // Ajout ou Modification d'une annonce
            $adresse = new Adresse(array('nom' => $_POST['nom'],
                                         'adresse' => $_POST['adresse'],
                                         'tel' => $_POST['tel'],
                                         'email' => $_POST['email'],
                                         'description' => $_POST['description'],
                                         'categorie' => $_POST['categorie']));
            if (isset($_POST['id'])) {
                $adresse->setId($_POST['id']);
            }
            if (isset($_POST['valide'])) {
                $adresse->setValide(1);
            } else {
                $adresse->setValide(0);
            }
            if ($adresse->isValid()) {
                $adresseManager->save($adresse);
            } else {
                $erreurModif = $adresse->erreurs();
            }
           }
        $categories = $adresseManager->getCategories();
        echo "<a href='index.php?page=admin'>Retour � l'accueil</a>";
        echo "<h2>Gestion de la liste des h�bergements � proximit� de l'�cole</h2>";
        if (isset($_GET['ajout']) || isset($_GET['modif']) || isset($erreurModif)) { // Interface de modification d'une adresse
            if (isset($_GET['modif'])) {
                $adresse = $adresseManager->getUnique($_GET['modif']);
            }
            ?>
            <form action="index.php?page=admin&action=hotel" method="post">
            Nom : <input type="text" name="nom" value="<?php if (isset($adresse)) { echo $adresse->nom(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::NOM_INVALIDE, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Adresse : <input type="text" name="adresse" value="<?php if (isset($adresse)) { echo $adresse->adresse(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::ADRESSE_INVALIDE, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            T�l�phone : <input type="text" name="tel" value="<?php if (isset($adresse)) { echo $adresse->tel(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::TEL_INVALIDE, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Email : <input type="text" name="email" value="<?php if (isset($adresse)) { echo $adresse->email(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::EMAIL_INVALIDE, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Description : <?php if (isset($erreurModif) && in_array(Adresse::DESCRIPTION_INVALIDE, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            <textarea name="description" cols="20" rows="4"><?php if (isset($adresse)) { echo $adresse->description(); } ?></textarea><br/><br/>
            Cat�gorie : <select name="categorie">
                        <option value=""></option>
            <?php 
            foreach ($categories as $value) {
                if (isset($adresse) && $adresse->categorie() == $value['id']) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                echo '<option value="'.$value['id'].'" '.$selected.'>'.$value['nom'].'</option>';
            }
            if (isset($adresse) && $adresse->valide() == "1") {
                $checked = "checked";
            } else {
                $checked = "";
            }
            ?>
            </select> <?php if (isset($erreurModif) && in_array(Adresse::CATEGORIE_INVALIDE, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Afficher cette annonce sur le site ? <input type="checkbox" name="valide" <?php echo $checked; ?>/> <?php if (isset($erreurModif) && in_array(Adresse::VALIDE_INVALIDE, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            <?php
            if(isset($adresse) && !$adresse->isNew()) {
                ?>
                <input type="hidden" name="id" value="<?php echo $adresse->id(); ?>" />
                <input type="submit" value="Modifier l'annonce"/>
                <?php
            } else {
                ?>
                <input type="submit" value="Ajouter l'annonce" />
                <?php
            } ?>
            </form>
            <?php
        } else { 
            echo "<a href='index.php?page=adresses'>Voir la page publique affichant les adresses</a>"; // a r�gler
            // Gestion des cat�gories
            echo "<h3>Cat�gories d'h�bergement</h3>";
            echo "<span style='color:red;'>".@$erreurA."</span>";
            echo "<form action='index.php?page=admin&action=hotel' method='post'>";
            echo "<table border=1 cellspacing=0>";
            echo "<tr><td>Cat�gories</td><td>Action</td></tr>";
            foreach ($categories as $value) {
                echo "<tr>";
                    echo "<td>".$value['nom']."</td></td>";
                    echo "<td><a href='index.php?page=admin&action=hotel&suppr_cat=".$value['id']."'>Suppr</a></td>";
                echo "</tr>";
            }
            echo "<tr>";
            echo "<td><input type='text' name='nom_cat'/></td>";
            echo "<td><input type='submit' value='Ajouter'/></td>";
            echo "</tr>";
            echo "</table>";
            echo "</form>";
            // Gestion des adresses affich�es
            echo "<h3>Adresses affich�es actuellement sur le site</h3>";
            echo "<a href='index.php?page=admin&action=hotel&ajout=1'>Ajouter une annonce</a>";
            $adressesValides = $adresseManager->getListAffiche();
            echo "<table border=1 cellspacing=0>";
            echo "<tr><td>Annonce comme affich�e</td><td>Cat�gorie</td><td>Actions</td></tr>";
            foreach ($adressesValides as $adresse) {
                echo "<tr>";
                    echo "<td>".$adresse->nom()."<br/>".$adresse->adresse()."<br/>T�l : ".$adresse->tel()."<br/>Mail : ".$adresse->email()."<br/>".nl2br($adresse->description())."</td>";
                    echo "<td>".$adresse->categorie()."</td>";
                    echo "<td><a href='index.php?page=admin&action=hotel&modif=".$adresse->id()."'>Modif</a> <a href='index.php?page=admin&action=hotel&suppr=".$adresse->id()."'>Suppr</a></td>";
                echo "</tr>";
            }
            echo "</table>";
            // Gestion des adresses � valider
            echo "<h3>Adresses non affich�es (propos�es par les �l�ves)</h3>";
            echo "<p>Pour valider une annonce, cliquez sur Modifier et cocher la case correspondannte</p>";
            $adressesValides = $adresseManager->getListAffiche(0);
            echo "<table border=1 cellspacing=0>";
            echo "<tr><td>Annonce comme affich�e</td><td>Cat�gorie</td><td>Actions</td></tr>";
            foreach ($adressesValides as $adresse) {
                echo "<tr>";
                    echo "<td>".$adresse->nom()."<br/>".$adresse->adresse()."<br/>T�l : ".$adresse->tel()."<br/>Mail : ".$adresse->email()."<br/>".nl2br($adresse->description())."</td>";
                    echo "<td>".$adresse->categorie()."</td>";
                    echo "<td><a href='index.php?page=admin&action=hotel&modif=".$adresse->id()."'>Modif</a> <a href='index.php?page=admin&action=hotel&suppr=".$adresse->id()."'>Suppr</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else { // Interface de gestion courante
        ?>
        <a href="index.php?page=admin&action=deconnect">Se d�connecter</a><br/>
        <a href="index.php?page=admin&action=RAZ">Remise � z�ro de l'interface d'h�bergement</a><br/>
        <a href="index.php?page=admin&action=series">Modifier les s�ries d'admissibilit�s (dates d'ouverture du site)</a><br/>
        <a href="index.php?page=admin&action=param&type=<?php echo Parametres::PROMO; ?>">Modifier les promotions pr�sentes sur le platal</a><br/>
        <a href="index.php?page=admin&action=param&type=<?php echo Parametres::ETABLISSEMENT; ?>">Modifier les �tablissements de provenance des �l�ves</a><br/>
        <a href="index.php?page=admin&action=param&type=<?php echo Parametres::FILIERE; ?>">Modifier les fili�res d'entr�e des �l�ves</a><br/>
        <a href="index.php?page=admin&action=param&type=<?php echo Parametres::SECTION; ?>">Modifier les sections sportives des �l�ves</a><br/>
        <a href="index.php?page=admin&action=admissibles">Entrer la liste des admissibles pour la prochaine s�rie</a><br/>
        <a href="index.php?page=admin&action=hotel">Modifier la liste des h�bergements � proximit� de l'�cole</a><br/>
        <?php
    }
}
?>