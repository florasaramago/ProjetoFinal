$(document).ready(function()
{
	var cssEditor;
	var jsEditor;
	var key;
	var iframe = document.getElementById('iphone-simulation');
	var baseURL = "http://projetofinal.dev";
	var currentPhone = "ios";

	//Create tabs
	$('#code-editor').tabs();

	//Create CodeMirror textarea for HTML
	var htmlEditor = CodeMirror.fromTextArea(document.getElementById('html-editor'), {
		lineNumbers: true, lineWrapping: true, mode: "text/html", theme: "solarized", tabMode: "indent"
	});

	if($('#user-agent-field').val() == "android") {
		currentPhone = "android";
		$('#code-editor').css('width', '62%');
		$('#second-controls').css('margin-left', '47px');
		$('#third-controls').css('margin-left', '60px');
		$("#android-radio").attr('checked', true);
		iframe = document.getElementById('android-simulation');
	}

	$('.dark-background').css('width', $(document).width()).css('height', $(document).height());
	$('.lib-name-disabled').each(function() {
		$('#'+$(this).attr('for')).attr('disabled', 'disabled');
	});
	

	//Get session key to generate correct URLs for files
	$.ajax({
		url: '/index/session-key/',
			success: function(response){
				key = String(response);

				//Update simulator with new contents
				function updateSimulator() {
					//Get content from HTML textarea and replace fake URLs with correct ones
					var htmlCode = String(htmlEditor.getValue()
									.replace("/temp/user/default.css", baseURL+"/temp/"+key+"/user/default.css")
									.replace("/temp/user/default.js", baseURL+"/temp/"+key+"/user/default.js"));

					//Replace fake CSS URLs with correct ones before updating simulator
					$('#tabs-2').find('.sub-tab').each(function() {
						if($(this).attr('file') != "/temp/user/default.css") {
							htmlCode = htmlCode.replace("/"+$(this).attr('file'), baseURL+"/temp/"+key+"/" + $(this).attr('file'));
						}
					});
					
					//Replace fake CSS URLs with correct ones before updating simulator
					$('#tabs-3').find('.sub-tab').each(function() {
						if($(this).attr('file') != "/temp/user/default.js") {
							htmlCode = htmlCode.replace("/"+$(this).attr('file'), baseURL+"/temp/"+key+"/" + $(this).attr('file'));
						}
					});		

					//Replaces simulator's HTML with new one, with correct URLs
					iframe.contentWindow.document.open('text/html', 'replace');
					iframe.contentWindow.document.write(htmlCode);
					iframe.contentWindow.document.close();
				}

				//Initial simulator update
				updateSimulator();

				//Edit HTML code
				$('.CodeMirror-wrap').find('textarea').typing({
					stop: function() {
						updateSimulator();
					},
					delay: 700
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

					//Get correct file name of current file, to be updated on editing
					if($('.active-tab').attr('file').indexOf("/temp/user/default.css") != -1) {
						var fileName = $('.active-tab').attr('file')
																.replace("/temp/user/default.css", baseURL+"/temp/"+key+"/user/default.css");
					} else if($('.active-tab').attr('file').indexOf("/temp/user/default.js") != -1) {
						var fileName = $('.active-tab').attr('file')
																.replace("/temp/user/default.js", baseURL+"/temp/"+key+"/user/default.js");
					} else {
						var fileName = baseURL+"/temp/"+key+"/" + $('.active-tab').attr('file');
					}

					//Change code inside CodeMirror textarea according to selected sub-tab
					$.ajax({
						url: '/index/change-sub-tab/',
						data: 'file='+ fileName,
							success: function(response) {
								//Remove other CodeMirror textareas
								$('#css-editor').siblings('.CodeMirror').remove();
								$('#javascript-editor').siblings('.CodeMirror').remove();

								//If the current tab is the CSS tab
								if($('.ui-tabs-active').attr('l') == "css") {
									//Create CSS textarea
									cssEditor = CodeMirror.fromTextArea(document.getElementById('css-editor'), {
										lineNumbers: true, lineWrapping: true, mode:  "css", theme: "solarized"
									});

									//Load CSS code into CodeMirror textarea
									cssEditor.setValue(response);

									//Edit CSS code
									$('.CodeMirror-wrap').find('textarea').typing({
										stop: function() {
											//Confirm if it's CSS textarea
											if($('.CodeMirror, .cm-s-solarized').siblings('textarea').hasClass('css-editor')) {

												//Check which sub-tab is active
												var activeTab = $('.CodeMirror, .cm-s-solarized').siblings('.sub-tabs').children('.active-tab');
												
												//Get correct file name of current file
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
										},
										delay: 700
									});

								//If the current tab is the JS tab
								} else if ($('.ui-tabs-active').attr('l') == "javascript") {
									//Create JavaScript textarea
									jsEditor = CodeMirror.fromTextArea(document.getElementById('javascript-editor'), {
										lineNumbers: true, lineWrapping: true, mode:  "javascript", theme: "solarized"
									});

									//Load JavaScript code into CodeMirror textarea
									jsEditor.setValue(response);

									//Edit JavaScript code
									$('.CodeMirror-wrap').find('textarea').typing({
										stop: function() {
											//Confirm if it's JavaScript textarea
											if($('.CodeMirror, .cm-s-solarized').siblings('textarea').hasClass('javascript-editor')) {
												//Check which sub-tab is active
												var activeTab = $('.CodeMirror, .cm-s-solarized').siblings('.sub-tabs').children('.active-tab');

												//Get correct file name of current file
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
										},
										delay: 700
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

				//Change simulator type
				$("input[name='sim-type']").change(function() {
					if ($("input[name='sim-type']:checked").val() == 'ios') {
						currentPhone = "iphone";
						$('#android').addClass('hidden');
						$('#iphone').removeClass('hidden');
						$('#code-editor').css('width', '68%');
						$('#second-controls').css('margin-left', '60px');
						$('#third-controls').css('margin-left', '85px');
						$('#user-agent-field').attr('value', 'ios');
						iframe = document.getElementById('iphone-simulation');
						updateSimulator();
					} else if ($("input[name='sim-type']:checked").val() == 'android') {
						currentPhone = "android";
						$('#iphone').addClass('hidden');
						$('#android').removeClass('hidden');
						$('#code-editor').css('width', '62%');
						$('#second-controls').css('margin-left', '47px');
						$('#third-controls').css('margin-left', '60px');
						$('#user-agent-field').attr('value', 'android');
						iframe = document.getElementById('android-simulation');
						updateSimulator();
					}
				});

				//Add external libraries
				$('.library-checkbox').change(function() {
					if($(this).is(":checked")) {
						//User checked checkbox
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
						//User unchecked checkbox
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

				//Clear editor
				$('#clear-editor').on('click', function() {
					$('.dark-background').show();
					$('#confirm-clear').dialog({ dialogClass: 'no-close'}); 
					$('#clear-editor :button').blur();

					$('.no').on('click', function() {
						$(this).parents(".ui-dialog-content").dialog('close');
						$('.dark-background').hide();
					});

					$('.yes').on('click', function() {
						var ok1 = false, ok2 = false;

						$('.ui-dialog').fadeOut(function() {
							$('#loader').fadeIn();
						})

						//Clear CSS
						$.ajax({
							url: '/index/update-file/',
							data: 'file=/temp/'+key+'/user/default.css&text=',
								success: function(response){
								}, 
								error: function (data) {
									console.log(data.responseText);
								},
								complete: function(data) {

									//Clear JavaScript, after CSS cleared
									$.ajax({
										url: '/index/update-file/',
										data: 'file=/temp/'+key+'/user/default.js&text=',
											success: function(response){
												ok2 = true;
											}, 
											error: function (data) {
												console.log(data.responseText);
											},
											complete: function(data) {
												//Clear HTML, after JS cleared
												window.location = baseURL + "?os=" + currentPhone;
											},
										type: "POST", 
										dataType: "json"
									});

								},
							type: "POST", 
							dataType: "json"
						});
					});
				});

				//Ask for confirmation before loading a new website
				$('#submit-url-button').on('click', function(e) {
					e.preventDefault();
					$('.dark-background').show();

					$('#confirm-clear').dialog({ dialogClass: 'no-close'}); 
					$('#clear-editor :button').blur();

					$('.no').on('click', function() {
						$(this).parents(".ui-dialog-content").dialog('close');
						$('.dark-background').hide();
					});

					$('.yes').on('click', function() {
						$('.ui-dialog').fadeOut(function() {
							$('#loader').fadeIn();
						})
						$('.url-form').submit();
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