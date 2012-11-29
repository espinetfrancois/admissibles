/**
 * Script pour la coloration du lien actif dans le menu
 * Le fonctionnement est le suivant :
 *  - l'objet Layout envoie l'id de la page dans un span d'id "page_id" qui n'est pas affiché (caché en css)
 *  - les id des pages sont répétés dans le menu
 * 	- le script ci-présent affecte la classe .active au lien correspondant dans le menu
 */


function coloreLien(id) {
	if (id != "0") {
		//coloration du parent
		$('li#'+id).parent('ul').parent('li').addClass("active");
		//coloration du lien actif
		$('li#'+id).addClass('active');
	}
}

function gestionLienAccueil() {
	$("#12:not(.active)").hide();
}

$(document).ready(function (){
	coloreLien($('#page_id').html());
	gestionLienAccueil();
});