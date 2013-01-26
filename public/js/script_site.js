$(document).ready(function()
{
	$('#code-editor').tabs();

	var htmlEditor = CodeMirror.fromTextArea(document.getElementById('html-editor'), {
		lineNumbers: true, mode: "text/html", theme: "solarized", tabMode: "indent"
	});

	$('.CodeMirror, .cm-s-solarized').on('keyup', function () { 
		$('#simulation').html(htmlEditor.getValue());
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

	$('#ui-id-2').on('click', function() {
		if(!$('#tabs-2').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-2').children('.sub-tabs').children('span:first').addClass('active-tab');
		}

		var cssEditor = CodeMirror.fromTextArea(document.getElementById('css-editor'), {
			lineNumbers: true, mode:  "css", theme: "solarized"
		});
	});

	$('#ui-id-3').on('click', function() {
		if(!$('#tabs-3').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-3').children('.sub-tabs').children('span:first').addClass('active-tab');
		}

		var jsEditor = CodeMirror.fromTextArea(document.getElementById('javascript-editor'), {
			lineNumbers: true, mode:  "javascript", theme: "solarized"
		});
	});

	$('.sub-tab').on('click', function() {
		$('.active-tab').removeClass('active-tab');
		$(this).addClass('active-tab');

		$.ajax({
	     	url: '/index/change-sub-tab/',
	     	data: 'file='+ $('.active-tab').attr('file'),
	         success: function(response){
	         	$('#css-editor').siblings('.CodeMirror').remove();
	         	$('#javascript-editor').siblings('.CodeMirror').remove();

	         	$('#' + $('.ui-tabs-active').attr('l') + '-editor').val(response);

	         	var editor = CodeMirror.fromTextArea(document.getElementById($('.ui-tabs-active').attr('l') + '-editor'), {
						lineNumbers: true, mode:  $('.ui-tabs-active').attr('l'), theme: "solarized"
					});
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
		$('#smartphone').toggleClass('smaller');
	});
	
});