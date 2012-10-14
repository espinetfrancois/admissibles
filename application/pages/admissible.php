<?php
/**
 * Page de gestion des demandes d'h�bergement des admissibles
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo gestion de l'envoi du mail de validation/annulation
 * @todo logs
 */

include_once(APPLICATION_PATH.'/inc/sql.php');

$demandeManager = new DemandeManager($db);
$eleveManager = new EleveManager($db);

echo "<h2>Demande d'h�bergement chez un �l�ve pendant la p�riode des oraux</h2>";

if (isset($_SESSION['demande']) && isset($_POST['user'])) {
    $dispos = $eleveManager->getDispos($_POST['user']);
    if (!in_array($_SESSION['demande']->serie(),$dispos)) {
        echo "<p>D�sol�, l'�l�ve que vous avez choisi vient d'�tre sollicit�. Merci de reit�rer votre recherche.</p>";
    }
    elseif (!$demandeManager->autorisation($_SESSION['demande'])) {
        echo "<p>D�sol�, vous avez d�j� effectu� une demande d'h�bergement. Merci d'attendre la r�ponse de l'�l�ve ou d'annuler votre demande.</p>";
    } else {
        $_SESSION['demande']->setUserEleve($_POST['user']);
        $_SESSION['demande']->setStatus("0");
        $_SESSION['demande']->setCode(md5(sha1(time().$_SESSION['demande']->email())));
        // envoi d'un email de validation contenant <a href="http://.../validation.php?code=$_SESSION['demande']->code()">Valider votre demande</a> <a href="http://.../?code=$_SESSION['demande']->code()">Annuler votre demande</a>
        $eleveManager->deleteDispo($_POST['user'], $_SESSION['demande']->serie());
    }
}

