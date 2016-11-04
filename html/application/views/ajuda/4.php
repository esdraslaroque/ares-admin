<head>
    <meta name="title" content="Guia Usuários">

</head>

<div id="passo1">

<div style="background: #8CD4CC; padding: 30px 0; min-height: 300px">
    <div class="container content">
        <div class="row">
            <div class="col-md-6" >
                <img src="/images/wiki/usuarios.jpg" class="img-thumbnail">
            </div>
            <div class="col-md-6" style="color: #fff; font-size: 14px;">
                <p>No Modulo usurários é possivel fazer: </p>
                <ul>
                    <li>Criar acesso ARES para um usuário.</li>
                    <li>Editar as permissões do usuário.</li>
                    <li>Renovar o acesso ARES para um usuário.</li>
                    <li>Gerar novo Kit ARES.</li>
                    <li>Excluir usuário.</li>
                    <li>Pesquisar a situação de um usuário.</li>
                    <li>Habilitar/Desabilitar o acesso do usuário.</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container-fluid">
      <div id="loginbox" style="margin-top: 10px;"
        class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
          <div class="panel-heading"><i class="glyphicon glyphicon-info-sign"></i> &nbsp;Legenda de icones da tabela:</div>
          <div style="padding-top: 10px" class="panel-body">
              <ul>
              <li><a class="text-danger icon"><i class="glyphicon glyphicon-flag"></i></a> - Chave expirada;</li>
              <li><a class="text-warn icon"><i class="glyphicon glyphicon-flag"></i></a> - Chave preste a expirar. Colocando o curso em cima irá aparacer um balãozinho;</li>
              <li><a class="icon"><i class="glyphicon glyphicon-briefcase"></i></a> - Administrador;</li>
              <li><a class="text-danger icon"><i class="glyphicon glyphicon-ban-circle"></i></a> -  Fora do grupo ARES no AD. Colocando o curso em cima irá aparacer um balãozinho;</li>
              <li><a class="text-muted text-italic">Usuário</a> -  Usuário desabilitado;</li>
              <li><a class="text-danger icon"><i class="glyphicon glyphicon-ban-circle"></i></a> - Bloqueado no AD. Colocando o curso em cima irá aparacer um balãozinho;</li>
              </ul>
          </div>
        </div>
      </div>
    </div>

</div>

<div style="background: #fff; padding: 30px 0; min-height: 300px">
    <div class="container content">
        <div class="row">
            <div class="col-md-6" style="color: #555; font-size: 14px;">
                <p><b>Para Criação de usuário:</b></p>
                <p>Clique no botão <a><i class="glyphicon glyphicon-plus-sign"></i> Novo</a>, no canto superior esquerdo:<p>
                <p>Mostra quantos acessos houve no mês atual.</p>
                <p>Digitar no campo Login o usuário de rede, caso não apareça o nome embaixo, é porque o usuário não existe no AD, se aparecer selecione e os demais campos nome e e-mail, serão preenchido automaticamente;<p>
                <p>Insira o numero do processo;<p>
                <p>Selecione o período de validade;<p>
                <p>Selecione as permissões de acesso que o usuário vai ter.<p>
            </div>
            <div class="col-md-6">
                <img src="/images/wiki/novo_usuario.jpg" height="290" width="290" class="img-thumbnail">
            </div>
        </div>
    </div>
</div>


<div style="background: #97BBCC; padding: 30px 0; min-height: 300px">
    <div class="container content">
        <div class="row">
            <div class="col-md-6">
                <img src="/images/wiki/editar_usuario.jpg" height="290" width="290" class="img-thumbnail">
            </div>
            <div class="col-md-6" style="color: #fff; font-size: 14px;">
                <p><b>Para Editar as permissões do usuário:</b></p>
                <p>Clique no botão <a><i class="glyphicon glyphicon-pencil"></i> Editar</a>, no canto direto da coluna Ações;</p>
                <p>Uma janela é exibida com os dados do usuário e com a opção: quando foi criado o ultima ARES, quem criou a chave, e-mail, nome e permissões.</p>

                <p>Mostra o tráfego de Rede em Kb/s de dados recebidos e enviados pelo servidor, em um intervalo de 1 hora e atualizado de 1 em 1 minuto;</p>
                <p>Abaixo é exibido o total no [MES/DIA];</p>
            </div>
        </div>
    </div>
    <div class="container-fluid">
      <div id="loginbox" style="margin-top: 10px;"
        class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
          <div class="panel-heading"><i class="glyphicon glyphicon-info-sign"></i> &nbsp;Botão de Ações:</div>
          <div style="padding-top: 10px" class="panel-body">
          <ul>
          <li><a class="icon"><i class="glyphicon glyphicon-remove text-danger"></i></a> - Desabilitar Usuário;</li>
          <li><a class="icon"><i class="glyphicon glyphicon-ok text-success"></i></a> - Habilitar Usuário;</li>
          <li><a class="icon"><i class="glyphicon icon-key"></i></a> - Renovar chave;</li>
          <li><a class="icon"><i class="glyphicon glyphicon-compressed"></i></a> - Gerar kit ares e enviar para o e-mail do usuário;</li>
          <li><a class="icon"><i class="glyphicon glyphicon-pencil"></i></a> - Editar Usuário;</li>
          <li><a class="icon"><i class="glyphicon glyphicon-trash"></i></a> - Excluir Usuário;</li>
          </ul>
          </div>
        </div>
      </div>
    </div>
