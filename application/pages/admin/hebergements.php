<?php
require_once(APPLICATION_PATH.'/inc/fkz_auth.php');
$parametres = Registry::get('parametres');
$db = Registry::get('db');

// Identification
if (! (isset($_SESSION['administrateur']) && $_SESSION['administrateur'] === true)) {
	frankiz_do_auth('/administration/hebergements');
	return;
}

//identification ok, affichage de l'interface
echo '<h2>Interface d\'administration</h2>';

// Interface de gestion des hébergements à proximité du campus
$adresseManager = new Manager_Adresse($db);
// Suppression d'une catégorie
if (isset($_GET['suppr_cat'])) {
	if (!is_numeric($_GET['suppr_cat'])) {
		throw new Exception_Page('Corruption des parametres. admin.php::GET', 'Selectionnez une catégorie hebergement pour le supprimer', Exception_Page::ERROR);
		//             Logs::logger(3, 'Corruption des parametres. admin.php::GET');
	}
	if (!$adresseManager->isUsedCat($_GET['suppr_cat'])) {
		try {
			$adresseManager->deleteCategorie($_GET['suppr_cat']);
			Logs::logger(1, 'Administrateur : Suppression d\'une categorie d\'adresse');
			Registry::get('layout')->addMessage("Suppression de la catégorie réussie.", MSG_LEVEL_OK);
		} catch (Exception_Bdd $e) {
			Regitry::get('layout')->addMessage('Echec de la suppression de la catégorie.', MSG_LEVEL_ERROR);
		}
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
	try {
		$adresseManager->delete($_GET['suppr']);
		Registry::get('layout')->addMessage('Suppression de l\'annonce effectuée.', MSG_LEVEL_OK);
	} catch (Exception_Bdd $e) {
		Registry::get('layout')->addMessage('Impossible de supprimer cette annonce.', MSG_LEVEL_ERROR);
	}
	Logs::logger(1, 'Administrateur : Suppression d\'une adresse');
}

// Ajout d'une catégorie
if (isset($_POST['nom_cat'])) {
	if (!empty($_POST['nom_cat']) && strlen($_POST['nom_cat']) <= 100) {
		try {
			$adresseManager->addCategorie($_POST['nom_cat']);
			Logs::logger(1, 'Administrateur : Ajout d\'une adresse');
			Registry::get('layout')->addMessage('Adresse ajoutée avec succés', MSG_LEVEL_OK);
		} catch (Exception_Bdd $e) {
			Registry::get('layout')->addMessage('Echec lors de l\'ajout de cette adresse dans la base de données.', MSG_LEVEL_ERROR);
		}
	} else {
		Registry::get('layout')->addMessage('Erreur lors de l\'ajout d\'une nouvelle catégorie : la catégorie n\'est pas correctement remplie.', MSG_LEVEL_ERROR);
		Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une categorie d\'adresses');
	}
}

