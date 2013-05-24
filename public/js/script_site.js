$(document).ready(function()
{
	var cssEditor;
	var jsEditor;
	var key;
	var iframe = document.getElementById('simulation');

	//Create tabs
	$('#code-editor').tabs();

	//Create CodeMirror textarea for HTML
	var htmlEditor = CodeMirror.fromTextArea(document.getElementById('html-editor'), {
		lineNumbers: true, lineWrapping: true, mode: "text/html", theme: "solarized", tabMode: "indent"
	});

	$.ajax({
		url: '/index/session-key/',
			success: function(response){
				key = String(response);

				function updateSimulator() {
					var htmlCode = String(htmlEditor.getValue()
									.replace("/temp/user/default.css", "/temp/"+key+"/user/default.css")
									.replace("/temp/user/default.js", "/temp/"+key+"/user/default.js"));

					$('#tabs-2').find('.sub-tab').each(function() {
						if($(this).attr('file') != "/temp/user/default.css") {
							htmlCode = htmlCode.replace("/"+$(this).attr('file'), "http://projetofinal.dev/temp/"+key+"/" + $(this).attr('file'));
						}
					});
					
					$('#tabs-3').find('.sub-tab').each(function() {
						if($(this).attr('file') != "/temp/user/default.js") {
							htmlCode = htmlCode.replace("/"+$(this).attr('file'), "http://projetofinal.dev/temp/"+key+"/" + $(this).attr('file'));
						}
					});		

					console.log(htmlCode);

					iframe.contentWindow.document.open('text/html', 'replace');
					iframe.contentWindow.document.write(htmlCode);
					iframe.contentWindow.document.close();
				}

				updateSimulator();

				//Edit HTML code
				$('.CodeMirror, .cm-s-solarized').on('keyup', function () { 
					//Update simulator
					updateSimulator();
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
					if($('.active-tab').attr('file').indexOf("/temp/user/default.css") != -1) {
						var fileName = $('.active-tab').attr('file')
																.replace("/temp/user/default.css", "/temp/"+key+"/user/default.css");
					} else if($('.active-tab').attr('file').indexOf("/temp/user/default.js") != -1) {
						var fileName = $('.active-tab').attr('file')
																.replace("/temp/user/default.js", "/temp/"+key+"/user/default.js");
					} else {
						var fileName = "/temp/"+key+"/" + $('.active-tab').attr('file');
					}

					//Change code inside CodeMirror textarea according to selected sub-tab
					$.ajax({
						url: '/index/change-sub-tab/',
						data: 'file='+ fileName,
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
										lineNumbers: true, lineWrapping: true, mode:  "css", theme: "solarized"
									});

									//Load CSS code into CodeMirror textarea
									cssEditor.setValue(response);

									//Edit CSS code
									cssEditor.on("change", function() {
										//Check if it's CSS textarea
										if($('.CodeMirror, .cm-s-solarized').siblings('textarea').hasClass('css-editor')) {
											//Check which sub-tab is active
											var activeTab = $('.CodeMirror, .cm-s-solarized').siblings('.sub-tabs').children('.active-tab');
											
											if(activeTab.attr('file').indexOf("/temp/user/default.css") != -1) {
												var fileName = activeTab.attr('file').replace("/temp/user/default.css", "/temp/"+key+"/user/default.css");
											} else {
												var fileName = "/temp/"+key+"/" + activeTab.attr('file');
											}

											//Update the file on server
											$.ajax({
												url: '/index/update-file/',
												data: 'file='+ fileName +'&text='+ escape(cssEditor.getValue()),
													success: function(response){
													}, 
													error: function (data) {
														console.log(data.responseText);
													},
													complete: function(data) {
														//Reload simulator
														updateSimulator();
													},
												type: "POST", 
												dataType: "json"
											});
										}
									});
								} else if ($('.ui-tabs-active').attr('l') == "javascript") {
									//Create JavaScript textarea
									jsEditor = CodeMirror.fromTextArea(document.getElementById('javascript-editor'), {
										lineNumbers: true, lineWrapping: true, mode:  "javascript", theme: "solarized"
									});

									//Load JavaScript code into CodeMirror textarea
									jsEditor.setValue(response);

									//Edit JavaScript code
									jsEditor.on("change", function() {
										//Check if it's JavaScript textarea
										if($('.CodeMirror, .cm-s-solarized').siblings('textarea').hasClass('javascript-editor')) {
											//Check which sub-tab is active
											var activeTab = $('.CodeMirror, .cm-s-solarized').siblings('.sub-tabs').children('.active-tab');

											if(activeTab.attr('file').indexOf("/temp/user/default.js") != -1) {
												var fileName = activeTab.attr('file').replace("/temp/user/default.js", "/temp/"+key+"/user/default.js");
											} else {
												var fileName = "/temp/"+key+"/" + activeTab.attr('file');
											}
											//Update the file on server
											$.ajax({
												url: '/index/update-file/',
												data: 'file='+ fileName +'&text='+ escape(jsEditor.getValue()),
													success: function(response){
													}, 
													error: function (data) {
														console.log(data.responseText);
													},
													complete: function(data) {
														//Reload simulator
														updateSimulator();
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

				$('.library-checkbox').change(function() {
					if($(this).is(":checked")) {
						//Usuário marcou a checkbox
						$.ajax({
							url: '/index/add-library/',
							data: 'lib='+ $(this).attr('value')+'&html='+ escape(htmlEditor.getValue()),
								success: function(response){
									htmlEditor.setValue(response);
								}, 
								error: function (data) {
									console.log(data.responseText);
								},
								complete: function(data) {
									//Reload simulator
									updateSimulator();
								},
							type: "POST", 
							dataType: "json"
						});
					} else {
						//Usuário desmarcou a checkbox
						$.ajax({
							url: '/index/remove-library/',
							data: 'lib='+ $(this).attr('value')+'&html='+ escape(htmlEditor.getValue()),
								success: function(response){
									htmlEditor.setValue(response);
								}, 
								error: function (data) {
									console.log(data.responseText);
								},
								complete: function(data) {
									//Reload simulator
									updateSimulator();
								},
							type: "POST", 
							dataType: "json"
						});
					}
				});

				$('#clear-editor').on('click', function() {
					$('#confirm-clear').dialog({ dialogClass: 'no-close'}); 
					$('#clear-editor :button').blur();

					$('.no').on('click', function() {
						$(this).parents(".ui-dialog-content").dialog('close');
					});

					$('.yes').on('click', function() {
						location.reload();
					});
				});
			}, 
			error: function (data) {
				console.log(data.responseText);
			},
			complete: function(data) {
			},
		type: "POST", 
		dataType: "json"
	});

	//Resize simulator
	$('.resize').change(function() {
		if($(this).is(":checked")) {
			if($(this).attr('value') == "small") {
				$('#smartphone').addClass('smaller');
			} else {
				$('#smartphone').removeClass('smaller');
			}  
		}
	});
});