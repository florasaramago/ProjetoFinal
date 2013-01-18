//document.onload="prettyPrint()";

$(document).ready(function()
{
	//var myCodeMirror = CodeMirror.fromTextArea($('#css-editor'));

	// var myCodeMirror = CodeMirror($('#css-editor', { mode:  "css" });

	$('#code-editor').tabs();

	//var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('html-editor', { mode:  "html" }));

	var editor = CodeMirror.fromTextArea(document.getElementById('html-editor'), {
		lineNumbers: true, mode: "text/html", theme: "solarized", tabMode: "indent"
	});
 
 	//$('.CodeMirror').addClass('cm-s-solarized').addClass('cm-s-light');
	//editor.setOption("theme", "eclipse");

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

	$('#ui-id-2').on('click', function() {
		if(!$('#tabs-2').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-2').children('.sub-tabs').children('span:first').addClass('active-tab');
		}

		//var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('css-editor', { mode:  "css" }));

		var editor = CodeMirror.fromTextArea(document.getElementById('css-editor'), {
			lineNumbers: true, mode:  "css", theme: "solarized"
		});
 
 	//$('.CodeMirror').addClass('cm-s-solarized').addClass('cm-s-light');
	});

	$('#ui-id-3').on('click', function() {
		if(!$('#tabs-3').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-3').children('.sub-tabs').children('span:first').addClass('active-tab');
		}

		//var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('javascript-editor', { mode:  "javascript" }));
		var editor = CodeMirror.fromTextArea(document.getElementById('javascript-editor'), {
			lineNumbers: true, mode:  "javascript", theme: "solarized"
		});
	});

	$('.sub-tab').on('click', function() {
		$('.active-tab').removeClass('active-tab');
		//$(this).siblings().removeClass('active-tab');
		$(this).addClass('active-tab');

		$.ajax({
	     	url: '/index/change-sub-tab/',
	     	data: 'file='+ $('.active-tab').attr('file'),
	         success: function(response){
	         	$('#' + $('.ui-tabs-active').attr('l') + '-editor').val(response);
	         	$('#pretty-javascript').html(response);
	         	prettyPrint();
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