$(document).ready(function()
{
	$('#code-editor').tabs();

	$('#html-editor').keyup(function () { 
		$('#simulation').html($('#html-editor').val());
	});

	$('#css-editor, #javascript-editor').keyup(function () { 
		$.ajax({
	     	url: '/index/update-file/',
	     	data: 'file='+ $(this).attr('file') +'&text='+ $(this).parent('.sub-tabs').parent('div').children('textarea').val(),
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

	$('.sub-tab').on('click', function() {
		$(this).siblings().removeClass('active-tab');
		$(this).addClass('active-tab');

		$.ajax({
	     	url: '/index/change-sub-tab/',
	     	data: 'file='+ $('.active-tab').attr('file'),
	         success: function(response){
	         	$('#css-editor').val(response);
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