if (isset($_GET['action']) && $_GET['action'] == "demande") {
    $series = $parametres->getCurrentSeries();
    if (empty($series)) { // Interface ferm�e aux demandes
        ?>
        <p>Les demandes ne sont pas encore ouvertes pour la prochaine s�rie...</p>
        <?php
    } else {
        if (isset($_POST['nom'])) {
            unset($erreurD);
            $demande = new Demande(array('nom' => $_POST['nom'],
                                         'prenom' => $_POST['prenom'],
                                         'email' => $_POST['email'],
                                         'sexe' => $_POST['sexe'],
                                         'sport' => $_POST['section'],
                                         'prepa' => $_POST['prepa'],
                                         'filiere' => $_POST['filiere'],
                                         'serie' => $_POST['serie']));
            $erreurD = $demande->erreurs();
            if (!$demandeManager->isAdmissible($_POST['nom'], $_POST['prenom'], $_POST['serie'])) {
                $erreurD[] = Demande::NON_ADMISSIBLE;
            }
        }
        if (isset($demande) && empty($erreurD)) { // Demande r�ussie : affichage de deux X pouvant les h�berger
            $_SESSION['demande'] = $demande;
            $eleves = $eleveManager->getFavorite($demande, 2);
            if (empty($eleves)) {
                echo "<p>D�sol�, aucune correspondance n'a �t� trouv�e (tous les �l�ves ont d�j� �t� sollicit�s.<br/>Rendez-vous sur la page <a href=''>Bonnes adresses</a> pour trouver un h�bergement � proximit� de l'�cole...</p>";
            } else {
                echo "<p>Voici les �l�ves qui te correspondent le mieux pour t'h�berger :</p>";
                echo "<table border=1 cellspacing=0>";
                echo "<tr><td>Nom d'utilisateur</td><td>Sexe</td><td>Etablissement d'origine</td><td>Fili�re</td><td>Section sportive</td><td>Contact</td></tr>";
                foreach ($eleves as $eleve) {
                    echo "<tr><td>".$eleve->user()."</td><td>".$eleve->sexe()."</td><td>".$eleve->prepa()."</td><td>".$eleve->filiere()."</td><td>".$eleve->section()."</td>";
                    echo "<td><form action='index.php?page=admissible' method='post'><input type='hidden' name='user' value='".$eleve->user()."'/><input type='submit' value='Envoyer une demande de logement'/></form></td></tr>";
                }
                echo "</table>";
            }
        } else { // Demande non remplie ou avec erreurs
            $prepas = $parametres->getList(Parametres::ETABLISSEMENT);
            $filieres = $parametres->getList(Parametres::FILIERE);
            $sections = $parametres->getList(Parametres::SECTION);
            
            if (isset($erreurD) && in_array(Demande::NON_ADMISSIBLE, $erreurD)) echo '<span style="color:red;">Merci de v�rifier vos informations personnelles : vous ne semblez pas �tre dans les listes d\'admissibilit� !</span>'; ?>
            <form action="index.php?page=admissible&action=demande" method="post">
            Nom : <input type="text" name="nom" value="<?php if (isset($demande)) { echo $demande->nom(); } ?>"/>
            <?php if (isset($erreurD) && in_array(Demande::NOM_INVALIDE, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Pr�nom : <input type="text" name="prenom" value="<?php if (isset($demande)) { echo $demande->prenom(); } ?>"/>
            <?php if (isset($erreurD) && in_array(Demande::PRENOM_INVALIDE, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Adresse e-mail valide : <input type="text" name="email" value="<?php if (isset($demande)) { echo $demande->email(); } ?>"/>
            <?php if (isset($erreurD) && in_array(Demande::EMAIL_INVALIDE, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Sexe : M <input type="radio" name="sexe" value="M" <?php if (!isset($demande) || $demande->sexe() == "M") { echo "checked='checked'"; } ?>/>/ F<input type="radio" name="sexe" value="F" <?php if (isset($demande) && $demande->sexe() == "F") { echo "checked='checked'"; } ?>/>
            <?php if (isset($erreurD) && in_array(Demande::SEXE_INVALIDE, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Etablissement d'origine : <select name="prepa">
                <?php
                foreach ($prepas as $value) {
                    if (isset($demande) && $demande->prepa() == $value['id']) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    echo '<option value="'.$value['id'].'" '.$selected.'>'.$value['ville'].' - '.$value['nom'].'</option>';
                }
                ?>
                <option value="-1">Autre</option>
            </select>
            <?php if (isset($erreurD) && in_array(Demande::PREPA_INVALIDE, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Fili�re : <select name="filiere">
            <?php
            foreach ($filieres as $value) {
                if (isset($demande) && $demande->filiere() == $value['id']) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                echo '<option value="'.$value['id'].'" '.$selected.'>'.$value['nom'].'</option>';
            }
            ?>
            </select>
            <?php if (isset($erreurD) && in_array(Demande::FILIERE_INVALIDE, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            S�rie : <select name="serie">
            <?php
            foreach ($series as $value) {
                if (isset($demande) && $demande->serie() == $value['id']) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                echo '<option value="'.$value['id'].'" '.$selected.'>'.$value['intitule'].' (du '.date("d.m.Y", $value['date_debut']).' au '.date("d.m.Y", $value['date_fin']).')</option>';
            }
            ?>
            </select>
            <?php if (isset($erreurD) && in_array(Demande::SERIE_INVALIDE, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Sport pr�f�r� : <select name="section">
            <?php
            foreach ($sections as $value) {
                echo '<option value="'.$value['id'].'">'.$value['nom'].'</option>';
            }
            ?>
            </select>
            <?php if (isset($erreurD) && in_array(Demande::SPORT_INVALIDE, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/><br/>
            <input type="submit" value="Rechercher un logement"/>
            </form>
            <?php
        }
    }
} else { // Page affich�e 
    ?>
    <p>Cette interface vous permet de trouver un �l�ve pr�sent sur le campus pour vous h�berger pendant la p�riode des oraux.<br/>
D�s la sortie des listes d'admissibilit�s pour votre s�rie et jusqu'� la veille du d�but des �preuves orales minuit, rendez-vous sur la page <a href="index.php?page=admissible&action=demande">Faire une demande de logement</a>, 
remplissez le formulaire avec vos informations personnelles et envoyer votre demande aupr�s de l'�l�ve qui vous correspond le mieux (m�me lyc�e de provenance, m�me fili�re ou autres...).<br/><br/>
D�s l'aceptation par l'�l�ve concern�, vous recevrez un email de confirmation vous permettant de prendre contact avec lui pour organiser votre arriv�e.<br/>
Si votre demande semble prendre trop de temps pour �tre accept�e par l'�l�ve que vous avez choisi, annulez votre premi�re demande et remplissez-en une autre...<br/><br/>
Par ailleurs, n'h�sitez pas � consulter la liste des <a href="index.php?page=adresses">bonnes adresses</a> si vous souhaitez vous loger par vos propres moyens � proximit� du campus (h�tel, pension...)</p>
    <?php
}

?>
<span id="page_id">12</span>