$(document).ready(function()
{
	$('#code-editor').tabs();

	$('#html-editor').keyup(function () { 
		$('#simulation').html($('#html-editor').val());
	});

	$('#css-editor, #javascript-editor').keyup(function () { 
		$.ajax({
	     	url: '/index/update-file/',
	     	data: $(this),
	         success: function(response){
	         	console.log('yay');
	         }, 
	         error: function (data) {
	         	console.log('omg');
	         },
	         complete: function(data) {
	         	$('#simulation').html($('#html-editor').val());
	         },
			type: "POST", 
			dataType: "json"
		});
	});

	$('.sub-tab').on('click', function() {
		alert('uepa!');
		$.ajax({
	     	url: '/index/change-sub-tab/',
	     	data: 'file='+ $(this).text() +'&text='+ $(this).parent('.sub-tabs').parent('div').children('textarea').val(),
	         success: function(response){
	         	console.log(response);
	         }, 
	         error: function (data) {
	         	console.log('omg');
	         },
	         complete: function(data) {
	         	$('#simulation').html($('#html-editor').val());
	         },
			type: "POST", 
			dataType: "json"
		});
	});

	$('#resize').on('click', function() {
		$('#simulation').toggleClass('smaller');
	});
	
});