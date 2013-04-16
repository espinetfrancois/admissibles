<?php
/**
 * Page d'administration de la plate-forme d'hébergement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

require_once(APPLICATION_PATH.'/inc/fkz_auth.php');
$parametres = Registry::get('parametres');
$db = Registry::get('db');

// Identification
if (!isset($_SESSION['eleve']) && $_SESSION["administrateur"] !== true) {
    frankiz_do_auth("/administration/gestion");
    return;
}
//identification ok, affichage de l'interface
echo '<h2>Interface d\'administration</h2>';

// Gestion des listes de paramètres = lycées de provenance et filières d'entrée
if (isset($_GET['action']) && $_GET['action'] == 'param' && isset($_GET['type'])) {
    //vérification du type donné
    switch ($_GET['type']) {
        case Parametres::Etablissement:
            echo '<span id="page_id">43</span>';
            echo '<h3>Etablissements de provenance des élèves</h3>';
            echo '<p>Les élèves gardent la possibilité d\'entrer une autre valeur que celles proposées ci-dessous.</p>';
            $form = '<input type="text" name="ville" value="VILLE" size="10" maxlength="50"/> - <input type="text" name="nom" value="Nom de l\'établissement" size="30" maxlength="50"/>';
            break;

        case Parametres::Filiere:
            echo '<span id="page_id">44</span>';
            echo '<h3>Filières d\'entrée des élèves</h3>';
            $form = '<input type="text" name="nom" maxlength="50"/>';
            break;

        default:
            echo '<span id="page_id">4</span>';
            echo '<h3>Erreur de paramétrage...</h3>';
            //             Logs::logger(2, 'Corruption des parametres admin.php::GET type');
            throw new Exception_Page('Corruption des parametres admin.php::GET type', "L'url demandée n'est pas valide.", Exception_Page::WARNING);
            break;
    }

    // Suppression d'un élément de liste
    if (isset($_GET['suppr'])) {
        //check sur le type
        if (!is_numeric($_GET['suppr'])) {
            throw new Exception_Page('Corruption des parametres. admin.php::GET suppr', "L'url demandée n'est pas valide.", Exception_Page::FATAL_ERROR);
            return 1;
        }

        if (!$parametres->isUsedList($_GET['type'], $_GET['suppr'])) {
            $parametres->deleteFromList($_GET['type'], $_GET['suppr']);
            Logs::logger(1, 'Administrateur : Suppression d\'un element de liste');
        } else {
            Registry::get('layout')->addMessage('Vous ne pouvez supprimer cet élément tant qu\'il est utilisé dans le profil d\'un élève ou d\'un admissible', MSG_LEVEL_ERROR);
            Logs::logger(1, 'Administrateur : Tentative de suppression d\'un element de liste encore utilise');
        }
    }

    // Ajout d'un élément de liste (Etablissement)
    if (isset($_POST['nom']) && isset($_POST['ville'])) {
        //vérification du post
        if (!empty($_POST['nom']) && !empty($_POST['ville']) && strlen($_POST['nom']) <= 50 && strlen($_POST['ville']) <= 50) {
            $parametres->addToList($_GET['type'], array('nom' => $_POST['nom'], 'commune' => $_POST['ville']));
            Logs::logger(1, 'Administrateur : Ajout d\'un element a une liste');
        } else {
            Registry::get('layout')->addMessage('Erreur lors de l\'ajout d\'un nouvel élément', MSG_LEVEL_WARNING);
            Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'un element a une liste');
        }
    } elseif (isset($_POST['nom'])) { // Ajout d'un élément de liste (autre)
        //vérification du post
        if (!empty($_POST['nom']) && strlen($_POST['nom']) <= 50) {
            $parametres->addToList($_GET['type'], array('nom' => $_POST['nom']));
            Logs::logger(1, 'Administrateur : Ajout d\'un element a une liste');
        } else {
            Registry::get('layout')->addMessage('Erreur lors de l\'ajout d\'un nouvel élément', MSG_LEVEL_WARNING);
            Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'un element a une liste');
        }
    }

    $liste = $parametres->getList($_GET['type']);

    echo '<form action="/administration/gestion?action=param&type='.$_GET['type'].'" method="post">';
    echo '<table border=1 cellspacing=0>';
    echo '<thead><tr><th>Valeur</th><th>Action</th></tr></thead>';
    echo '<tbody>';
    foreach ($liste as $res) {
        if ($_GET['type'] == Parametres::Etablissement) {
            $res['nom'] = $res['ville']." - ".$res['nom'];
        }
        echo '<tr>';
            echo '<td>'.$res['nom'].'</td><td><a class="action" href="/administration/gestion?action=param&type='.$_GET['type'].'&suppr='.$res['id'].'">Supprimer</a></td>';
        echo '</tr>';
    }
    echo '<tr>';
    echo '<td>'.$form.'</td>';
    echo '<td><input type="submit" value="Ajouter"/></td>';
    echo '</tr></tbody>';
    echo '</table>';
    echo '</form>';

    //fin
    return;
}

// Modification des séries d'admissibilité
if (isset($_GET['action']) && $_GET['action'] == 'series') {
        // Suppression d'une série
    if (isset($_GET['suppr'])) {
        if (!is_numeric($_GET['suppr'])) {
            throw new Exception_Page('Corruption des parametres. admin.php::GET suppr', "L'url demandée n'est pas valide.", Exception_Page::FATAL_ERROR);
            return 1;
        }
        if (!$parametres->isUsedList(Parametres::Serie, $_GET['suppr'])) {
            $parametres->deleteFromList(Parametres::Serie, $_GET['suppr']);
            Logs::logger(1, 'Administrateur : Suppression d\'une serie');
        } else {
            Registry::get('layout')->addMessage('Vous ne pouvez supprimer cette série tant qu\'elle est utilisée dans le profil d\'un élève ou d\'un admissible', MSG_LEVEL_WARNING);
            Logs::logger(2, 'Administrateur : Tentative de suppression d\'une serie encore utilise');
        }
    }
    // Insertion d'une nouvelle série
    if (isset($_POST['intitule']) && isset($_POST['date_debut']) && isset($_POST['date_fin'])) {
        //vérification du post
        if (!empty($_POST['intitule']) && strlen($_POST['intitule']) <= 50
                && preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $_POST['date_debut'])
                && preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $_POST['date_fin'])) {

            $expDateD = explode('/', $_POST['date_debut']);
            $expDateF = explode('/', $_POST['date_fin']);
            $date_debut = mktime(0, 0, 0, $expDateD[1], $expDateD[0], $expDateD[2]);
            $date_fin = mktime(0, 0, 0, $expDateF[1], $expDateF[0], $expDateF[2]);
            /*
             * L'ouverture des demandes sera réglée lors de l'insertion de la liste des admissibles
             * La fermeture des demandes correspond à minuit la veille du début des oraux
             *
             */
            $parametres->addToList(Parametres::Serie, array('intitule' => $_POST['intitule'], 'date_debut' => $date_debut, 'date_fin' => $date_fin, 'ouverture' => $date_debut, 'fermeture' => $date_debut));
            Logs::logger(1, 'Administrateur : Ajout d\'une serie');
        } else {
            Registry::get('layout')->addMessage('Erreur lors de l\'ajout d\'une nouvelle série', MSG_LEVEL_ERROR);
            Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une serie');
        }
    }

    //interface pour l'ajout
    echo '<h3>Séries d\'admissibilité</h3>';
    echo '<span id="page_id">41</span>';
    $series = $parametres->getList(Parametres::Serie);
    echo '<form action="/administration/gestion?action=series" method="post">';
    echo '<table border=1 cellspacing=0>';
    echo '<thead><tr><th>Intitulé</th><th>Date de début des oraux</th><th>Date de fin des oraux</th><th>Action</th></tr></thead>';
    echo '<tbody>';
    foreach ($series as $value) {
        echo '<tr>';
            echo '<td>'.$value['intitule'].'</td>';
            echo '<td>'.date('d/m/Y', $value['date_debut']).'</td>';
            echo '<td>'.date('d/m/Y', $value['date_fin']).'</td>';
            echo '<td><a class="action" href="/administration/gestion?action=series&suppr='.$value['id'].'">Supprimer</a></td>';
        echo '</tr>';
    }
    echo '<tr>';
    echo '<td><input type="text" name="intitule"/></td>';
    echo '<td><input type="text" class="champ_date" name="date_debut" value="'.date('d/m/Y').'"/></td>';
    echo '<td><input type="text" class="champ_date" name="date_fin" value="'.date('d/m/Y').'"/></td>';
    echo '<td><input type="submit" value="Ajouter"/></td>';
    echo '</tr></tbody>';
    echo '</table>';
    echo '</form>';
    return;
}

