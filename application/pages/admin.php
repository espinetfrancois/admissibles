<?php
/**
 * Page d'administration de la plate-forme d'hébergement
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

require_once(APPLICATION_PATH.'/inc/fkz_auth.php');
// Identification
if (! (isset($_SESSION['administrateur']) && $_SESSION['administrateur'] === true)) {
    frankiz_do_auth('/administration/gestion');
    return;
}
//identification ok, affichage de l'interface
?>
<h2>Interface d'administration</h2>
<ul>
    <li><a href="/deconnexion">Se déconnecter</a></li>
    <li><a href="/administration/remise-a-zero">Remise à zéro de l'interface d'hébergement</a></li>
    <li><a href="/administration/series-admissibilites">Modifier les séries d'admissibilités (dates d'ouverture du site)</a></li>
    <li><a href="/administration/etablissements">Modifier les établissements de provenance des élèves</a></li>
    <li><a href="/administration/filieres">Modifier les filières d'entrée des élèves</a></li>
    <li><a href='/administration/inscriptionsx'>Voir les disponibilités des élèves par série</a></li>
    <li><a href="/administration/listes-admissibles">Entrer la liste des admissibles pour la prochaine série</a></li>
    <li><a href="/administration/demandes">Voir les demandes en cours</a></li>
    <li><a href="/administration/hebergements">Modifier la liste des hébergements à proximitè de l'école</a></li>
</ul>
<span id="page_id">4</span>
