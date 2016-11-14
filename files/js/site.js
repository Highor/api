$(document).ready(function() {

	if ($('.alert').length == 1) {
		$('#createApp').modal('show');	
	}

	$('#appuseplaceholder').click(function() {
		$('input[name="authkey"]').val($('input[name="authkey"]').attr('placeholder'));
	});

});