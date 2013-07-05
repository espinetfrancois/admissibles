/**
 * Script de gestion de l'envoi des mails de sondage
 */

function submitRetex(event) {
	event.preventDefault();
	$("<div>Envoi des mails en cours...</div>").dialog({modal : true});
	//submit via ajax
	var post = $.post($(this).attr('action') ,$(this).serialize());
	post.done(function(data) {
		$(".ui-dialog-content").dialog("close");
		$(data).wrap('<div />').delay(1).dialog({height : 100});
	});
	port.fail(function() {
		$("<div>La page n'a pas répondu!</div>").dialog();
	});
	//prevent page from submitting
	return false;
}


$(document).ready(function() {
	$('.envoi-retex').submit(submitRetex);
});

