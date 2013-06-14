<?php
require_once(APPLICATION_PATH.'/inc/fkz_auth.php');
$parametres = Registry::get('parametres');
$db = Registry::get('db');

// Identification
if (! (isset($_SESSION['administrateur']) && $_SESSION['administrateur'] === true)) {
	frankiz_do_auth('/administration/gestion');
	return;
}

//identification ok, affichage de l'interface
echo '<h2>Interface d\'administration</h2>';

// Modification des séries d'admissibilité
// Suppression d'une série
if (isset($_GET['suppr'])) {
	if (!is_numeric($_GET['suppr'])) {
		throw new Exception_Page('Corruption des parametres. admin.php::GET suppr', "L'url demandée n'est pas valide.", Exception_Page::FATAL_ERROR);
		return 1;
	}
	if (!$parametres->isUsedList(Parametres::Serie, $_GET['suppr'])) {
		try {
			$parametres->deleteFromList(Parametres::Serie, $_GET['suppr']);
			Logs::logger(1, 'Administrateur : Suppression d\'une serie');
			Registry::get('layout')->addMessage('Liste des admissibles supprimée avec succés.', MSG_LEVEL_OK);
		} catch (Exception_Bdd $e) {
			Registry::get('layout')->addMessage("Erreur lors de la suppression de la liste d'admissibilité sélectionnée.", MSG_LEVEL_ERROR);
		}
	} else {
		Registry::get('layout')->addMessage('Vous ne pouvez supprimer cette série tant qu\'elle est utilisée dans le profil d\'un élève ou d\'un admissible', MSG_LEVEL_WARNING);
		Logs::logger(2, 'Administrateur : Tentative de suppression d\'une serie encore utilise');
	}
}
// Insertion d'une nouvelle série
if (isset($_POST['intitule']) && isset($_POST['date_debut']) && isset($_POST['date_fin'])) {
	//vérification du post
	if (!empty($_POST['intitule']) && strlen($_POST['intitule']) <= 50
			&& preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $_POST['date_debut']) == 1
			&& preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $_POST['date_fin']) == 1) {

		$expDateD = explode('/', $_POST['date_debut']);
		$expDateF = explode('/', $_POST['date_fin']);
		$date_debut = mktime(0, 0, 0, $expDateD[1], $expDateD[0], $expDateD[2]);
		$date_fin = mktime(0, 0, 0, $expDateF[1], $expDateF[0], $expDateF[2]);
		/*
		 * L'ouverture des demandes sera réglée lors de l'insertion de la liste des admissibles
		* La fermeture des demandes correspond à minuit la veille du début des oraux
		*
		*/
		try {
			$parametres->addToList(Parametres::Serie, array('intitule' => $_POST['intitule'], 'date_debut' => $date_debut, 'date_fin' => $date_fin, 'ouverture' => $date_debut, 'fermeture' => $date_debut));
			Logs::logger(1, 'Administrateur : Ajout d\'une serie');
			Registry::get('layout')->addMessage('Série ajoutée avec succés', MSG_LEVEL_OK);
		} catch (Exception_Bdd $e) {
			Registry::get('layout')->addMessage('Erreur lors de l\'ajout de la série', MSG_LEVEL_ERROR);
		}
	} else {
		Registry::get('layout')->addMessage('Erreur lors de l\'ajout d\'une nouvelle série', MSG_LEVEL_ERROR);
		Logs::logger(2, 'Administrateur : Erreur dans le remplissage du formulaire d\'ajout d\'une serie');
	}
}

//interface pour l'ajout
echo '<h3>Séries d\'admissibilité</h3>';
echo '<span id="page_id">41</span>';
try {
	$series = $parametres->getList(Parametres::Serie);
} catch (Exception_Bdd $e) {
	//rethrow ?
	$series = array();
	Regitry::get('layout')->addMessage('Impossible de récupérer la liste des séries.', MSG_LEVEL_ERROR);
}
echo '<form action="/administration/series-admissibilites" method="post">';
echo '<table border=1 cellspacing=0>';
echo '<thead><tr><th>Intitulé</th><th>Date de début des oraux</th><th>Date de fin des oraux</th><th>Action</th></tr></thead>';
echo '<tbody>';
foreach ($series as $value) {
	echo '<tr>';
	echo '<td>'.$value['intitule'].'</td>';
	echo '<td>'.date('d/m/Y', $value['date_debut']).'</td>';
	echo '<td>'.date('d/m/Y', $value['date_fin']).'</td>';
	echo '<td><a class="action" href="/administration/series-admissibilites?suppr='.$value['id'].'">Supprimer</a></td>';
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
