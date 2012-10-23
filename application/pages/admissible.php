<?php
/**
 * Page de gestion des demandes d'hébergement des admissibles
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo gestion de l'envoi du mail de validation/annulation
 */
include_once(APPLICATION_PATH.'/inc/sql.php');

$demandeManager = new DemandeManager($db);
$eleveManager = new EleveManager($db);

echo '<h2>Demande d\'hébergement chez un élève pendant la période des oraux</h2>';

if (isset($_SESSION['demande']) && isset($_POST['user'])) {
    $dispos = $eleveManager->getDispos($_POST['user']);
    if (!in_array($_SESSION['demande']->serie(),$dispos)) {
        echo '<p>Désolé, l\'élève que vous avez choisi vient d\'être sollicité. Merci de reitérer votre recherche.</p>';
        Logs::logger(2, 'Demandes d\'admissibles simultannees sur l\'eleve '.$_POST['user']);
    }
    elseif (!$demandeManager->autorisation($_SESSION['demande'])) {
        echo '<p>Désolé, vous avez déjà effectué une demande d\'hébergement. Merci d\'attendre la réponse de l\'élève ou d\'annuler votre demande.</p>';
        Logs::logger(2, 'Tentative de sur-demande de l\'admissible '.$_SESSION['demande']->id());
    } else {
        $_SESSION['demande']->setUserEleve($_POST['user']);
        $_SESSION['demande']->setStatus('0');
        $_SESSION['demande']->setCode(md5(sha1(time().$_SESSION['demande']->email())));
        // envoi d'un email de validation contenant <a href="http://.../validation.php?code=$_SESSION['demande']->code()">Valider votre demande</a> <a href="http://.../?code=$_SESSION['demande']->code()">Annuler votre demande</a>
        $eleveManager->deleteDispo($_POST['user'], $_SESSION['demande']->serie());
        Logs::logger(1, 'Demande de logement '.$_SESSION['demande']->id().' effectuee');
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'demande') {
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
                $erreurD[] = Demande::Non_Admissible;
                Logs::logger(2, 'Formulaire de demande rempli par un non-admissible');
            }
        }
        if (!empty($erreurD)) {
            Logs::logger(2, 'Erreur dans le remplissage du formulaire de demande de logement');
        }
        if (isset($demande) && empty($erreurD)) { // Demande réussie : affichage de deux X pouvant les héberger
            $_SESSION['demande'] = $demande;
            $eleves = $eleveManager->getFavorite($demande, 2);
            if (empty($eleves)) {
                echo '<p>Désolé, aucune correspondance n\'a été trouvée (tous les élèves ont déjà été sollicités.<br/>Rendez-vous sur la page <a href=''>Bonnes adresses</a> pour trouver un hébergement à proximité de l\'école...</p>';
                Logs::logger(2, 'Plus aucun eleve disponible');
            } else {
                echo '<p>Voici les élèves qui te correspondent le mieux pour t\'héberger :</p>';
                echo '<table border=1 cellspacing=0>';
                echo '<tr><td>Nom d\'utilisateur</td><td>Sexe</td><td>Etablissement d\'origine</td><td>Filière</td><td>Section sportive</td><td>Contact</td></tr>';
                foreach ($eleves as $eleve) {
                    echo '<tr><td>'.$eleve->user().'</td><td>'.$eleve->sexe().'</td><td>'.$eleve->prepa().'</td><td>'.$eleve->filiere().'</td><td>'.$eleve->section().'</td>';
                    echo '<td><form action="index.php?page=admissible" method="post"><input type="hidden" name="user" value="'.$eleve->user().'"/><input type="submit" value="Envoyer une demande de logement"/></form></td></tr>';
                }
                echo '</table>';
            }
        } else { // Demande non remplie ou avec erreurs
            $prepas = $parametres->getList(Parametres::Etablissement);
            $filieres = $parametres->getList(Parametres::Filiere);
            $sections = $parametres->getList(Parametres::Section);
            
            if (isset($erreurD) && in_array(Demande::Non_Admissible, $erreurD)) echo '<span style="color:red;">Merci de vérifier vos informations personnelles : vous ne semblez pas être dans les listes d\'admissibilité !</span>'; ?>
            <form action="/admissible/inscription?action=demande" method="post">
            Nom : <input type="text" name="nom" value="<?php if (isset($demande)) { echo $demande->nom(); } ?>"/>
            <?php if (isset($erreurD) && in_array(Demande::Nom_Invalide, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Prénom : <input type="text" name="prenom" value="<?php if (isset($demande)) { echo $demande->prenom(); } ?>"/>
            <?php if (isset($erreurD) && in_array(Demande::Prenom_Invalide, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Adresse e-mail valide : <input type="text" name="email" value="<?php if (isset($demande)) { echo $demande->email(); } ?>"/>
            <?php if (isset($erreurD) && in_array(Demande::Email_Invalide, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Sexe : M <input type="radio" name="sexe" value="M" <?php if (!isset($demande) || $demande->sexe() == "M") { echo "checked='checked'"; } ?>/>/ F<input type="radio" name="sexe" value="F" <?php if (isset($demande) && $demande->sexe() == "F") { echo "checked='checked'"; } ?>/>
            <?php if (isset($erreurD) && in_array(Demande::Sexe_Invalide, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Etablissement d'origine : <select name="prepa">
                <?php
                foreach ($prepas as $value) {
                    if (isset($demande) && $demande->prepa() == $value['id']) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    echo '<option value="'.$value['id'].'" '.$selected.'>'.$value['ville'].' - '.$value['nom'].'</option>';
                }
                ?>
                <option value="-1">Autre</option>
            </select>
            <?php if (isset($erreurD) && in_array(Demande::Prepa_Invalide, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Filière : <select name="filiere">
            <?php
            foreach ($filieres as $value) {
                if (isset($demande) && $demande->filiere() == $value['id']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                echo '<option value="'.$value['id'].'" '.$selected.'>'.$value['nom'].'</option>';
            }
            ?>
            </select>
            <?php if (isset($erreurD) && in_array(Demande::Filiere_Invalide, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Série : <select name="serie">
            <?php
            foreach ($series as $value) {
                if (isset($demande) && $demande->serie() == $value['id']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                echo '<option value="'.$value['id'].'" '.$selected.'>'.$value['intitule'].' (du '.date("d.m.Y", $value['date_debut']).' au '.date("d.m.Y", $value['date_fin']).')</option>';
            }
            ?>
            </select>
            <?php if (isset($erreurD) && in_array(Demande::Serie_Invalide, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            Sport préféré : <select name="section">
            <?php
            foreach ($sections as $value) {
                echo '<option value="'.$value['id'].'">'.$value['nom'].'</option>';
            }
            ?>
            </select>
            <?php if (isset($erreurD) && in_array(Demande::Sport_Invalide, $erreurD)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/><br/>
            <input type="submit" value="Rechercher un logement"/>
            </form>
            <?php
        }
    }
} else { // Page affichée 
    ?>
    <p>Cette interface vous permet de trouver un élève présent sur le campus pour vous héberger pendant la période des oraux.<br/>
Dès la sortie des listes d'admissibilités pour votre série et jusqu'à la veille du début des épreuves orales minuit, rendez-vous sur la page <a href="/admissible/inscription?action=demande">Faire une demande de logement</a>, 
remplissez le formulaire avec vos informations personnelles et envoyer votre demande auprès de l'élève qui vous correspond le mieux (même lycée de provenance, même filière ou autres...).<br/><br/>
Dès l'aceptation par l'élève concerné, vous recevrez un email de confirmation vous permettant de prendre contact avec lui pour organiser votre arrivée.<br/>
Si votre demande semble prendre trop de temps pour être acceptée par l'élève que vous avez choisi, annulez votre première demande et remplissez-en une autre...<br/><br/>
</p>
    <?php
}
?>
<span id="page_id">12</span>