// Ajout ou Modification d'une annonce
if (isset ($_POST['nom'])) {
	$adresse = new Model_Adresse(array('nom' => $_POST['nom'],
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
		try {
			$adresseManager->save($adresse);
			Logs::logger(1, 'Administrateur : Ajout d\'une adresse');
			Registry::get('layout')->addMessage('Adresse ajoutée avec succés', MSG_LEVEL_OK);
		} catch (Exception_Bdd $e) {
			Registry::get('layout')->addMessage('Echec lors de l\'ajout d\'une adresse', MSG_LEVEL_ERROR);
		}
	} else {
		$erreurModif = $adresse->erreurs();
		Registry::get('layout')->addMessage('Erreur dans le remplissage du formulaire d\'ajout d\'une adresse.', MSG_LEVEL_WARNING);
		Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une adresses');
	}
}
try {
	$categories = $adresseManager->getCategories();
} catch (Exception_Bdd $e) {
	//rethrow
	$categories = array();
	Registry::get('layout')->addMessage('Impossible de récupérer la liste des catégories.', MSG_LEVEL_ERROR);
}

//interface de gestion
echo '<h3>Gestion de la liste des hébergements à proximité de l\'école</h3>';

// Interface de modification d'une adresse
if (isset($_GET['ajout']) || isset($_GET['modif']) || isset($erreurModif)) {
	if (isset($_GET['modif'])) {
		try {
			$adresse = $adresseManager->getUnique($_GET['modif']);
		} catch (Exception_Bdd $e) {
			Registry::get('layout')->addMessage('Impossible de récupérer cette adresse pour la modifier.', MSG_LEVEL_ERROR);
		}
	}
	$champInvalide = '<span class"error">Champ invalide</span>';
	?>
        <form action="/administration/hebergements" method="post">
        <p class="champ"><label for="nom">Nom : </label><input type="text" name="nom" value="<?php if (isset($adresse)) { echo $adresse->nom(); } ?>"/> <?php if (isset($erreurModif) && in_array(Model_Adresse::Nom_Invalide, $erreurModif)) echo $champInvalide ?></p>
        <p class="champ"><label for="adresse">Adresse : </label><input type="text" name="adresse" value="<?php if (isset($adresse)) { echo $adresse->adresse(); } ?>"/> <?php if (isset($erreurModif) && in_array(Model_Adresse::Adresse_Invalide, $erreurModif)) echo $champInvalide; ?></p>
        <p class="champ"><label for="tel">Téléphone : </label><input type="text" name="tel" value="<?php if (isset($adresse)) { echo $adresse->tel(); } ?>"/> <?php if (isset($erreurModif) && in_array(Model_Adresse::Tel_Invalide, $erreurModif)) echo $champInvalide; ?></p>
        <p class="champ"><label for="email">Email : </label><input type="text" name="email" value="<?php if (isset($adresse)) { echo $adresse->email(); } ?>"/> <?php if (isset($erreurModif) && in_array(Model_Adresse::Email_Invalide, $erreurModif)) echo $champInvalide; ?></p>
        <p class="champ"><label for="description">Description : </label><?php if (isset($erreurModif) && in_array(Model_Adresse::Description_Invalide, $erreurModif)) echo $champInvalide; ?>
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

    if (isset($adresse) && $adresse->valide() == '1') {
        $checked = 'checked';
    } else {
        $checked = '';
    }

    ?>
    </select> <?php if (isset($erreurModif) && in_array(Model_Adresse::Categorie_Invalide, $erreurModif)) echo $champInvalide; ?></p>
    <p class="champ"><label for="valide">Afficher cette annonce sur le site ? </label><input type="checkbox" name="valide" <?php echo $checked; ?>/> <?php if (isset($erreurModif) && in_array(Model_Adresse::Valide_Invalide, $erreurModif)) echo $champInvalide; ?></p>
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

    echo '<form action="/administration/hebergements" method="post">';
    echo '<table border=1 cellspacing=0>';
    echo '<thead><tr><th>Catégories</th><th>Action</th></tr></thead>';
    echo '<tbody>';

    if (count($categories) < 1)
    	echo '<tr><td colspan="3">Aucune catégorie</td></tr>';

    foreach ($categories as $value) {
        echo '<tr>';
            echo '<td>'.$value['nom'].'</td></td>';
            echo '<td><a class="action" href="/administration/hebergements?suppr_cat='.$value['id'].'">Supprimer</a></td>';
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
    echo '<p><a href="/administration/hebergements?ajout=1">Ajouter une annonce</a></p>';
    try {
        $adressesValides = $adresseManager->getListAffiche();
    } catch (Exception_Bdd $e) {
        $adressesValides = array();
        Registry::get('layout')->addMessage('Impossible de récupérer la liste des hébergements.', MSG_LEVEL_ERROR);
    }
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
                <a class="action" href="/administration/hebergements?&modif='.$adresse->id().'">Modifier</a>
                <a class="action" href="/administration/hebergements?suppr='.$adresse->id().'">Supprimer</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

    // Gestion des adresses à valider
    echo '<h4>Adresses non affichées (proposées par les élèves)</h4>';
    echo '<p>Pour valider une annonce, cliquez sur Modifier et cocher la case correspondannte</p>';
    echo '<p>Avant de valider une annonce, merci de vérifier le caractère non-commercial de cette dernière.</p>';
    try {
        $adressesValides = $adresseManager->getListAffiche(0);
    } catch (Exception_Bdd $e) {
        Registry::get('layout')->addMessage('Impossible de récupérer la liste des hébergements à valider.', MSG_LEVEL_ERROR);
    }
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
                <a class="action" href="/administration/hebergements?modif='.$adresse->id().'">Modifier</a>
                <a class="action" href="/administration/hebergements?suppr='.$adresse->id().'">Supprimer</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

    echo '<span id="page_id">47</span>';
}
return;
