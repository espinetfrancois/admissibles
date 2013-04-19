<?php
/**
 * Page d'affichage des adresses d'hébergement à proximité de l'école
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */
// require_once(APPLICATION_PATH.'/inc/sql.php');

$adresseManager = new Manager_Adresse(Registry::get('db'));
$adressesValides = $adresseManager->getListAffiche();
$cat = '';
if (count($adressesValides) < 1)
    echo '<p>Aucune adresse n\'a encore été rentrée.</p>';

foreach ($adressesValides as $adresse) {
    if ($adresse->categorie() != $cat) {
        echo '<h3 class="categorie">'.$adresse->categorie().'</h3>';
        $cat = $adresse->categorie();
    }
    echo '<div class="bonne-adresse">';
    echo '<div class="nom">'.$adresse->nom().'</div>'.
         '<div class="adresse">'.$adresse->adresse().'</div>'.
         '<div class="telephone">'.'Tél : '.$adresse->tel().'</div>'.
         '<div class="email">Mail : <a href="mailto:'.$adresse->email().'">'.$adresse->email().'</a></div>'.
         '<div class="description">'.nl2br($adresse->description()).'</div>';
    echo '</div>';
}
Registry::get('layout')->appendCss('bonnes-adresses.css');
?>
<span id="page_id">2</span>