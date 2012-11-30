<?php
/**
 * Page d'affichage des adresses d'hébergement à proximité de l'école
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */
require_once(APPLICATION_PATH.'/inc/sql.php');

$adresseManager = new AdresseManager($db);
$adressesValides = $adresseManager->getListAffiche();
$cat = '';
foreach ($adressesValides as $adresse) {
    if ($adresse->categorie() != $cat) {
        echo '<h3 class="categorie">'.$adresse->categorie().'</h3>';
        $cat = $adresse->categorie();
    }
    echo '<div class="bonne_adresse">';
    echo '<span class="nom">'.$adresse->nom().'</span><br/>'.
         '<span class="adresse">'.$adresse->adresse().'</span><br/>'.
         '<span class="telephone">'.'Tél : '.$adresse->tel().'</span><br/>'.
         '<span class="email">Mail : <a href="mailto:'.$adresse->email().'">'.$adresse->email().'</a></span><br/>'.
         '<span class="description">'.nl2br($adresse->description()).'</span><br/>';
    echo '</div>';
}
?>
<span id="page_id">2</span>
