$(document).ready(function() {

	
	$('#feedback').hide();
  
  validateLogin = function() {
    $('#feedback').hide();
	$('#feedback strong').empty();

	if ($('#login-username').val().length < 1) {

		$('#feedback strong').text('Informe o usuário');
		$('#login-username').focus();
		$('#feedback').show();

	} else if ($('#login-password').val().length < 1) {

		$('#feedback strong').text('Senha não informada!');
		$('#login-password').focus();
		$('#feedback').show();

    } else {
		$.ajax({
		   type: 'POST',
		   url: '/app/autenticador/admin',
		   dataType: 'json',
		   data: {'username': $('#login-username').val(),'password': $('#login-password').val()},
		   cache: false,
		   timeout: 16000,
		   beforeSend: function() {
			   $('#loginform input, button').attr('disabled','disabled');
			   $('#btndiv button').after('<img src="/images/loading.gif" width="35px" alt="Loading.." />');
		   },
		   success: function(data){
			   $('#loginform input, button').removeAttr('disabled');
			   $('#btndiv img').hide();
			   
				if (data.cod == 1) {
					$('#feedback strong').text(data.msg);
					$('#feedback').show();
				}
		   },
		   error:  function (jqXHR, timeout, message) {
			   var contentType = jqXHR.getResponseHeader("Content-Type");
			   if (jqXHR.status === 200 && contentType.toLowerCase().indexOf("text/html") >= 0) {
				   // assume that our login has expired - reload our current page
				   window.location.reload();
			   }
		   }
		});
	}
  };
  
  $('#submit').click(function(){
	  validateLogin();
  });
  
  $('#login-password').keyup(function(e){
	  if (e.which == 13)
		  validateLogin();
  });

//  alert(navigator.appVersion);

}); // ready