</div>

<div style="background: #fff; padding: 30px 0; min-height: 300px">
    <div class="container content">
        <div class="row">
            <div class="col-md-6" style="color: #555; font-size: 14px;">
                <p><b>Para Renovar o acesso ARES:</b></p>
                <p>Clique no botão <a><i class="glyphicon icon-key"></i> Renovar</a>, no canto direto da coluna Ações;</p>
                <p>Digitar o numero do processo;</p>
                <p>Selecionar a validade da acesso ;</p>
                <p>Clicar em renovar;</p>
            </div>
            <div class="col-md-6">
                <img src="/images/wiki/renovacao_processo.jpg"  height="290" width="290" class="img-thumbnail">
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div id="loginbox" style="margin-top: 10px;"
          class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
          <div class="panel panel-danger">
            <div class="panel-heading"><i class="glyphicon glyphicon-warning-sign"></i> &nbsp;ATENÇÃO:</div>
            <div style="padding-top: 10px" class="panel-body">
              Caso o usuário solicitar acesso por um periodo curto selecinar informar e digitar a quantidade de dias que ficará disponivel;<p>
                  <img src="/images/wiki/renovacao_validade.jpg" height="290" width="290" class="img-thumbnail">
            </div>
          </div>
        </div>
    </div>

    <div class="container-fluid">
            <div id="loginbox" style="margin-top: 10px;"
              class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
              <div class="panel panel-danger">
                <div class="panel-heading"><i class="glyphicon glyphicon-warning-sign"></i> &nbsp;ATENÇÃO:</div>
                <div style="padding-top: 10px" class="panel-body">
                  Caso o usuário solicitar para poder gerar o processo no SIAT clicar em Renovação provisória;
                </div>
              </div>
            </div>
    </div>
</div>

<div style="background: #97BBCC; padding: 30px 0; min-height: 300px">
    <div class="container content">
        <div class="row">
            <div class="col-md-6" style="color: #fff; font-size: 14px;">
                <p><b>Para Gerar novo Kit ARES:</b></p>
                <p>Clique no botão <a><i class="glyphicon glyphicon-compressed"></i> Gerar</a>, no canto direto da coluna Ações;</p>
            </div>
            <div class="col-md-6">
                <img src="/images/wiki/Acesso_Expirado.jpg" class="img-thumbnail">
            </div>
        </div>
    </div>
</div>

<div style="background: #fff; padding: 30px 0; min-height: 300px">
    <div class="container content">
        <div class="row">
            <div class="col-md-6">
                <img src="/images/wiki/Acesso_Expirado.jpg" class="img-thumbnail">
            </div>
            <div class="col-md-6" style="color: #555; font-size: 14px;">
                <p><b>Para Pesquisar:</b></p>
                <p>Para pesquisar basta usar o checkbox no canto superior central;</p>
                <p>A pesquisa é automática podendo filtrar pelo nome, login, chave key e validade.</p>
            </div>
        </div>
    </div>
</div>

<div style="background: #97BBCC; padding: 30px 0; min-height: 300px">
    <div class="container content">
        <div class="row">
            <div class="col-md-6" style="color: #fff; font-size: 14px;">
                <p><b>Para Habilitar/Desabilitar acesso:</b></p>
                <p>Clique no botão <a><i class="glyphicon glyphicon-ok text-success" ></i> Habilitar</a>/<a><i class="glyphicon glyphicon-remove text-danger" ></i> Desabilitar</a>, no canto direto da coluna Ações;</p>
                <ul>Onde cada um tem acesso as seguintes funções:
                    <li>Intranet - Site Sefa : acesso aos sites internos da sefa; como siatweb, mantis...</li>
                    <li>Intranet - (Zimbra 80) : Acesso a internet atraves da rede da sefa.</li>
                    <li>Intranet - (Zimbra 443) : Acesso a internet atraves da rede da sefa, com a criptografia https.</li>
                    <li>Intranet - Intranet : Acesso aos TS's para acessar o SIAT.</li>
                </ul>
                Caso não aparece basta digitar o nome do usuário no campo de pesquisar IMAGEM
            </div>
            <div class="col-md-6">
                <img src="/images/wiki/Acesso_Expirado.jpg" class="img-thumbnail">
            </div>
        </div>
    </div>
</div>

</div>