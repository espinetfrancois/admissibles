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
echo '<h3>Disponibilités des X</h3>';
echo '<span id="page_id">491</span>';

$series = $parametres->getXInSeries();


foreach ($series as $serie => $aX) {
    $sTable = '<table class="dispo-series"><thead><tr><th>'.$serie.'</th></tr></thead><tbody>';
    $nEleves = count($aX);
    if ($nEleves == 1 && $aX[0] === NULL) {
        $sTable .= '<tr><td>Aucun élève disponible</td></tr>';
        $nEleves = 0;
    } else {
        foreach ($aX as $x) {
            $sTable .= '<tr><td>'.$x.'</td></tr>';
        }
    }
    $sTable .= '</tbody>';
    $sTable .= '<tfoot><tr><th>Nombre d\'élèves diponibles : '.$nEleves.'</th></tr></tfoot></table>';
    echo $sTable;
}