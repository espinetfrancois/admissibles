<?php
/**
 * Page de gestion des demandes d'hébergement des admissibles
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

$demandeManager = new Manager_Demande(Registry::get('db'));
$eleveManager = new Manager_Eleve(Registry::get('db'));
$parametres = Registry::get('parametres');

echo '<span id="page_id">11</span>';
echo '<h2>Demande d\'hébergement chez un élève pendant la période des oraux</h2>';

//gestion de la validation de la demande une fois postée
if (isset($_SESSION['demande']) && isset($_POST['user'])) {
    try {
        $dispos = $eleveManager->getDispo($_POST['user']);
    } catch (Exception_Bdd $e) {
        //rethrow ?
        $dispos = array();
        Registry::get('layout')->addMessage('Impossible de récupérer la liste des disponibilités de l\'élève choisi.', MSG_LEVEL_ERROR);
    }
    $demande = unserialize($_SESSION['demande']);
    if (!in_array($demande->serie(), $dispos)) {
        echo '<p>Désolé, l\'élève que vous avez choisi vient d\'être sollicité. Merci de reitérer votre recherche.</p>';
        Logs::logger(2, 'Demandes d\'admissibles simultannees sur l\'eleve '.$_POST['user']);
    } else if (!$demandeManager->autorisation($demande)) {
        echo '<p>Désolé, vous avez déjà effectué une demande d\'hébergement. Merci d\'attendre la réponse de l\'élève ou d\'annuler votre demande.</p>';
        Logs::logger(2, 'Tentative de sur-demande de l\'admissible '.$demande->id());
    } else {
        $demande->setUserEleve($_POST['user']);
        $demande->setStatus('0');
        $demande->setCode(md5(sha1(time().$demande->email())));

        $mail = new Mail_Admissible($demande->nom(), $demande->prenom(), $demande->email());
        $mail->demandeEnvoyee($demande->userEleve(), '/admissible/annulation-demande?code='.$demande->code(), '/admissible/validation-demande?code='.$demande->code());
        try {
            $demandeManager->add($demande);
            $eleveManager->deleteDispo($_POST['user'], $demande->serie());
            Logs::logger(1, 'Demande de logement '.$demande->id().' effectuee');
            Registry::get('layout')->addMessage('Votre demande de logement est prête à être envoyée.', MSG_LEVEL_OK);
            $success = 1;
        } catch (Exception_Bdd $e) {
            Registry::get('layout')->addMessage('Impossible d\'ajouter votre demande dans la base de données.', MSG_LEVEL_ERROR);
        }
    }
}

//interface pour poser une nouvelle demande
if (isset($_GET['action']) && $_GET['action'] == 'demande') {
    try {
        $series = $parametres->getCurrentSeries();
    } catch (Exception_Bdd $e) {
        throw new Exception_Page('Impossible de récupérer la liste des séries actives', 'Un problème est survenu lors de la récupération de la liste des séries.', null, $e);
    }

    // Interface fermée aux demandes
    if (empty($series)) {
        echo '<p>Les demandes ne sont pas encore ouvertes pour la prochaine série...</p>';
        return;
    }

    //on regarde le bom de l'admissible pour voir s'il est admissible
    if (isset($_POST['nom'])) {
        unset($erreurD);
        $demande = new Model_Demande(array('nom' => $_POST['nom'],
                                     'prenom' => $_POST['prenom'],
                                     'email' => $_POST['email'],
                                     'sexe' => $_POST['sexe'],
                                     'sport' => $_POST['section'],
                                     'prepa' => $_POST['prepa'],
                                     'filiere' => $_POST['filiere'],
                                     'serie' => $_POST['serie']));
        $erreurD = $demande->erreurs();
        try {
            $id = $demandeManager->isAdmissible($_POST['nom'], $_POST['prenom'], $_POST['serie']);
        } catch (Exception_Bdd $e) {
            Registry::get('layout')->addMessage('Impossible de vérifier votre admissibilité dans la base de données.', MSG_LEVEL_ERROR);
        }
        if ($id == -1) {
            $erreurD[] = Model_Demande::Non_Admissible;
            Logs::logger(2, 'Formulaire de demande rempli par un non-admissible');
        } else {
            $demande->setId($id);
        }
    }

    if (!empty($erreurD)) {
        Registry::get('layout')->addMessage('Erreur dans le remplissage du formulaire de demande de logement.', MSG_LEVEL_WARNING);
        Logs::logger(2, 'Erreur dans le remplissage du formulaire de demande de logement');
    }

    // Demande réussie : affichage de deux X pouvant les héberger
    if (isset($demande) && empty($erreurD)) {
        $_SESSION['demande'] = serialize($demande);
        try {
            $eleves = $eleveManager->getFavorite($demande, 2);
        } catch (Exception_Bdd $e) {
            throw new Exception_Page('Erreur lors de la requête en base pour trouver les élèves.', 'Impossible de trouver des élèves dans la base de données.', null, $e);
        }
        if (empty($eleves)) {
            echo '<p>Désolé, aucune correspondance n\'a été trouvée (tous les élèves ont déjà été sollicités).<br/>Rendez-vous sur la page <a href=\'\'>Bonnes adresses</a> pour trouver un hébergement à proximité de l\'école...</p>';
            throw new Exception_Page('Plus aucun eleve disponible.', 'Aucun élève n\'a été trouvé.', Exception_Page::WARNING);
            return;
        }
        //on affiche les élève disponible
        echo '<p>Voici les élèves qui te correspondent le mieux pour t\'héberger :</p>';
        echo '<table border=1 cellspacing=0>';
        echo '<thead><tr><th>Nom d\'utilisateur</th><th>Genre</th>
                         <th>Etablissement d\'origine</th>
                         <th>Filière</th><th>Section sportive</th>
                         <th>Contact</th></tr></thead>';
        echo '<tbody>';
        foreach ($eleves as $eleve) {
            echo '<tr><td>'.preg_replace("#^([a-z0-9_-])[a-z0-9_-]*\.((de-)?[a-z0-9]*)[a-z0-9_-]*$#","$1.$2", $eleve->user())
                .'</td><td>'.$eleve->sexe().'</td><td>'
                .$eleve->prepa().'</td><td>'.$eleve->filiere()
                .'</td><td>'.$eleve->section().'</td>';
            echo '<td>
                    <form class="inline" action="/admissible/inscription" method="post">
                    <input type="hidden" name="user" value="'.$eleve->user().'"/>
                    <input type="submit" value="Envoyer une demande de logement"/>
                    </form>
                  </td></tr>';
        }
        echo '</tdbody></table>';
        return;
    }

    // Demande non remplie ou avec erreurs
    $prepas = $parametres->getList(Parametres::Etablissement);
    $filieres = $parametres->getList(Parametres::Filiere);
    $sections = $parametres->getList(Parametres::Section);

    $champInvalide = '<span class="error">Champ invalide</span>';

    if (isset($erreurD) && in_array(Model_Demande::Non_Admissible, $erreurD))
        Registry::get('layout')->addMessage('Merci de vérifier vos informations personnelles : vous ne semblez pas être dans les listes d\'admissibilité !', MSG_LEVEL_ERROR);
    ?>

    <form action="/admissible/inscription?action=demande" method="post">

    <p class="champ">
        <label for="nom">Nom : </label>
        <input type="text" name="nom" value="<?php
            echo (isset($demande) ? $demande->nom() : '').'"';
            echo ( (isset($erreurD) && in_array(Model_Demande::Nom_Invalide, $erreurD)) ? 'class="error" />'.$champInvalide : '/>');
        ?>
    </p>
    <p class="champ">
        <label for="prenom">Prénom : </label>
        <input type="text" name="prenom" value="<?php
            echo (isset($demande) ? $demande->prenom() : '').'"';
            echo ( (isset($erreurD) && in_array(Model_Demande::Prenom_Invalide, $erreurD)) ? 'class="error" />'.$champInvalide : '/>');
        ?>
    </p>
    <p class="champ">
        <label for="email">Adresse e-mail valide : </label>
        <input type="text" name="email" value="<?php
            echo (isset($demande) ? $demande->email() : '').'"';
            echo (( isset($erreurD) && in_array(Model_Demande::Email_Invalide, $erreurD) ) ? 'class="error" />'.$champInvalide : '/>');
        ?>
    </p>
    <p id="champ-sexe" class="champ radio">
        <label for="sexe">Genre: </label>
        <label> Masculin <input type="radio" name="sexe" value="M"<?php if (!isset($demande) || $demande->sexe() == 'M') { echo 'checked="checked"'; } ?>/></label>
        <label>Féminin<input type="radio" name="sexe" value="F"<?php if (isset($demande) && $demande->sexe() == 'F') { echo 'checked="checked"'; } ?>/></label>
    <?php if (isset($erreurD) && in_array(Model_Demande::Sexe_Invalide, $erreurD)) echo $champInvalide; ?></p>
    <p class="champ">
        <label for="prepa">Etablissement d'origine : </label>
        <select name="prepa">
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
        </select>
        <?php if (isset($erreurD) && in_array(Model_Demande::Prepa_Invalide, $erreurD)) echo $champInvalide; ?>
    </p>
    <p class="champ">
        <label for="filiere">Filière : </label>
        <select name="filiere">
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
        <?php if (isset($erreurD) && in_array(Model_Demande::Filiere_Invalide, $erreurD)) echo $champInvalide; ?>
    </p>
    <p class="champ">
        <label for="serie">Série : </label>
        <select name="serie">
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
        <?php if (isset($erreurD) && in_array(Model_Demande::Serie_Invalide, $erreurD)) echo $champInvalide; ?>
    </p>
    <p class="champ"><label for="section">Sport préféré : </label>
        <select name="section">
        <?php
        foreach ($sections as $value) {
            echo '<option value="'.$value['section'].'">'.$value['section'].'</option>';
        }
        ?>
        </select>
        <?php if (isset($erreurD) && in_array(Model_Demande::Sport_Invalide, $erreurD)) echo $champInvalide; ?>
    </p>
    <p class="mentions-legales champ"><a href="/mentions-legales" target="_blank">Consultez les mentions légales</a></p>
    <br/>
    <input type="submit" value="Rechercher un logement"/>

    </form>
    <?php
    return;
}
if (isset($success) && $success == 1) {
    echo '<p>Pour poursuivre votre demande et afin de vérifier votre identité, merci de cliquer sur le lien qui vous a été envoyé par mail.</p>';
    return;
}
//page par défaut
?>
<h3>Détail du fonctionnement</h3>
<p class="emph">Ce service d'hébergement permet seulement de vous mettre en
    relation avec un élève pouvant vous accueillir. En aucun cas l'École Polytechnique
    ne pourrait être tenue responsable si la qualité de l'accueil, du logement ou du sommeil
    vous dérangeait pendant vos oraux. Bien que l'élève s'engage à vous accueillir lors de son
    inscription sur le site, l'École ne garantit pas qu'il le fera effectivement. Enfin, l'hébergement
    est fourni à titre gracieux par les élèves de l'École, aucune rétribution financière ne pourra vous
    être demandée pour le logement.</p>
<p>Vous pouvez vous inscrire dès la sortie des listes
    d'admissibilités pour votre série et jusqu'à la veille du début des
    épreuves orales minuit.</p>
<p>La procédure commence dès que vous avez déposé votre demande sur
    le site. Pour s'inscrire, une seule chose à faire : indiquer quelques
    informations personnelles sur le formulaire d'inscription, elles
    servent à vous proposez un élève avec qui vous pourriez avoir le plus
    d'affinités, vous choississez dans cette liste la personne qui vous
    convient le plus. Ensuite :</p>
<ul>
    <li>Vous recevrez ensuite un email de confirmation. Et votre
        demande sera transmise à l'élève que vous aurez choisi.</li>
    <li>Vous recevrez ensuite un email lorsque l'élève polytechnicien
        aura accepté votre demande, et vous pourrez alors contacter la
        personne correspondante pour arranger les modalités de votre séjour.</li>
</ul>
<p>Vous pouvez à tout moment annuler votre demande par le biais du
    lien fournit dans le mail : Si votre demande semble prendre trop de
    temps pour être acceptée par l'élève que vous avez choisi, annulez
    votre première demande et remplissez-en une autre...</p>
<p>Pour commencer le processus, cliquez
<a href='/admissible/inscription?action=demande'>ici</a>.</p>

<p>Remarque : un sport préféré vous sera demandé, il s'agit juste de vous proposer des élèves avec qui vous pourriez avoir plus d'affinités.</p>
<!-- <span id="page_id">11</span> -->
<!-- <span id="page_id">12</span> -->