function ajaxSubmit(e) {
    var $form = $('#login-form');
    $.ajax({
        url: $form.attr('action'),
        type: $form.attr('method'),
        data: $form.serialize(),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        dataType: 'json',
        async: false,
        beforeSend: function(arr) {
            $('.alert').remove();
            $form.hide();
            $('.modal-body').append('<p align="center"><i class="icon-spin icon-spinner icon-4x muted"></i></p>');
        },
        error: function(data){
            $('.modal-body').prepend('<div class="alert alert-danger"><a class="close" href="#" data-dismiss="alert">×</a><h4 class="alert-heading">Oh snap! Your server took a nose dive!</h4>Internal Server error</div>');
            $('p .icon-spin').remove();
            $form.show(800, 'swing');
        },
        success: function(response) {
            if (response.status === 'OK') {
                window.location = response.redirect;
            } else if (response.status === 'FAILED') {
                var error;
                for (error in response.errors) {
                    $('.modal-body').prepend('<div class="alert alert-danger"><a class="close" href="#" data-dismiss="alert">×</a>'+ response.errors[error].message +'</div>');
                }
                $('.control-group').addClass('error');
                $('p .icon-spin').remove();
                $form.show(800, 'swing');
            }
        }
    });
    return false;
}

$(function() {

	// Link Action to get Login Panel
	$('.login-panel').click(function(el) {
		el.preventDefault();
		var url = $(this).attr('href');
		$.get(url, function(response){
			$('#modal-login').html(response);
		});
	});

	// On Loading a request add a spinner icon
	$('#modal-login').on('show', function () {
		$('#modal-login').html('<div class="modal-body"><p align="center"><i class="icon-spin icon-spinner icon-4x muted"></i></p></div>');
	});

	// When DOM is ready show button
	$('.btn').removeClass('hide');

});
