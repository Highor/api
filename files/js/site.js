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

});