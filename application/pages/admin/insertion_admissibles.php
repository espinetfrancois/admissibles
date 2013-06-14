<?php
require_once(APPLICATION_PATH.'/inc/fkz_auth.php');
$parametres = Registry::get('parametres');
$db = Registry::get('db');

// Identification
if (! (isset($_SESSION['administrateur']) && $_SESSION['administrateur'] === true)) {
	frankiz_do_auth('/administration/listes-admissibles');
	return;
}

//identification ok, affichage de l'interface
echo '<h2>Interface d\'administration</h2>';

// Modification des listes d'admissibilité
// Traitement de la liste ajoutée
if (isset($_POST['serie']) && isset($_POST['filiere']) && isset($_POST['liste'])) {
	if (is_numeric($_POST['serie']) && is_numeric($_POST['filiere'])
			&& preg_match("#^(.+\s\(.+\)(\r)?(\n)?)+$#", $_POST['liste']) == 1) {
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
		try {
			$parametres->supprAdmissible($_GET['suppr']);
			Logs::logger(1, 'Administrateur : Suppression d\'un admissible');
			Registry::get('layout')->addMessage('Admissible supprimé avec succés.', MSG_LEVEL_OK);
		} catch (Exception_Bdd $e) {
			Registry::get('layout')->addMessage('Impossible de supprimer cet admissible de la base de donnée.', MSG_LEVEL_ERROR);
		}
	} else {
		//          Logs::logger(2, 'Administrateur : Erreur dans la suppression d\'un admissible');
		throw new Exception_Page('Administrateur : Erreur dans la suppression d\'un admissible', "L'url demandée n'existe pas", Exception_Page::ERROR);
	}
}

//interface de gestion
echo '<h3>Insérer une liste d\'admissibilité</h3>';
echo '<span id="page_id">46</span>';
try {
	$filieres = $parametres->getList(Parametres::Filiere);
	$series = $parametres->getList(Parametres::Serie);
} catch (Exception_Bdd $e) {
	//rethrow ici à la place?
	$filieres = array();
	$series = array();
	Registry::get('layout')->addMessage('Impossible de récupérer la liste des filières ou des séries.', MSG_LEVEL_ERROR);
}
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
	echo '<form action="/administration/listes-admissibles" method="post">';
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
<form action="/administration/listes-admissibles" method="post">
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
        try {
            $admissibles = $parametres->getAdmissibles($_POST['serie-voir'], $_POST['filiere-voir']);
        } catch (Exception_Bdd $e) {
            $admissibles = array();
            Registry::get('layout')->addMessage('Impossible de récupérer les admissibles de cette serie.', MSG_LEVEL_ERROR);
        }
        echo '<p>Serie : '.$series[$_POST['serie-voir']].', Filière : '.$fil[$_POST['filiere-voir']].'</p>';
        echo '<table border="1" cellspacing="0" cellspadding="1">';
        echo '<thead><tr><th>Nom</th><th>Prénom</th><th>Mail (si inscrit)</th><th>Supprimer définitivement</th></tr></thead>';
        echo '<tbody>';
        if (count($admissibles) < 1)
            echo '<tr><td colspan="4">Aucun admissible</td></tr>';

        foreach ($admissibles as $admissible) {
            echo '<tr><td>'.$admissible['nom'].'</td><td>'
                .$admissible['prenom'].'</td><td>'.$admissible['mail']
                .'</td><td><a class="action" href="/administration/listes-admissibles?suppr='
                .$admissible['id'].'&serie-voir='.$_POST['serie-voir'].'&filiere-voir='.$_POST['filiere-voir'].'">Supprimer</a></td></tr>';
        }
        echo '</tbody></table>';
    } else {
        throw new Exception_Page('Administrateur : Corruption des parametres (numeric serie et filiere) admin.php', "Vous devez selectionner une serie valide", Exception_Page::ERROR);
        //       Logs::logger(3, 'Administrateur : Corruption des parametres (numeric serie et filiere) admin.php');
    }
}
return;
