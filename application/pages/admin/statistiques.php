<?php
require_once(APPLICATION_PATH.'/inc/fkz_auth.php');
$parametres = Registry::get('parametres');
$db = Registry::get('db');
$stat = new Statistiques($db);
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
$resume = $stat->getNbTotalDemandes();
echo '<table border=1 cellspacing=0>';
echo '<thead><tr><th>Statut</th><th>Nombre de demandes</th></tr></thead>';
echo '<tbody>';
foreach($resume as $statut) {
    echo '<tr>';
    echo '<td>'.$statut['NOM'].'</td>';
    echo '<td>'.$statut['NOMBRE'].'</td>';
    echo '</tr>';
}
echo '</tbody></table>';

echo '<h4>Détails par séries</h4>';
$detail = $stat->getNbDemandesSeries();
echo '<table border=1 cellspacing=0>';
echo '<thead><tr><th>Série</th><th>Statut</th><th>Nombre de demandes</th></tr></thead>';
echo '<tbody>';
foreach($detail as $s=>$serie) {
    $rowspan = 'rowspan='.count($serie);
    foreach ($serie as $serieStat) {
        echo '<tr>';
    	echo ($rowspan === null ? '' : '<td '.$rowspan.' >'.$s.'</td>');
    	echo '<td>'.$serieStat['NOM'].'</td>';
    	echo '<td>'.$serieStat['NOMBRE'].'</td>';
    	echo '</tr>';
    	$rowspan = null;
    }
}
echo '</tbody></table>';

echo '<h3>Admissibles n\'ayant pas eu de places</h3>';
echo "<p>Disponible prochainement</p>";

echo '<h3>Bonnes Adresses enregistrées</h3>';

echo '<table border=1 cellspacing=0>';
echo '<thead><tr><th>Catégorie</th><th>Nombre d\'annonces</th></tr></thead>';
foreach ($stat->getNbAnnonces() as $categorie) {
    echo '<tr>';
    echo '<td>'.$categorie['NOM'].'</td>';
    echo '<td>'.$categorie['NOMBRE'].'</td>';
    echo '</tr>';
}
echo '</tbody></table>';
echo '<h3>Envoyer les sondages</h3>';

echo '<h4>Sondage aux élèves</h4>';
echo '<p>Remplissez ce formulaire pour envoyer un mail à tous les élèves s\'étant inscrit sur le site</p>';


echo '<h4>Sondage aux admissibles</h4>';
echo '<p>Remplissez ce formulaire pour envoyer un mail à tous les admissibles s\'étant inscrit sur le site</p>';


return;