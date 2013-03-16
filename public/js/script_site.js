$(document).ready(function()
{
	var cssEditor;
	var jsEditor;

	//Create tabs
	$('#code-editor').tabs();

	//Create CodeMirror textarea for HTML
	var htmlEditor = CodeMirror.fromTextArea(document.getElementById('html-editor'), {
		lineNumbers: true, mode: "text/html", theme: "solarized", tabMode: "indent"
	});

	var iframe = document.getElementById('simulation');
	iframe.contentWindow.document.open('text/html', 'replace');
	iframe.contentWindow.document.write(String(htmlEditor.getValue()));
	iframe.contentWindow.document.close();

	//Edit HTML code
	$('.CodeMirror, .cm-s-solarized').on('keyup', function () { 
		//Update simulator
		//$('#simulation').html(htmlEditor.getValue());
		iframe.contentWindow.document.open('text/html', 'replace');
		iframe.contentWindow.document.write(String(htmlEditor.getValue()));
		iframe.contentWindow.document.close();
	});

	//Change to CSS tab
	$('#ui-id-2').on('click', function() {
		//Set first sub-tab as active
		if(!$('#tabs-2').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-2').children('.sub-tabs').children('span:first').addClass('active-tab');
			$('#tabs-2').children('.sub-tabs').children('span:first').trigger('click');
		}
	});

	//Change to JavaScript tab
	$('#ui-id-3').on('click', function() {
		//Set first sub-tab as active
		if(!$('#tabs-3').children('.sub-tabs').children('.sub-tab').hasClass('active-tab')) {
			$('#tabs-3').children('.sub-tabs').children('span:first').addClass('active-tab');
			$('#tabs-3').children('.sub-tabs').children('span:first').trigger('click');
		}
	});

	//Change sub-tab
	$('.sub-tab').on('click', function() {
		//Update active sub-tab
		$('.active-tab').removeClass('active-tab');
		$(this).addClass('active-tab');

		//Change code inside CodeMirror textarea according to selected sub-tab
		$.ajax({
	     	url: '/index/change-sub-tab/',
	     	data: 'file='+ $('.active-tab').attr('file'),
	         success: function(response) {
	         	//Remove other CodeMirror textareas
	         	$('#css-editor').siblings('.CodeMirror').remove();
	         	$('#javascript-editor').siblings('.CodeMirror').remove();

	         	//Load received code into correct textarea
	         	$('#' + $('.ui-tabs-active').attr('l') + '-editor').val(response);

	         	//Re-create CodeMirror textarea
	         	if($('.ui-tabs-active').attr('l') == "css") {
	         		//Create CSS textarea
	         		cssEditor = CodeMirror.fromTextArea(document.getElementById('css-editor'), {
							lineNumbers: true, mode:  "css", theme: "solarized"
						});

						//Load CSS code into CodeMirror textarea
						cssEditor.setValue(response);

						//Edit CSS code
						cssEditor.on("change", function() {
							//Check if it's CSS textarea
							if($('.CodeMirror, .cm-s-solarized').siblings('textarea').hasClass('css-editor')) {
								//Check which sub-tab is active
								var activeTab = $('.CodeMirror, .cm-s-solarized').siblings('.sub-tabs').children('.active-tab');
								//Update the file on server
								$.ajax({
							     	url: '/index/update-file/',
							     	data: 'file='+ activeTab.attr('file') +'&text='+ escape(cssEditor.getValue()),
							         success: function(response){
							         }, 
							         error: function (data) {
							         	console.log(data.responseText);
							         },
							         complete: function(data) {
							         	//Reload simulator
							         	//$('#simulation').html(htmlEditor.getValue());
							         	iframe.contentWindow.document.open('text/html', 'replace');
											iframe.contentWindow.document.write(String(htmlEditor.getValue()));
											iframe.contentWindow.document.close();
							         },
									type: "POST", 
									dataType: "json"
								});
							}
						});
	         	} else if ($('.ui-tabs-active').attr('l') == "javascript") {
	         		//Create JavaScript textarea
	         		jsEditor = CodeMirror.fromTextArea(document.getElementById('javascript-editor'), {
							lineNumbers: true, mode:  "javascript", theme: "solarized"
						});

						//Load JavaScript code into CodeMirror textarea
						jsEditor.setValue(response);

						//Edit JavaScript code
						jsEditor.on("change", function() {
							//Check if it's JavaScript textarea
							if($('.CodeMirror, .cm-s-solarized').siblings('textarea').hasClass('javascript-editor')) {
								//Check which sub-tab is active
								var activeTab = $('.CodeMirror, .cm-s-solarized').siblings('.sub-tabs').children('.active-tab');
								//Update the file on server
								$.ajax({
							     	url: '/index/update-file/',
							     	data: 'file='+ activeTab.attr('file') +'&text='+ escape(jsEditor.getValue()),
							         success: function(response){
							         }, 
							         error: function (data) {
							         	console.log(data.responseText);
							         },
							         complete: function(data) {
							         	//Reload simulator
							         	iframe.contentWindow.document.open('text/html', 'replace');
											iframe.contentWindow.document.write(String(htmlEditor.getValue()));
											iframe.contentWindow.document.close();
							         },
									type: "POST", 
									dataType: "json"
								});
							}
						});
	         	}	
	         }, 
	         error: function (data) {
	         },
	         complete: function(data) {
	         },
			type: "POST", 
			dataType: "json"
		});
	});

	//Resize simulator
	$('#resize').on('click', function() {
		$('#smartphone').toggleClass('smaller');
	});

	$('.library-checkbox').change(function() {
		if($(this).is(":checked")) {
			//Usuário marcou a checkbox
			$.ajax({
		     	url: '/index/add-library/',
		     	data: 'lib='+ $(this).attr('value')+'&html='+ escape(htmlEditor.getValue()),
		         success: function(response){
		         	console.log(response);
		         	htmlEditor.setValue(response);
		         }, 
		         error: function (data) {
		         	console.log(data.responseText);
		         },
		         complete: function(data) {
		         	//Reload simulator
		         	iframe.contentWindow.document.open('text/html', 'replace');
						iframe.contentWindow.document.write(String(htmlEditor.getValue()));
						iframe.contentWindow.document.close();
		         },
				type: "POST", 
				dataType: "json"
			});
		} else {
			//Usuário desmarcou a checkbox
			if($(this).attr('value') == 'jquery') {
				alert('desmarcou jQuery');
			} else if($(this).attr('value') == 'jquery-ui') {
				alert('desmarcou jQuery UI');
			} else if($(this).attr('value') == 'jquery-mobile') {
				alert('desmarcou jQuery Mobile');
			}
		}
	})
	
});