<div id="box">
	<form action="/app/autenticador/uploadKeyTest" method="post" enctype="multipart/form-data">
        <label class="lChave">Chave de acesso</label> <input name="cChave" class="file" type="file" />
        <label class="lCode">Número de acesso</label> <input name="cCode" class="code" type="text" maxlength="5" />
        <img alt="número de acesso" src="/app/autenticador/randomImage" />
        <input id="submit" type="submit" value="Autenticar" class="auth" />
    </form>
</div>

<div id="feedback">
	<p></p>
</div>
<div id="rules">
	<center><p></p></center>
	<table id="dataTable" width="100%">
		<thead>
			<tr>
				<th>Id</th>
				<th>Regra de Acesso</th>
			</tr>
		</thead>
	</table>
</div>

<script type="text/javascript">
	$('#rules').hide();
</script>
    
<script src="/js/jquery.form.js"></script>

<script>
(function() {

function codeValidate(cCode, keyname) {
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
         if (data == 0) {
        	 gpgValidate(keyname);  
         } else {
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

function gpgValidate(chave) {
	$.ajax({
	   url: '/gpgValidate',
	   type: 'GET',
	   data: {'keyname': chave},
	   dataType: 'json',
	   cache: false,
	   timeout: 18000
	}).done(function(a) {
		if (a.cod == 0) {
		   $('#box').hide();
		   $('#feedback p').attr('class','info').text('Carregando permissões para '+a.login+'..');
		   $('#feedback').show();

		   fwLoad(a.conexao_id, a.usuario_id, a.login, a.expire);

		} else if (a.cod == 120) {
		   $('#box').hide();
		   $('#feedback p').attr('class','error').text(a.msg+' (Erro: '+a.cod+')');
		   $('#feedback').show();
		} else {
		   $('#feedback p').attr('class','error').text(a.msg+' (Erro: '+a.cod+')');
		   $('#feedback').show().fadeOut(12000);
		}
	}).fail(function(jqXHR, textStatus) {
                if (textStatus === 'timeout') {
                   $('#feedback p').attr('class','error').text('Tempo de processamento esgotado. Reconecte seu ARES.');
                   $('#feedback').show();
                }
	});
}

function fwLoad(conexao_id, usuario_id, login, expire) {
    $.ajax({
       url: '/fwLoad',
       type: 'GET',
       data: {'conexao_id': conexao_id, 'usuario_id': usuario_id},
       dataType: 'json',
       cache: false,
       timeout: 8000
    }).done(function(b) {
            if (b.cod == 0) {
               $('#box').hide();
               showRules(login, expire);
               $('#feedback p').attr('class','succes').text('ARES autenticado com sucesso!');
               $('#feedback').show();
            } else {
	   $('#box').show();
               $('#feedback p').attr('class','error').text(b.msg+' (Erro: '+b.cod+')');
               $('#feedback').show().fadeOut(12000);
            }
    }).fail(function(jqXHR, textStatus) {
            if (textStatus === 'timeout') {
               $('#feedback p').attr('class','error').text('Tempo de processamento esgotado. Reconecte seu ARES.');
               $('#feedback').show();
            }
    });
}

function showRules(login, expire) {
	var dateBr = expire.split('-');
	$('#rules p').text('Chave válida até '+dateBr[2]+'/'+dateBr[1]+'/'+dateBr[0]);
	
	$.ajax({
        type: "GET",
        url: '/app/autenticador/showRules/'+login,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        cache: false,
        success: function (data) {
	        var trHTML = '';
	                
	        $.each(data, function (i, item) {
	            trHTML += '<tr><td align="center">' + item.id + '</td><td>' + item.descricao + '</td></tr>';
	        });
	        
	        $('#dataTable').append(trHTML);
        },
        error: function (msg) {
            alert('Falha ao carregar permissões.');
        }
    });
	
	$('#rules').show();
}

$('form').ajaxForm({
    beforeSend: function() {
        if ($('.file').val().length < 1) {
        	$('#feedback p').attr('class','error').text('Selecione uma chave ARES');
        	$('#feedback').show().fadeOut(8000);
        	return false;
        } else if ($('.code').val().length < 1) {
        	$('#feedback p').attr('class','error').text('Número de acesso não informado');
        	$('#feedback').show().fadeOut(8000);
        	return false;
        } else if (! fileValidate ( $('.file').val() )) {
			$('#feedback p').attr('class','error').text('Chave inválida! Selecione um arquivo .pub');
			$('#feedback').show().fadeOut(8000);
			$('.file').val('').focus();
        	return false;
		} 
        
        function fileValidate(cChave) {
        	var extension = cChave.split('.').pop();
        	if (extension == 'pub')
        	   return true;
        	return false;
        }
    },
	complete: function(xhr) {
		if (xhr.responseText == 1) {
			$('#feedback p').attr('class','error').text('Problema interno na validacao da chave. Tente novamente');
			$('#feedback').show().fadeOut(8000);
		} else codeValidate($('.code').val(), xhr.responseText);
	}
}); 

})();       
</script>