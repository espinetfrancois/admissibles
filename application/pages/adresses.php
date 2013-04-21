<?php
/**
 * Page d'affichage des adresses d'hébergement à proximité de l'école
 * @author Nicolas GROROD <nicolas.grorod@polytechnique.edu>
 * @version 1.0
 *
 */

$adresseManager = new Manager_Adresse(Registry::get('db'));
try {
    $adressesValides = $adresseManager->getListAffiche();
} catch (Exception_Bdd $e) {
    Registry::get('layout')->addMessage('Impossible de récupérer les adresses en base.', MSG_LEVEL_ERROR);
    $adressesValides = array();
}
$cat = '';
if (count($adressesValides) < 1)
    echo '<p>Aucune adresse n\'a encore été rentrée.</p>';

foreach ($adressesValides as $adresse) {
    if ($adresse->categorie() != $cat) {
        echo '<h3 class="categorie">'.Manager::escape($adresse->categorie()).'</h3>';
        $cat = $adresse->categorie();
    }
    echo '<div class="bonne-adresse">';
    echo '<div class="nom">'.Manager::escape($adresse->nom()).'</div>'.
         '<div class="adresse">'.Manager::escape($adresse->adresse()).'</div>'.
         '<div class="telephone">'.'Tél : '.Manager::escape($adresse->tel()).'</div>'.
         '<div class="email">Mail : <a href="mailto:'.Manager::escape($adresse->email()).'">'.Manager::escape($adresse->email()).'</a></div>'.
         '<div class="description">'. Manager::escape(nl2br($adresse->description())) .'</div>';
    echo '</div>';
}
Registry::get('layout')->appendCss('bonnes-adresses.css');
?>
<span id="page_id">2</span>