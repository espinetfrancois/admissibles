
function setDatePicker() {
	$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
	$('.champ_date').datepicker();
}
$(document).ready(function() {
	setDatePicker();
});