// Modification des listes d'admissibilité
if (isset($_GET['action']) && $_GET['action'] == 'admissibles') {
    // Traitement de la liste ajoutée
    if (isset($_POST['serie']) && isset($_POST['filiere']) && isset($_POST['liste'])) {
        if (is_numeric($_POST['serie']) && is_numeric($_POST['filiere'])
                && preg_match("#^(.+\s\(.+\)(\r)?(\n)?)+$#", $_POST['liste'])) {
            $parametres->parseADM($_POST['serie'], $_POST['filiere'], $_POST['liste']);
            Registry::get('layout')->addMessage('Ajout des admissibles réussi !', MSG_LEVEL_OK);
            Logs::logger(1, 'Administrateur : Ajout d\'une liste d\'admissibilite');
        } else {
            Registry::get('layout')->addMessage('Mauvais formatage de la liste', MSG_LEVEL_WARNING);
            Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une liste d\'admissibilite');
        }
    }
    // Suppression d'un admissible
    if (isset($_GET['suppr'])) {
        if (is_numeric($_GET['suppr'])) {
            $parametres->supprAdmissible($_GET['suppr']);
            Logs::logger(1, 'Administrateur : Suppression d\'un admissible');
        } else {
//          Logs::logger(2, 'Administrateur : Erreur dans la suppression d\'un admissible');
            throw new Exception_Page('Administrateur : Erreur dans la suppression d\'un admissible', "L'url demandée n'existe pas", Exception_Page::ERROR);
        }
    }

    //interface de gestion
    echo '<h3>Insérer une liste d\'admissibilité</h3>';
    echo '<span id="page_id">46</span>';
    $filieres = $parametres->getList(Parametres::Filiere);
    $series = $parametres->getList(Parametres::Serie);
    $serie_valide = array();
    foreach ($series as $value) {
        // On ne considère que les séries non encore commencées
        if (time() < $value['fermeture']) {
            $serie_valide[] = $value;
        }
    }
    // On n'affiche le formulaire que si une série nécessite l'entrée d'une liste d'admissibilité
    if (!empty($serie_valide)) {
        echo '<p class="emph">Attention : l\'insertion d\'une liste d\'admissibilité marque l\'ouverture des demandes d\'hébergement pour la série considérée !</p>';
        echo '<form action="/administration/gestion?action=admissibles" method="post">';
        echo '<p class="champ"><label for="serie">Série d\'admissibilité : </label><select name="serie">';
        echo '<option value="" selected></option>';
        foreach ($serie_valide as $value) {
            echo '<option value="'.$value['id'].'">'.$value['intitule'].' (du '.date('d.m.Y', $value['date_debut']).' au '.date('d.m.Y', $value['date_fin']).')</option>';
        }
        echo '</select></p>';
        echo '<p class="champ"><label for="filiere">Filière : </label><select name="filiere">';
        echo '<option value=""></option>';
        foreach ($filieres as $value) {
            echo '<option value="'.$value['id'].'">'.$value['nom'].'</option>';
        }
        echo '</select></p>
              <p class="champ"><label for="liste">Liste des candidats reçus de la forme suivante :<br/>
              <i>Nom (Prénom)<br/>
              Nom (Prénom)<br/>
              Nom (Prénom)</i></label>
              <textarea name="liste" rows="10" cols="45"></textarea></p>
              <p class="champ">En validant ce formulaire, vous publiez cette liste d\'admissibilité et ouvrez les demandes d\'hébergement pour les admissibles :</p>
              <input type="submit" value="Valider" name="valider"/>
              </form>';
    } else {
        echo '<p>Aucune série ne nécessite l\'entrée de listes d\'admissibilité.</p>
              <p>Reportez-vous à la page "Séries d\'admissibilité" pour déclarer une nouvelle série...</p>';
    }
    ?>
    <hr/>
    <h3>Visualiser une liste d'admissibilité</h3>
    <form action="/administration/gestion?action=admissibles" method="post">
        <p class="champ"><label for="serie">Série d'admissibilité : </label><select name="serie-voir">
            <option value="" selected></option>
    <?php
    foreach ($serie_valide as $value) {
        echo '<option value="'.$value['id'].'">'.$value['intitule'].' (du '.date('d.m.Y', $value['date_debut']).' au '.date('d.m.Y', $value['date_fin']).')</option>';
        $series[$value['id']] = $value['intitule'];
    }
    ?>
    </select></p>
    <p class="champ"><label for="filiere">Filière : </label><select name="filiere-voir">
        <option value=""></option>
    <?php
    foreach ($filieres as $value) {
        echo '<option value="'.$value['id'].'">'.$value['nom'].'</option>';
        $fil[$value['id']] = $value['nom'];
    }
    ?>
        </select></p><br/>
        <input type="submit" value="Voir" name="valider"/>
    </form>
    <?php
    if (isset($_POST['serie-voir']) && isset($_POST['filiere-voir'])) { // Affichage des admissibles
        if (is_numeric($_POST['serie-voir']) && is_numeric($_POST['filiere-voir'])) {
            $admissibles = $parametres->getAdmissibles($_POST['serie-voir'], $_POST['filiere-voir']);
            echo '<p>Serie : '.$series[$_POST['serie-voir']].', Filière : '.$fil[$_POST['filiere-voir']].'</p>';
            echo '<table border="1" cellspacing="0" cellspadding="1">';
            echo '<thead><tr><th>Nom</th><th>Prénom</th><th>Mail (si inscrit)</th><th>Supprimer définitivement</th></tr></thead>';
            echo '<tbody>';
            if (count($admissibles) < 1)
                echo '<tr><td colspan="4">Aucun admissible</td></tr>';

            foreach ($admissibles as $admissible) {
                echo '<tr><td>'.$admissible['nom'].'</td><td>'
                    .$admissible['prenom'].'</td><td>'.$admissible['mail']
                    .'</td><td><a class="action" href="/administration/gestion?action=admissibles&suppr='
                    .$admissible['id'].'">Supprimer</a></td></tr>';
            }
            echo '</tbody></table>';
        } else {
            throw new Exception_Page('Administrateur : Corruption des parametres (numeric serie et filiere) admin.php', "Vous devez selectionner une serie valide", Exception_Page::ERROR);
//             Logs::logger(3, 'Administrateur : Corruption des parametres (numeric serie et filiere) admin.php');
        }
    }
    return;
}

