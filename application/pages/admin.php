<?php
/**
 * Page d'administration de la plate-forme d'hébergement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 * @todo identification LDAP
 */

include_once(APPLICATION_PATH.'/inc/sql.php');

// Identification
if (isset($_POST['user']) && !empty($_POST['user']) && !empty($_POST['pass']))
{
    if (in_array($_POST['user'], $parametres->getAdmin()) && true) { // LDAP
        $_SESSION['administrateur'] = true;
        Logs::logger(1, 'Connexion a l\'interface d\'administration reussie');
    }
    else {
        $erreurID = 1;
        Logs::logger(3, 'Tentative de connexion a l\'interface d\'administration echouee'); // Alerte de sécurité de niveau 3
    }
}


// Interface de connexion
if (!isset($_SESSION['administrateur']) || (isset($_GET['action']) && $_GET['action'] == 'deconnect')) {
session_destroy();
Logs::logger(1, 'Deconnexion administrateur');
?>
    <h2>Connexion</h2>
    <?php if (isset($erreurID)) { echo '<p style="color:red;">Erreur d\'identification !</p>'; } ?>
    <form action="/administration/gestion" method="post">
    <p class="champ"><label for="user">Utilisateur : </label><input type="text" name="user"/></p>
    <p class="champ"><label for="pass">Mot de passe : </label><input type="password" name="pass"/></p>
    <br/>
    <input type="submit" value="Se connecter"/>
    </form>
<?php
}
else {
    echo '<h2>Interface d\'administration</h2>';
    if (isset($_GET['action']) && $_GET['action'] == 'param' && isset($_GET['type'])) { // Gestion des listes de paramètres
        echo '<a href="/administration/gestion">Retour à l\'accueil</a>';
        switch ($_GET['type']) {
            case Parametres::Promo:
                echo '<span id="page_id">42</span>';
                echo '<h2>Promotions présentes sur le platâl</h2>';
                $form = '<input type="text" name="nom" maxlength="50"/>';
                break;

            case Parametres::Etablissement:
                echo '<span id="page_id">43</span>';
                echo '<h2>Etablissements de provenance des élèves</h2><p>Les élèves gardent la possibilité d\'entrer une autre valeur que celles proposées ci-dessous.</p>';
                $form = '<input type="text" name="ville" value="VILLE" size="10" maxlength="50"/> - <input type="text" name="nom" value="Nom de l\'établissement" size="30" maxlength="50"/>';
                break;

            case Parametres::Filiere:
                echo '<span id="page_id">44</span>';
                echo '<h2>Filières d\'entrée des élèves</h2>';
                $form = '<input type="text" name="nom" maxlength="50"/>';
                break;

            case Parametres::Section:
                echo '<span id="page_id">45</span>';
                echo '<h2>Sections sportives</h2>';
                $form = '<input type="text" name="nom" maxlength="50"/>';
                break;

            default:
                $erreurP = 1;
                echo '<span id="page_id">4</span>';
                echo '<h2>Erreur de paramétrage...</h2>';
                Logs::logger(2, 'Corruption des parametres admin.php::GET type');
                break;
        }
        if (!isset($erreurP)) { // Si aucune erreure de paramétrage
            if (isset($_GET['suppr'])) { // Suppression d'un élément de liste
                if (!is_numeric($_GET['suppr'])) {
                    Logs::logger(3, 'Corruption des parametres. admin.php::GET');
                }
                if (!$parametres->isUsedList($_GET['type'], $_GET['suppr'])) {
                    $parametres->deleteFromList($_GET['type'], $_GET['suppr']);
                    Logs::logger(1, 'Administrateur : Suppression d\'un element de liste');
                } else {
                    $erreurA = 'Vous ne pouvez supprimer cet élément tant qu\'il est utilisé dans le profil d\'un élève ou d\'un admissible';
                    Logs::logger(1, 'Administrateur : Tentative de suppression d\'un element de liste encore utilise');
                }
            }
            if (isset($_POST['nom']) && isset($_POST['ville'])) { // Ajout d'un élément de liste (Etablissement)
                if (!empty($_POST['nom']) && !empty($_POST['ville']) && strlen($_POST['nom']) <= 50 && strlen($_POST['ville']) <= 50) {
                    $parametres->addToList($_GET['type'], array('nom' => $_POST['nom'], 'commune' => $_POST['ville']));
                    Logs::logger(1, 'Administrateur : Ajout d\'un element a une liste');
                } else {
                    $erreurA = 'Erreur lors de l\'ajout d\'un nouvel élément';
                    Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'un element a une liste');
                }
            } elseif (isset($_POST['nom'])) { // Ajout d'un élément de liste (autre)
                if (!empty($_POST['nom']) && strlen($_POST['nom']) <= 50) {
                    $parametres->addToList($_GET['type'], array('nom' => $_POST['nom']));
                    Logs::logger(1, 'Administrateur : Ajout d\'un element a une liste');
                } else {
                    $erreurA = 'Erreur lors de l\'ajout d\'un nouvel élément';
                    Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'un element a une liste');
                }
            }
            $liste = $parametres->getList($_GET['type']);
            echo '<span style="color:red;">'.@$erreurA.'</span>';
            echo '<form action="/administration/gestion?action=param&type='.$_GET['type'].'" method="post">';
            echo '<table border=1 cellspacing=0>';
            echo '<tr><td>Valeur</td><td>Action</td></tr>';
            foreach ($liste as $res) {
                if ($_GET['type'] == Parametres::Etablissement) {
                    $res['nom'] = $res['ville']." - ".$res['nom'];
                }
                echo '<tr>';
                    echo '<td>'.$res['nom'].'</td><td><a href="/administration/gestion?action=param&type='.$_GET['type'].'&suppr='.$res['id'].'">Suppr</a></td>';
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td>'.$form.'</td>';
            echo '<td><input type="submit" value="Ajouter"/></td>';
            echo '</tr>';
            echo '</table>';
            echo '</form>';

        }
    } elseif (isset($_GET['action']) && $_GET['action'] == 'series') { // Modification des séries d'admissibilité
        if (isset($_GET['suppr'])) { // Suppression d'une série
            if (!is_numeric($_GET['suppr'])) {
                Logs::logger(3, 'Corruption des parametres. admin.php::GET');
            }
            if (!$parametres->isUsedList(Parametres::Serie, $_GET['suppr'])) {
                $parametres->deleteFromList(Parametres::Serie, $_GET['suppr']);
                Logs::logger(1, 'Administrateur : Suppression d\'une serie');
            } else {
                $erreurA = 'Vous ne pouvez supprimer cette série tant qu\'elle est utilisée dans le profil d\'un élève ou d\'un admissible';
                Logs::logger(2, 'Administrateur : Tentative de suppression d\'une serie encore utilise');
            }
        }
        if (isset($_POST['intitule']) && isset($_POST['date_debut']) && isset($_POST['date_fin'])) { // Insertion d'une nouvelle série
            if (!empty($_POST['intitule']) && strlen($_POST['intitule']) <= 50 && preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $_POST['date_debut']) && preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $_POST['date_fin'])) {
                $expDateD = explode('/', $_POST['date_debut']);
                $expDateF = explode('/', $_POST['date_fin']);
                $date_debut = mktime(0, 0, 0, $expDateD[1], $expDateD[0], $expDateD[2]);
                $date_fin = mktime(0, 0, 0, $expDateF[1], $expDateF[0], $expDateF[2]);
                // L'ouverture des demandes sera réglée lors de l'insertion de la liste des admissibles
                // La fermeture des demandes correspond Ã  minuit la veille du début des oraux
                $parametres->addToList(Parametres::Serie, array('intitule' => $_POST['intitule'], 'date_debut' => $date_debut, 'date_fin' => $date_fin, 'ouverture' => $date_debut, 'fermeture' => $date_debut));
                Logs::logger(1, 'Administrateur : Ajout d\'une serie');
            } else {
                $erreurA = 'Erreur lors de l\'ajout d\'une nouvelle série';
                Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une serie');
            }
        }
        echo '<a href="/administration/gestion">Retour à l\'accueil</a>';
        echo '<h2>Séries d\'admissibilité</h2>';
        echo '<span id="page_id">41</span>';
        $series = $parametres->getList(Parametres::Serie);
        echo '<span style="color:red;">'.@$erreurA.'</span>';
        echo '<form action="/administration/gestion?action=series" method="post">';
        echo '<table border=1 cellspacing=0>';
        echo '<tr><td>Intitulé</td><td>Date de début des oraux</td><td>Date de fin des oraux</td><td>Action</td></tr>';
        foreach ($series as $value) {
            echo '<tr>';
                echo '<td>'.$value['intitule'].'</td></td>';
                echo '<td>'.date('d/m/Y', $value['date_debut']).'</td>';
                echo '<td>'.date('d/m/Y', $value['date_fin']).'</td>';
                echo '<td><a href="/administration/gestion?action=series&suppr='.$value['id'].'">Suppr</a></td>';
            echo '</tr>';
        }
        echo '<tr>';
        echo '<td><input type="text" name="intitule"/></td>';
        echo '<td><input type="text" name="date_debut" value="00/00/0000"/></td>';
        echo '<td><input type="text" name="date_fin" value="00/00/0000"/></td>';
        echo '<td><input type="submit" value="Ajouter"/></td>';
        echo '</tr>';
        echo '</table>';
        echo '</form>';
    } elseif (isset($_GET['action']) && $_GET['action'] == 'admissibles') { // Modification des listes d'admissibilité
        if (isset($_POST['serie']) && isset($_POST['filiere']) && isset($_POST['liste'])) { // Traitement de la liste ajoutée
            if (is_numeric($_POST['serie']) && is_numeric($_POST['filiere']) && preg_match("#^(.+\s\(.+\)(\r)?(\n)?)+$#", $_POST['liste'])) {
                $parametres->parseADM($_POST['serie'],$_POST['filiere'],$_POST['liste']);
                $erreurA = 'Ajout des admissibles réussi !';
                Logs::logger(1, 'Administrateur : Ajout d\'une liste d\'admissibilite');
            } else {
                $erreurA = 'Mauvais formatage de la liste';
                Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une liste d\'admissibilite');
            }
        }
        echo '<a href="/administration/gestion">Retour à l\'accueil</a>';
        echo '<h2>Insertion d\'une liste d\'admissibilité</h2>';
        echo '<span id="page_id">46</span>';
        echo '<span style="color:red;">'.@$erreurA.'</span>';
        $filieres = $parametres->getList(Parametres::Filiere);
        $series = $parametres->getList(Parametres::Serie);
		$serie_valide = array();
		foreach ($series as $value) {
			if (time() < $value['fermeture']) { // On ne considère que les séries non encore commencées
			    $serie_valide[] = $value;
			}
		}
		if (!empty($serie_valide)) { // On n'affiche le formulaire que si une série nécessite l'entrée d'une liste d'admissibilité
		    echo '<p>Attention : l\'insertion d\'une liste d\'admissibilité marque l\'ouverture des demandes d\'hébergement pour la série considérée !</p>';
			?>
			<form action="/administration/gestion?action=admissibles" method="post">
				<p class="champ"><label for="serie">Série d'admissibilité : </label><select name="serie">
					<option value="" selected></option>
			<?php
			foreach ($serie_valide as $value) {
				echo '<option value="'.$value['id'].'">'.$value['intitule'].' (du '.date('d.m.Y', $value['date_debut']).' au '.date('d.m.Y', $value['date_fin']).')</option>';
			}
			?>
				</select></p>
				<p class="champ"><label for="filiere">Filière : </label><select name="filiere">
					<option value=""></option>
			<?php
			foreach ($filieres as $value) {
				echo '<option value="'.$value['id'].'">'.$value['nom'].'</option>';
			}
			?>
				</select></p>
				<p class="champ"><label for="liste">Liste des candidats reçus de la forme suivante :<br/>
				<i>Nom (Prénom)<br/>
				Nom (Prénom)<br/>
				Nom (Prénom)</i></label></p>
				<br/>
				<textarea name="liste" rows="10" cols="45"></textarea>
				<br/>
				En validant ce formulaire, vous publiez cette liste d'admissibilité et ouvrez les demandes d'hébergement pour les admissibles :
				<input type="submit" value="Valider" name="valider"/>
			</form>
        	<?php
        } else {
			echo '<p>Aucune série ne nécessite l\'entrée de listes d\'admissibilité.<br/>Reportez-vous à la page "Séries d\'admissibilité" pour déclarer une nouvelle série...</p>';
		}
    } elseif (isset($_GET['action']) && $_GET['action'] == 'RAZ') { // Interface de remise à zéro de la plate-forme
        echo '<a href="/administration/gestion">Retour à l\'accueil</a>';
        echo '<span id="page_id">48</span>';
        if (isset($_POST['raz']) && $_POST['raz']) {
            $parametres->remiseAZero();
            echo '<h3 style="color:red;">Remise à zéro effectuée</h3>';
            Logs::logger(1, 'Administrateur : Remise a zero de l\'interface effectuee');
        }
        ?>
        <p style="color:red;">Attention : la remise à zéro de l'interface est irréversible.</p>
        <p>Cette action efface toutes les informations relatives aux séries, aux admissibles, aux élèves, et aux demandes d'hébergement.</p>
        <form action="/administration/gestion?action=RAZ" method="post">
        <p class="champ" id="champ-raz"><label for="raz">Cocher cette case si vous êtes certain de vouloir effectuer une remise à zéro de l'interface :</label></p>
        <br/><input type="checkbox" name="raz"/><br/>
        <input type="submit" value="Effectuer la remise à zéro"/>
        </form>
        <?php
    } elseif (isset($_GET['action']) && $_GET['action'] == 'hotel') { // Interface de gestion des hébergements à proximité du campus
        $adresseManager = new AdresseManager($db);
        if (isset($_GET['suppr_cat'])) { // Suppression d'une catégorie
            if (!is_numeric($_GET['suppr_cat'])) {
                Logs::logger(3, 'Corruption des parametres. admin.php::GET');
            }
            if (!$adresseManager->isUsedCat($_GET['suppr_cat'])) {
                $adresseManager->deleteCategorie($_GET['suppr_cat']);
                Logs::logger(1, 'Administrateur : Suppression d\'une categorie d\'adresse');
            } else {
                $erreurA = 'Vous ne pouvez supprimer cette catégorie tant qu\'elle contient des adresses';
                Logs::logger(2, 'Administrateur : Tentative de suppression d\'une categorie d\'adresse encore utilisee');
            }
        }
        if (isset($_GET['suppr'])) { // Suppression d'une annonce
            if (!is_numeric($_GET['suppr'])) {
                Logs::logger(3, 'Corruption des parametres. admin.php::GET');
            }
            $adresseManager->delete($_GET['suppr']);
            Logs::logger(1, 'Administrateur : Suppression d\'une adresse');
        }
        if (isset($_POST['nom_cat'])) { // Ajout d'une catégorie
            if (!empty($_POST['nom_cat']) && strlen($_POST['nom_cat']) <= 100) {
                $adresseManager->addCategorie($_POST['nom_cat']);
                Logs::logger(1, 'Administrateur : Ajout d\'une adresse');
            } else {
                $erreurA = 'Erreur lors de l\'ajout d\'une nouvelle catégorie';
                Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une categorie d\'adresses');
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
                Logs::logger(1, 'Administrateur : Ajout d\'une adresse');
            } else {
                $erreurModif = $adresse->erreurs();
                Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une adresses');
            }
        }
        $categories = $adresseManager->getCategories();
        echo '<a href="/administration/gestion">Retour à l\'accueil</a>';
        echo '<h2>Gestion de la liste des hébergements à proximité de l\'école</h2>';
        if (isset($_GET['ajout']) || isset($_GET['modif']) || isset($erreurModif)) { // Interface de modification d'une adresse
            if (isset($_GET['modif'])) {
                $adresse = $adresseManager->getUnique($_GET['modif']);
            }
            ?>
            <form action="/administration/gestion?action=hotel" method="post">
            <p class="champ"><label for="nom">Nom : </label><input type="text" name="nom" value="<?php if (isset($adresse)) { echo $adresse->nom(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::Nom_Invalide, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/>
            <p class="champ"><label for="adresse">Adresse : </label><input type="text" name="adresse" value="<?php if (isset($adresse)) { echo $adresse->adresse(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::Adresse_Invalide, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/>
            <p class="champ"><label for="tel">Téléphone : </label><input type="text" name="tel" value="<?php if (isset($adresse)) { echo $adresse->tel(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::Tel_Invalide, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/>
            <p class="champ"><label for="email">Email : </label><input type="text" name="email" value="<?php if (isset($adresse)) { echo $adresse->email(); } ?>"/> <?php if (isset($erreurModif) && in_array(Adresse::Email_Invalide, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/>
            <p class="champ"><label for="description">Description : </label><?php if (isset($erreurModif) && in_array(Adresse::Description_Invalide, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/>
            <textarea name="description" cols="20" rows="4"><?php if (isset($adresse)) { echo $adresse->description(); } ?></textarea><p/>
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
            </select></p> <?php if (isset($erreurModif) && in_array(Adresse::Categorie_Invalide, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><br/>
            <p class="champ"><label for="valide">Afficher cette annonce sur le site ? </label><input type="checkbox" name="valide" <?php echo $checked; ?>/> <?php if (isset($erreurModif) && in_array(Adresse::VALIDE_INVALIDE, $erreurModif)) echo '<span style="color:red;">Champ invalide</span>'; ?><p/>
            <br/>
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
            echo '<a href="/admissible/adresses" target="_blank">Voir la page publique affichant les adresses</a>';
            // Gestion des catégories
            echo '<h3>Catégories d\'hébergement</h3>';
            echo '<span style="color:red;">'.@$erreurA.'</span>';
            echo '<form action="/administration/gestion?action=hotel" method="post">';
            echo '<table border=1 cellspacing=0>';
            echo '<tr><td>Catégories</td><td>Action</td></tr>';
            foreach ($categories as $value) {
                echo '<tr>';
                    echo '<td>'.$value['nom'].'</td></td>';
                    echo '<td><a href="/administration/gestion?action=hotel&suppr_cat='.$value['id'].'">Suppr</a></td>';
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td><input type="text" name="nom_cat"/></td>';
            echo '<td><input type="submit" value="Ajouter"/></td>';
            echo '</tr>';
            echo '</table>';
            echo '</form>';
            // Gestion des adresses affichées
            echo '<h3>Adresses affichées actuellement sur le site</h3>';
            echo '<a href="/administration/gestion?action=hotel&ajout=1">Ajouter une annonce</a>';
            $adressesValides = $adresseManager->getListAffiche();
            echo '<table border=1 cellspacing=0>';
            echo '<tr><td>Annonce comme affichée</td><td>Catégorie</td><td>Actions</td></tr>';
            foreach ($adressesValides as $adresse) {
                echo '<tr>';
                    echo '<td>'.$adresse->nom().'<br/>'.$adresse->adresse().'<br/>Tél : '.$adresse->tel().'<br/>Mail : '.$adresse->email().'<br/>'.nl2br($adresse->description()).'</td>';
                    echo '<td>'.$adresse->categorie().'</td>';
                    echo '<td><a href="/administration/gestion?action=hotel&modif='.$adresse->id().'">Modif</a> <a href="/administration/gestion?action=hotel&suppr='.$adresse->id().'">Suppr</a></td>';
                echo '</tr>';
            }
            echo '</table>';
            // Gestion des adresses à valider
            echo '<h3>Adresses non affichées (proposées par les élèves)</h3>';
            echo '<p>Pour valider une annonce, cliquez sur Modifier et cocher la case correspondannte</p>';
            $adressesValides = $adresseManager->getListAffiche(0);
            echo '<table border=1 cellspacing=0>';
            echo '<tr><td>Annonce comme affichée</td><td>Catégorie</td><td>Actions</td></tr>';
            foreach ($adressesValides as $adresse) {
                echo '<tr>';
                    echo '<td>'.$adresse->nom().'<br/>'.$adresse->adresse().'<br/>Tél : '.$adresse->tel().'<br/>Mail : '.$adresse->email().'<br/>'.nl2br($adresse->description()).'</td>';
                    echo '<td>'.$adresse->categorie().'</td>';
                    echo '<td><a href="/administration/gestion?action=hotel&modif='.$adresse->id().'">Modif</a> <a href="/administration/gestion?action=hotel&suppr='.$adresse->id().'">Suppr</a></td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '<span id="page_id">47</span>';
        }
    } else { // Interface de gestion courante
        ?>
        <a href="/administration/gestion?action=deconnect">Se déconnecter</a><br/>
        <a href="/administration/gestion?action=RAZ">Remise à zéro de l'interface d'hébergement</a><br/>
        <a href="/administration/gestion?action=series">Modifier les séries d'admissibilités (dates d'ouverture du site)</a><br/>
        <a href="/administration/gestion?action=param&type=<?php echo Parametres::Promo; ?>">Modifier les promotions présentes sur le platal</a><br/>
        <a href="/administration/gestion?action=param&type=<?php echo Parametres::Etablissement; ?>">Modifier les établissements de provenance des élèves</a><br/>
        <a href="/administration/gestion?action=param&type=<?php echo Parametres::Filiere; ?>">Modifier les filières d'entrée des élèves</a><br/>
        <a href="/administration/gestion?action=param&type=<?php echo Parametres::Section; ?>">Modifier les sections sportives des élèves</a><br/>
        <a href="/administration/gestion?action=admissibles">Entrer la liste des admissibles pour la prochaine série</a><br/>
        <a href="/administration/gestion?action=hotel">Modifier la liste des hébergements à proximitè de l'école</a><br/>
        <span id="page_id">4</span>
        <?php
    }
}
?>