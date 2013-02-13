$(document).ready(function()
{
	//Create tabs
	$('#code-editor').tabs();

	//Create CodeMirror textarea for HTML
	var htmlEditor = CodeMirror.fromTextArea(document.getElementById('html-editor'), {
		lineNumbers: true, mode: "text/html", theme: "solarized", tabMode: "indent"
	});

	//Edit HTML code
	$('.CodeMirror, .cm-s-solarized').on('keyup', function () { 
		//Update simulator
		$('#simulation').html(htmlEditor.getValue());
	});

	//Change to CSS tab
	$('#ui-id-2').on('click', function() {
		//Set first sub-tab as active
		if(!$('#tabs-2').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-2').children('.sub-tabs').children('span:first').addClass('active-tab');
		}

		//Create CodeMirror textarea for JavaScript
		var cssEditor = CodeMirror.fromTextArea(document.getElementById('css-editor'), {
			lineNumbers: true, mode: "css", theme: "solarized"
		});

		//Edit CSS code
		$('.CodeMirror, .cm-s-solarized').on('keyup', function () { 
			//Check if it's CSS textarea
			if($(this).siblings('textarea').hasClass('css-editor')) {
				//Check which sub-tab is active
				var activeTab = $(this).siblings('.sub-tabs').children('.active-tab');
				console.log(activeTab.attr('file'));

				//Update the file on server
				$.ajax({
			     	url: '/index/update-file/',
			     	data: 'file='+ activeTab.attr('file') +'&text='+ cssEditor.getValue(),
			         success: function(response){
			         }, 
			         error: function (data) {
			         },
			         complete: function(data) {
			         	//Reload simulator
			         	$('#simulation').html(htmlEditor.getValue());
			         },
					type: "POST", 
					dataType: "json"
				});
			}
		});
	});

	//Change to JavaScript tag
	$('#ui-id-3').on('click', function() {
		//Set first sub-tab as active
		if(!$('#tabs-3').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-3').children('.sub-tabs').children('span:first').addClass('active-tab');
		}

		//Create CodeMirror textarea for JavaScript
		var jsEditor = CodeMirror.fromTextArea(document.getElementById('javascript-editor'), {
			lineNumbers: true, mode:  "javascript", theme: "solarized"
		});

		//Edit JavaScript code
		$('.CodeMirror, .cm-s-solarized').on('keyup', function () { 
			if($(this).siblings('textarea').hasClass('javascript-editor')) {
				$('#simulation').html(htmlEditor.getValue());
			}
		});
	});

	//Change sub-tab
	$('.sub-tab').on('click', function() {
		//Update active sub-tab
		$('.active-tab').removeClass('active-tab');
		$(this).addClass('active-tab');

		//Change code inside CodeMirror textarea
		$.ajax({
	     	url: '/index/change-sub-tab/',
	     	data: 'file='+ $('.active-tab').attr('file'),
	         success: function(response){
	         	//Remove other CodeMirror textareas
	         	$('#css-editor').siblings('.CodeMirror').remove();
	         	$('#javascript-editor').siblings('.CodeMirror').remove();

	         	//Load received code into correct textarea
	         	$('#' + $('.ui-tabs-active').attr('l') + '-editor').val(response);

	         	//Re-create CodeMirror textarea
	         	var cssEditor = CodeMirror.fromTextArea(document.getElementById('css-editor'), {
						lineNumbers: true, mode:  "css", theme: "solarized"
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

	//Resize simulator
	$('#resize').on('click', function() {
		$('#smartphone').toggleClass('smaller');
	});
	
});