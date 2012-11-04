$(document).ready(function(){
	$('#code-editor').tabs();

	$('#html-editor').keyup(function () { 
		$('#simulation').html($('#html-editor').val());
	});
});
