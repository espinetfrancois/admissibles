<?php
/**
 * Page de gestion des demandes d'hébergement des admissibles
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo gestion de l'envoi du mail de validation/annulation
 * @todo logs
 */

$demandeManager = new DemandeManager($db);
$eleveManager = new EleveManager($db);

echo "<h2>Demande d'hébergement chez un élève pendant la période des oraux</h2>";

if (isset($_SESSION['demande']) && isset($_POST['user'])) {
    $dispos = $eleveManager->getDispos($_POST['user']);
    if (!in_array($_SESSION['demande']->serie(),$dispos)) {
        echo "<p>Désolé, l'élève que vous avez choisi vient d'être sollicité. Merci de reitérer votre recherche.</p>";
    }
    elseif (!$demandeManager->autorisation($_SESSION['demande'])) {
        echo "<p>Désolé, vous avez déjà effectué une demande d'hébergement. Merci d'attendre la réponse de l'élève ou d'annuler votre demande.</p>";
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
    if (empty($series)) { // Interface fermée aux demandes
        ?>
        <p>Les demandes ne sont pas encore ouvertes pour la prochaine série...</p>
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
        if (isset($demande) && empty($erreurD)) { // Demande réussie : affichage de deux X pouvant les héberger
            $_SESSION['demande'] = $demande;
            $eleves = $eleveManager->getFavorite($demande, 2);
            if (empty($eleves)) {
                echo "<p>Désolé, aucune correspondance n'a été trouvée (tous les élèves ont déjà été sollicités.<br/>Rendez-vous sur la page <a href=''>Bonnes adresses</a> pour trouver un hébergement à proximité de l'école...</p>";
            } else {
                echo "<p>Voici les élèves qui te correspondent le mieux pour t'héberger :</p>";
                echo "<table border=1 cellspacing=0>";
                echo "<tr><td>Nom d'utilisateur</td><td>Sexe</td><td>Etablissement d'origine</td><td>Filière</td><td>Section sportive</td><td>Contact</td></tr>";
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
            
            if (isset($erreurD) && in_array(Demande::NON_ADMISSIBLE, $erreurD)) echo '<span style="color:red;">Merci de vérifier vos informations personnelles : vous ne semblez pas être dans les listes d\'admissibilité !</span>'; ?>
            <form action="index.php?page=admissible&action=demande" method="post">
            Nom : <input type="text" name="nom" value="<?php if (isset($demande)) { echo $demande->nom(); } ?>"/>
            <?php if (isset($erreurD) && in_array(Demande::NOM_INVALIDE, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Prénom : <input type="text" name="prenom" value="<?php if (isset($demande)) { echo $demande->prenom(); } ?>"/>
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
            Filière : <select name="filiere">
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
            Série : <select name="serie">
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
            Sport préféré : <select name="section">
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
} else { // Page affichée 
    ?>
    <p>Cette interface vous permet de trouver un élève présent sur le campus pour vous héberger pendant la période des oraux.<br/>
Dès la sortie des listes d'admissibilités pour votre série et jusqu'à la veille du début des épreuves orales minuit, rendez-vous sur la page <a href="index.php?page=admissible&action=demande">Faire une demande de logement</a>, 
remplissez le formulaire avec vos informations personnelles et envoyer votre demande auprès de l'élève qui vous correspond le mieux (même lycée de provenance, même filière ou autres...).<br/><br/>
Dès l'aceptation par l'élève concerné, vous recevrez un email de confirmation vous permettant de prendre contact avec lui pour organiser votre arrivée.<br/>
Si votre demande semble prendre trop de temps pour être acceptée par l'élève que vous avez choisi, annulez votre première demande et remplissez-en une autre...<br/><br/>
Par ailleurs, n'hésitez pas à consulter la liste des <a href="index.php?page=adresses">bonnes adresses</a> si vous souhaitez vous loger par vos propres moyens à proximité du campus (hôtel, pension...)</p>
    <?php
}

?>