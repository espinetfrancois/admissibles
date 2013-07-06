<?php
require_once(APPLICATION_PATH.'/inc/fkz_auth.php');
$parametres = Registry::get('parametres');
$db = Registry::get('db');

// Identification
if (! (isset($_SESSION['administrateur']) && $_SESSION['administrateur'] === true)) {
	frankiz_do_auth('/administration/etablissements');
	return;
}

//identification ok, affichage de l'interface
echo '<h2>Interface d\'administration</h2>';

// Gestion des listes de paramètres = lycées de provenance et filières d'entrée
echo '<span id="page_id">401</span>';
echo '<h3>Etablissements de provenance des élèves</h3>';
echo '<p>Les élèves gardent la possibilité d\'entrer une autre valeur que celles proposées ci-dessous.</p>';
$form = '<input type="text" name="ville" value="VILLE" size="10" maxlength="50"/> - <input type="text" name="nom" value="Nom de l\'établissement" size="30" maxlength="50"/>';

// Suppression d'un élément de liste
if (isset($_GET['suppr'])) {
	//check sur le type
	if (!is_numeric($_GET['suppr'])) {
		throw new Exception_Page('Corruption des parametres. admin.php::GET suppr', "L'url demandée n'est pas valide.", Exception_Page::FATAL_ERROR);
		return 1;
	}

	if (!$parametres->isUsedList($_GET['type'], $_GET['suppr'])) {
		try {
			$parametres->deleteFromList($_GET['type'], $_GET['suppr']);
			Registry::get('layout')->addMessage('Élément supprimé avec succés.', MSG_LEVEL_OK);
			Logs::logger(1, 'Administrateur : Suppression d\'un element de liste');
		} catch (Exception_Bdd $e) {
			Registry::get('layout')->addMessage('Impossible de supprimer cet élément.', MSG_LEVEL_ERROR);
		}
	} else {
		Registry::get('layout')->addMessage('Vous ne pouvez supprimer cet élément tant qu\'il est utilisé dans le profil d\'un élève ou d\'un admissible', MSG_LEVEL_WARNING);
		Logs::logger(1, 'Administrateur : Tentative de suppression d\'un element de liste encore utilise');
	}
}

// Ajout d'un élément de liste (Etablissement)
if (isset($_POST['nom']) && isset($_POST['ville'])) {
	//vérification du post
	if (!empty($_POST['nom']) && !empty($_POST['ville']) && strlen($_POST['nom']) <= 50 && strlen($_POST['ville']) <= 50) {
		try {
			$parametres->addToList(Parametres::Etablissement, array('nom' => $_POST['nom'], 'commune' => $_POST['ville']));
			Logs::logger(1, 'Administrateur : Ajout d\'un element a une liste');
			Registry::get('layout')->addMessage('Lycée ajouté avec succés', MSG_LEVEL_OK);
		} catch (Exception_Bdd $e) {
			Registry::get('layout')->addMessage("Impossible d'ajouter ce lycée dans la base de donnée", MSG_LEVEL_ERROR);
		}
	} else {
		Registry::get('layout')->addMessage('Erreur lors de l\'ajout d\'un nouvel élément : l\'élément est invalide', MSG_LEVEL_WARNING);
		Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'un element a une liste');
	}
} else if (isset($_POST['nom'])) { // Ajout d'un élément de liste (autre)
	//vérification du post
	if (!empty($_POST['nom']) && strlen($_POST['nom']) <= 50) {
		try {
			$parametres->addToList($_GET['type'], array('nom' => $_POST['nom']));
			Logs::logger(1, 'Administrateur : Ajout d\'un element a une liste');
			Registry::get('layout')->addMessage('Élément ajouté avec succés.',MSG_LEVEL_OK);
		} catch (Exception_Bdd $e) {
			Registry::get('Impossible d\'ajouter cet élément dans la base de donnée.', MSG_LEVEL_ERROR);
		}
	} else {
		Registry::get('layout')->addMessage('Erreur lors de l\'ajout d\'un nouvel élément : l\'élément est invalide', MSG_LEVEL_WARNING);
		Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'un element a une liste');
	}
}
try {
	$liste = $parametres->getList(Parametres::Etablissement);
} catch (Exception_Bdd $e) {
	//peut-être rethrow ici pour arreter l'execution
	Registry::get('layout')->addMessage('Impossible de récupérer la liste demandée.', MSG_LEVEL_ERROR);
	$liste = array();
}
//formulaire d'ajout
echo '<form action="/administration/etablissements" method="post">';
echo '<table border=1 cellspacing=0>';
echo '<thead><tr><th>Valeur</th><th>Action</th></tr></thead>';
echo '<tbody>';
foreach ($liste as $res) {
	$res['nom'] = $res['ville'].' - '.$res['nom'];
	echo '<tr>';
	echo '<td>'.$res['nom'].'</td><td><a class="action" href="/administration/etabilssements?suppr='.$res['id'].'">Supprimer</a></td>';
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
