<?php
require_once(APPLICATION_PATH.'/inc/fkz_auth.php');
$parametres = Registry::get('parametres');
$db = Registry::get('db');

// Identification
if (! (isset($_SESSION['administrateur']) && $_SESSION['administrateur'] === true)) {
	frankiz_do_auth('/administration/demandes');
	return;
}

//identification ok, affichage de l'interface
echo '<h2>Statistiques d\'utilisation du site</h2>';

// Statistiques d'utilisation
echo '<span id="page_id">431</span>';
echo '<h3>Demandes sur le site</h3>';

echo '<h3>Admissibles n\'ayant pas eu de places</h3>';

echo '<h3>Bonnes Adresses enregistrées</h3>';

echo '<h3>Envoyer les sondages</h3>';

echo '<h4>Sondage aux élèves</h4>';
echo '<p>Remplissez ce formulaire pour envoyer un mail à tous les élèves s\'étant inscrit sur le site</p>';


echo '<h4>Sondage aux admissibles</h4>';
echo '<p>Remplissez ce formulaire pour envoyer un mail à tous les admissibles s\'étant inscrit sur le site</p>';


return;