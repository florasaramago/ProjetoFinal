$(document).ready(function(){
	$('#html-editor').keyup(function () { 
		$('#simulation').html($('#html-editor').val());
	});
});
