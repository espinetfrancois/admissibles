<?php
/**
 * Page d'affichage des adresses d'h�bergement � proximit� de l'�cole
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

include_once(APPLICATION_PATH.'/inc/sql.php');

$adresseManager = new AdresseManager($db);
$adressesValides = $adresseManager->getListAffiche();
$cat = "";
foreach ($adressesValides as $adresse) {
    if ($adresse->categorie() != $cat) {
        echo "<h3>".$adresse->categorie()."</h3>";
        $cat = $adresse->categorie();
    }
    echo "<p>";
    echo $adresse->nom()."<br/>".$adresse->adresse()."<br/>T�l : ".$adresse->tel()."<br/>Mail : ".$adresse->email()."<br/>".nl2br($adresse->description());
    echo "</p>";
}
?>
<span id="page_id">2</span>