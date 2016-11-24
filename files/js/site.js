$(document).ready(function() {

	if ($('.alert').length == 1) {
		$('#createApp').modal('show');	
	}

	$('#appuseplaceholder').click(function() {
		$('input[name="authkey"]').val($('input[name="authkey"]').attr('placeholder'));
	});

	$('.deleteapp').click(function() {
		$('span.appName').text($(this).parent().find('.currentAppName').text());
		$('input[name="deleteAppId"]').val($(this).parent().find('input[name="appId"]').val());
	});

	$('.authCopy').click(function() {
		var text = $('.copyKey').text();
		window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
	});

	$('.tryAPI').click(function() {
		var prefixurl = $(this).parent().parent().find('input[name="prefixurl"]').val();
		var version = $('body').find('select[name="version"]').val();
		var apiUrl = $(this).parent().parent().find('input[name="apiurl"]').val();
		var fullApiUrl = prefixurl + version + '/' + apiUrl;
		var apiType = $(this).parent().parent().find('select[name="apitype"]').val();
		var username = $('input[name="basic_user"]').val();
		var password = $('input[name="basic_pass"]').val();
		console.log(username);
		console.log(password);
		var request = $.ajax({
			url: fullApiUrl,
			beforeSend: function( xhr ) {
				xhr.setRequestHeader("Authorization", "Basic " + btoa(username + ":" + password));
			},
			method: apiType,
			data: {},
			dataType: "json"
		});

		request.done(function( msg ) {
			console.log(msg);
		});

		request.fail(function( jqXHR, textStatus ) {
			console.log( "Request failed: " + textStatus );
		});
	});

});