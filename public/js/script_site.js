$(document).ready(function()
{
	$('#code-editor').tabs();

	var htmlEditor = CodeMirror.fromTextArea(document.getElementById('html-editor'), {
		lineNumbers: true, mode: "text/html", theme: "solarized", tabMode: "indent"
	});

	$('.CodeMirror, .cm-s-solarized').on('keyup', function () { 
		alert('epa!');
		$('#simulation').html(htmlEditor.getValue());

		if($(this).siblings('textarea').hasClass('css')) {
			alert("yahoo!");
		}
	});

	$('#ui-id-2').on('click', function() {
		if(!$('#tabs-2').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-2').children('.sub-tabs').children('span:first').addClass('active-tab');
		}

		var cssEditor = CodeMirror.fromTextArea(document.getElementById('css-editor'), {
			lineNumbers: true, mode:  "css", theme: "solarized"
		});

		$('.CodeMirror, .cm-s-solarized').on('keyup', function () { 
			alert("1");
			if($(this).siblings('textarea').hasClass('css-editor')) {
				alert("2");
				var activeTab = $(this).siblings('.sub-tabs').children('.active-tab');

				$.ajax({
			     	url: '/index/update-file/',
			     	data: 'file='+ activeTab.attr('file') +'&text='+ cssEditor.getValue(),
			         success: function(response){
			         	alert("4");
			         	console.log(response);
			         }, 
			         error: function (data) {
			         	alert("5");
			         	console.log('omg');
			         },
			         complete: function(data) {
			         	alert("6");
			         	$('#simulation').html(htmlEditor.getValue());
			         },
					type: "POST", 
					dataType: "json"
				});
			}
		});
	});

	$('#ui-id-3').on('click', function() {
		if(!$('#tabs-3').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-3').children('.sub-tabs').children('span:first').addClass('active-tab');
		}

		var jsEditor = CodeMirror.fromTextArea(document.getElementById('javascript-editor'), {
			lineNumbers: true, mode:  "javascript", theme: "solarized"
		});

		$('.CodeMirror, .cm-s-solarized').on('keyup', function () { 
			if($(this).siblings('textarea').hasClass('javascript-editor')) {
				$('#simulation').html(htmlEditor.getValue());
			}
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