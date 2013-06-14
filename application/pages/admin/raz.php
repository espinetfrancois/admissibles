<style>
.contenu {
    max-width : 100% !important;
}
</style>
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
echo '<h3>Remize à zéro</h3>';

// Interface de remise à zéro de la plate-forme
echo '<span id="page_id">48</span>';
if (isset($_POST['raz']) && $_POST['raz']) {
    try {
        $parametres->remiseAZero();
        Registry::get('layout')->addMessage('Remise à zéro effectuée', MSG_LEVEL_OK);
        Logs::logger(1, 'Administrateur : Remise a zero de l\'interface effectuee');
    } catch (Exception_Bdd $e) {
        Registry::get('layout')->addMessage('Impossible de remettre à zéro l\'interface', MSG_LEVEL_ERROR);
    }
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