// Interface de remise à zéro de la plate-forme
if (isset($_GET['action']) && $_GET['action'] == 'RAZ') {
    echo '<span id="page_id">48</span>';
    if (isset($_POST['raz']) && $_POST['raz']) {
        $parametres->remiseAZero();
        Registry::get('layout')->addMessage('Remise à zéro effectuée', MSG_LEVEL_OK);
        Logs::logger(1, 'Administrateur : Remise a zero de l\'interface effectuee');
    }
    ?>
    <p style="color:red;">Attention : la remise à zéro de l'interface est irréversible.</p>
    <p>Cette action efface toutes les informations relatives aux séries, aux admissibles, aux élèves, et aux demandes d'hébergement.</p>
    <form action="/administration/gestion?action=RAZ" method="post">
    <p class="champ" id="champ-raz"><label for="raz">Cocher cette case si vous êtes certain de vouloir effectuer une remise à zéro de l'interface :</label><input type="checkbox" name="raz"/></p>
    <input type="submit" value="Effectuer la remise à zéro"/>
    </form>
    <?php
    return;
}

// Administration des demandes en cours
if (isset($_GET['action']) && $_GET['action'] == 'demandes') {
    echo '<span id="page_id">42</span>';
    echo '<h3>Demandes en cours</h3>';
    $demandeManager = new DemandeManager(Registry::get('db'));
    $demandes = $demandeManager->getList();
    echo '<table border=1 cellspacing=0>';
    echo '<thead><tr><th>Série</th><th>Filière</th><th>Admissible</th><th>Elève X</th><th>Statut</th></tr></thead>';
    echo '<tbody>';
    if (count($demandes) < 1)
        echo '<tr><td colspan="5">Pas de demandes en cours</td></tr>';
    foreach ($demandes as $demande) {
        echo '<tr>';
        echo '<td>'.$demande->serie().'</td>';
        echo '<td>'.$demande->filiere().'</td>';
        echo '<td>'.$demande->nom().' '.$demande->prenom().'<br/>'.$demande->email().'</td>';
        echo '<td>'.$demande->userEleve().'</td>';
        echo '<td>'.$demande->status().'</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    return;
}

// Interface de gestion des hébergements à proximité du campus
if (isset($_GET['action']) && $_GET['action'] == 'hotel') {
    $adresseManager = new AdresseManager($db);
    // Suppression d'une catégorie
    if (isset($_GET['suppr_cat'])) {
        if (!is_numeric($_GET['suppr_cat'])) {
            throw new Exception_Page('Corruption des parametres. admin.php::GET', 'Selectionnez une catégorie hebergement pour le supprimer', Exception_Page::ERROR);
//             Logs::logger(3, 'Corruption des parametres. admin.php::GET');
        }
        if (!$adresseManager->isUsedCat($_GET['suppr_cat'])) {
            $adresseManager->deleteCategorie($_GET['suppr_cat']);
            Logs::logger(1, 'Administrateur : Suppression d\'une categorie d\'adresse');
        } else {
            Registry::get('layout')->addMessage('Vous ne pouvez supprimer cette catégorie tant qu\'elle contient des adresses', MSG_LEVEL_WARNING);
            Logs::logger(2, 'Administrateur : Tentative de suppression d\'une categorie d\'adresse encore utilisee');
        }
    }

    // Suppression d'une annonce
    if (isset($_GET['suppr'])) {
        if (!is_numeric($_GET['suppr'])) {
            throw new Exception_Page('Corruption des parametres. admin.php::GET', 'Selectionnez un hebergement pour le supprimer', Exception_Page::ERROR);
        }
        $adresseManager->delete($_GET['suppr']);
        Logs::logger(1, 'Administrateur : Suppression d\'une adresse');
    }

    // Ajout d'une catégorie
    if (isset($_POST['nom_cat'])) {
        if (!empty($_POST['nom_cat']) && strlen($_POST['nom_cat']) <= 100) {
            $adresseManager->addCategorie($_POST['nom_cat']);
            Logs::logger(1, 'Administrateur : Ajout d\'une adresse');
            Registry::get('layout')->addMessage('Adresse ajoutée avec succés', MSG_LEVEL_OK);
        } else {
            Registry::get('layout')->addMessage('Erreur lors de l\'ajout d\'une nouvelle catégorie', MSG_LEVEL_ERROR);
            Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une categorie d\'adresses');
        }
    }

    // Ajout ou Modification d'une annonce
    if (isset ($_POST['nom'])) {
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
            Logs::logger(1, 'Administrateur : Ajout d\'une adresse');
        } else {
            $erreurModif = $adresse->erreurs();
            Registry::get('layout')->addMessage('Erreur dans le remplissage du formulaire d\'ajout d\'une adresses', MSG_LEVEL_WARNING);
            Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une adresses');
        }
    }

    $categories = $adresseManager->getCategories();

    //interface de gestion
    echo '<h3>Gestion de la liste des hébergements à proximité de l\'école</h3>';

    // Interface de modification d'une adresse
    if (isset($_GET['ajout']) || isset($_GET['modif']) || isset($erreurModif)) {
        if (isset($_GET['modif'])) {
            $adresse = $adresseManager->getUnique($_GET['modif']);
        }
        $champInvalide = '<span class"error">Champ invalide</span>';
        ?>
            <form action="/administration/gestion?action=hotel" method="post">
            <p class="champ"><label for="nom">Nom : </label><input type="text" name="nom" value="<?php if (isset($adresse)) { echo $adresse->nom(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::Nom_Invalide, $erreurModif)) echo $champInvalide ?></p>
            <p class="champ"><label for="adresse">Adresse : </label><input type="text" name="adresse" value="<?php if (isset($adresse)) { echo $adresse->adresse(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::Adresse_Invalide, $erreurModif)) echo $champInvalide; ?></p>
            <p class="champ"><label for="tel">Téléphone : </label><input type="text" name="tel" value="<?php if (isset($adresse)) { echo $adresse->tel(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::Tel_Invalide, $erreurModif)) echo $champInvalide; ?></p>
            <p class="champ"><label for="email">Email : </label><input type="text" name="email" value="<?php if (isset($adresse)) { echo $adresse->email(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::Email_Invalide, $erreurModif)) echo $champInvalide; ?></p>
            <p class="champ"><label for="description">Description : </label><?php if (isset($erreurModif) && in_array(Adresse::Description_Invalide, $erreurModif)) echo $champInvalide; ?>
            <textarea name="description" cols="20" rows="4"><?php if (isset($adresse)) { echo $adresse->description(); } ?></textarea></p>
            <p class="champ"><label for="categorie">Catégorie : </label><select name="categorie">
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

        if (isset($adresse) && $adresse->valide() == "1") {
            $checked = 'checked';
        } else {
            $checked = '';
        }

        ?>
        </select> <?php if (isset($erreurModif) && in_array(Adresse::Categorie_Invalide, $erreurModif)) echo $champInvalide; ?></p>
        <p class="champ"><label for="valide">Afficher cette annonce sur le site ? </label><input type="checkbox" name="valide" <?php echo $checked; ?>/> <?php if (isset($erreurModif) && in_array(Adresse::Valide_Invalide, $erreurModif)) echo $champInvalide; ?></p>
        <?php
        if(isset($adresse) && !$adresse->isNew()) {
            echo '<input type="hidden" name="id" value="'.$adresse->id().'" />';
            echo '<input type="submit" value="Modifier l\'annonce"/>';
        } else {
            echo '<input type="submit" value="Ajouter l\'annonce" />';
        }

        echo '</form>';

    } else {
        echo '<p><a href="/admissible/adresses" target="_blank">Voir la page publique affichant les adresses</a></p>';
        // Gestion des catégories
        echo '<h4>Catégories d\'hébergement</h4>';

        echo '<form action="/administration/gestion?action=hotel" method="post">';
        echo '<table border=1 cellspacing=0>';
        echo '<thead><tr><th>Catégories</th><th>Action</th></tr></thead>';
        echo '<tbody>';

        if (count($categories) < 1)
        	echo '<tr><td colspan="3">Aucune catégorie</td></tr>';

        foreach ($categories as $value) {
            echo '<tr>';
                echo '<td>'.$value['nom'].'</td></td>';
                echo '<td><a class="action" href="/administration/gestion?action=hotel&suppr_cat='.$value['id'].'">Supprimer</a></td>';
            echo '</tr>';
        }
        echo '<tr>';
            echo '<td><input type="text" name="nom_cat"/></td>';
            echo '<td><input type="submit" value="Ajouter"/></td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</form>';

        // Gestion des adresses affichées
        echo '<h4>Adresses affichées actuellement sur le site</h4>';
        echo '<p><a href="/administration/gestion?action=hotel&ajout=1">Ajouter une annonce</a></p>';
        $adressesValides = $adresseManager->getListAffiche();
        echo '<table border=1 cellspacing=0>';
        echo '<thead><tr><th>Annonce comme affichée</th><th>Catégorie</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        if (count($adressesValides) < 1)
        	echo '<tr><td colspan="3">Aucune annonce</td></tr>';

        foreach ($adressesValides as $adresse) {
            echo '<tr>';
                echo '<td>'.$adresse->nom().'<br/>'.$adresse->adresse().'<br/>Tél : '.$adresse->tel().'<br/>Mail : '.$adresse->email().'<br/>'.nl2br($adresse->description()).'</td>';
                echo '<td>'.$adresse->categorie().'</td>';
                echo '<td>
                    <a class="action" href="/administration/gestion?action=hotel&modif='.$adresse->id().'">Modifier</a>
                    <a class="action" href="/administration/gestion?action=hotel&suppr='.$adresse->id().'">Supprimer</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        // Gestion des adresses à valider
        echo '<h4>Adresses non affichées (proposées par les élèves)</h4>';
        echo '<p>Pour valider une annonce, cliquez sur Modifier et cocher la case correspondannte</p>';
        $adressesValides = $adresseManager->getListAffiche(0);
        echo '<table border=1 cellspacing=0>';
        echo '<thead><tr><th>Annonce comme affichée</th><th>Catégorie</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        if (count($adressesValides) < 1)
            echo '<tr><td colspan="3">Aucune annonce</td></tr>';

        foreach ($adressesValides as $adresse) {
            echo '<tr>';
                echo '<td>'.$adresse->nom().'<br/>'.$adresse->adresse().'<br/>Tél : '.$adresse->tel().'<br/>Mail : '.$adresse->email().'<br/>'.nl2br($adresse->description()).'</td>';
                echo '<td>'.$adresse->categorie().'</td>';
                echo '<td>
                    <a class="action" href="/administration/gestion?action=hotel&modif='.$adresse->id().'">Modifier</a>
                    <a class="action" href="/administration/gestion?action=hotel&suppr='.$adresse->id().'">Supprimer</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        echo '<span id="page_id">47</span>';
    }
    return;
}

//interface de gestion basique
?>
<ul>
    <li><a href="/deconnexion">Se déconnecter</a></li>
    <li><a href="/administration/gestion?action=RAZ">Remise à zéro de l'interface d'hébergement</a></li>
    <li><a href="/administration/gestion?action=series">Modifier les séries d'admissibilités (dates d'ouverture du site)</a></li>
    <li><a href="/administration/gestion?action=param&type=<?php echo Parametres::Etablissement; ?>">Modifier les établissements de provenance des élèves</a></li>
<li><a href="/administration/gestion?action=param&type=<?php echo Parametres::Filiere; ?>">Modifier les filières d'entrée des élèves</a></li>
    <li><a href="/administration/gestion?action=admissibles">Entrer la liste des admissibles pour la prochaine série</a></li>
    <li><a href="/administration/gestion?action=demandes">Voir les demandes en cours</a></li>
    <li><a href="/administration/gestion?action=hotel">Modifier la liste des hébergements à proximitè de l'école</a></li>
</ul>
<span id="page_id">4</span>