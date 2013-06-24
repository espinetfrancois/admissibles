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
echo '<h2>Interface d\'administration</h2>';

// Administration des demandes en cours
echo '<span id="page_id">42</span>';
echo '<h3>Demandes en cours</h3>';
$demandeManager = new Manager_Demande(Registry::get('db'));
try {
	$demandes = $demandeManager->getList();
} catch (Exception_Bdd $e) {
	//rethrow
	$demandes = array();
	Registry::get('layout')->addMessage('Impossible de récupérer la liste des demandes en cours', MSG_LEVEL_ERROR);
}
echo '<table border=1 cellspacing=0>';
echo '<thead><tr><th>Série</th><th>Filière</th><th>Admissible</th><th>Elève X</th><th>Statut</th></tr></thead>';
echo '<tbody>';
$resume = array();
if (count($demandes) < 1)
	echo '<tr><td colspan="5">Pas de demandes en cours</td></tr>';
foreach ($demandes as $demande) {
    (isset($resume[$demande->serie()][$demande->status()]) ? $resume[$demande->serie()][$demande->status()]+=1 : $resume[$demande->serie()][$demande->status()]=1);
	echo '<tr>';
	echo '<td>'.$demande->serie().'</td>';
	echo '<td>'.$demande->filiere().'</td>';
	echo '<td>'.$demande->nom().' '.$demande->prenom().'<br/>'.$demande->email().'</td>';
	echo '<td>'.$demande->userEleve().'</td>';
	echo '<td>'.$demande->status().'</td>';
	echo '</tr>';
}

echo '</tbody></table>';

echo '<h3>Résumé des demandes</h3>';
echo '<table border=1 cellspacing=0>';
echo '<thead><tr><th>Série</th><th>État</th><th>Nombre</th></thead>';
echo '<tbody>';
foreach ($resume as $serie=>$statseries) {
    $rowspan = 'rowspan='.count($statseries);
    foreach($statseries as $status=>$num) {
    	echo '<tr>';
    	echo '<td '.$rowspan.' >'.$serie.'</td>';
    	echo '<td>'.$status.'</td>';
    	echo '<td>'.$num.'</td>';
    	echo '</tr>';
    	$rowspan = '';
    }
}

echo '</tbody></table>';

return;
