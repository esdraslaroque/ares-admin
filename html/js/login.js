$(document).ready(function() {

  $('#feedback').hide();
  
  $('#submit').click(function(){
	  if ($('.file').val().length < 1) {
		  $('#feedback p').attr('class','error').text('Selecione uma chave ARES');
		  $('#feedback').show().fadeOut(8000);
		  
	  } else if ($('.code').val().length < 1) {
		  $('#feedback p').attr('class','error').text('Número de acesso não informado');
		  $('#feedback').show().fadeOut(8000);
		  
	  } else if (! fileValidate ( $('.file').val() )) {
			$('#feedback p').attr('class','error').text('Chave inválida! Selecione um arquivo .pub');
			$('#feedback').show().fadeOut(8000);
			$('.file').val('').focus();
	  } else
		  codeValidate( $('.code').val() );
  });

  function codeValidate(cCode) {
       $.ajax({
           type: 'GET',
           url: '/app/autenticador/codeValidate/'+cCode,
	   cache: false,
	   timeout: 8000,
           beforeSend: function() { 
			  $('#feedback p').attr('class','info').text('Validando número de acesso..');
			  $('#feedback').show();
			}
        }).done(function(data) {
		   $('#feedback p').attr('class','info').text('Validando chave ARES..');
		   if (data == 0)
			   $('form').submit();
		   else {
	        	$('#feedback p').attr('class','error').text('Número de acesso incorreto. Tente novamente!');
				$('#feedback').show().fadeOut(8000);
				$('.code').val('').focus();
				$('#box img').attr('src','/app/autenticador/randomImage');
		   }
        }).fail(function(jqXHR, textStatus) {
			if (textStatus === 'timeout') {
			   $('#feedback p').attr('class','error').text('Tempo de processamento esgotado. Reconecte seu ARES.');
			   $('#feedback').show();
			}
        });
  }

  function fileValidate(cChave) {
	var extension = cChave.split('.').pop();
	if (extension == 'pub')
	   return true;
	return false;
  }
});
