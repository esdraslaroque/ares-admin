<div ng-controller="AdminsCtrl">
	<ul class="nav nav-pills">
		<li><a ng-click="createAdm()"><i class="glyphicon glyphicon-plus-sign"></i> Novo</a></li>
		<li><a ng-click="carregaAdmins()"><i class="glyphicon glyphicon-repeat"></i> Atualizar</a></li>
		<div class="col-sm-5 text-primary" ng-esc="search=''">
		    <div class="left-inner-addon">
		        <i class="glyphicon glyphicon-search"></i>
		        <input ng-model="search" class="form-control" placeholder="Pesquisar .."/>
		    </div>
		</div>
	</ul>
	<hr>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>Id</th>
				<th>Flags</th>
				<th>Perfil</th>
				<th>Login</th>
				<th>Nome</th>
				<th>Ações</th>
			</tr>
		</thead>
		<tr dir-paginate="person in admins | filter:search | itemsPerPage:pageSize" current-page="currentPage" ng-class="{'text-primary': person.ativo == 1, 'text-muted text-italic': person.ativo == 0}">
			<td>{{person.id}}</td>
			<td>
				<a ng-if="checkRen(person.validade)" class="text-warning text-warn icon" uib-tooltip="{{'Chave expirando em '+ checkRen(person.validade)}}" ng-click="checkRen2(person.validade)"><i class="glyphicon glyphicon-flag"></i></a>
				<a ng-if="checkExpired(person.validade)" class="text-danger icon" uib-tooltip="Chave expirada!"><i class="glyphicon glyphicon-flag"></i></a> 
				<a ng-if="person.isuser == 1" uib-tooltip="Usuário ARES" class="icon"><i class="glyphicon glyphicon-user"></i></a>
			</td>
			<td><a class="label" ng-class="{'perfil-cons':person.perfil==3, 'perfil-oper':person.perfil==2, 'perfil-admin':person.perfil==1}">{{getPerfil(person.perfil)}}</a></td>
			<td>{{person.login}}</td>
			<td>{{person.nome}}</td>
			<td class="text-primary">
				<a class="icon" uib-tooltip="{{person.ativo == 1 && 'Desabilitar Admin' || 'Habilitar Admin'}}" ng-click="ativaAdmin(person)"><i ng-class="{'glyphicon glyphicon-ok text-success': person.ativo == 0, 'glyphicon glyphicon-remove text-danger': person.ativo == 1}"></i></a>
				<a class="icon" uib-tooltip="Editar Admin"><i class="glyphicon glyphicon-pencil" ng-click="editAdmin(person)"></i></a>
				<a class="icon" uib-tooltip="Excluir Admin"><i class="glyphicon glyphicon-trash" ng-click="confirmDel(person)"></i></a>
			</td>
		</tr>
	</table>
	<dir-pagination-controls max-size="5" direction-links="true" boundary-links="true"></dir-pagination-controls>
<!-- </div> -->

<!-- ---------------------------------
 Modal de Edição do Admin :: inicio 
-------------------------------------- -->
	<script type="text/ng-template" id="editModal.html">
      <div class="modal-header">
        <button type="button" class="close" ng-click="cancel()">&times;</button>
        <h4 class="modal-title">Editar Admin</h4>
		<p class="text-italic">{{person.nome}}</p>
      </div>
      <div class="modal-body">
      
		<form class="form-horizontal" role="form">
			<div class="form-group">
				<label class="control-label col-sm-2">Nome:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" ng-init="person2mod.nome = person.nome" ng-model="person2mod.nome">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2">Perfil:</label>
				<div class="col-sm-8">
					<ol id="sigleSelection" class="nya-bs-select col-sm-12" ng-init="select = getPerfil(person.perfil)" ng-model="select" title="Selecione o perfil">
						<li nya-bs-option="option in perfils">
							<a>
								{{option.descricao}}
								<span class="glyphicon glyphicon-ok check-mark"></span>
							</a>
						</li>
					</ol>
				</div>
			</div>
		</form>      
		
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary" ng-click="ok()">Salvar</button>
        <button type="button" class="btn btn-default" ng-click="cancel()">Fechar</button>
      </div>
	</script>
<!-- Modal de Edição/Informação do usuario :: fim -->


<!-- ---------------------------------
 Modal de Criação do Admin :: inicio 
-------------------------------------- -->
	<script type="text/ng-template" id="createModal.html">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" ng-click="cancel()">&times;</button>
			<h4 class="modal-title">Criação de Admin</h4>
			<p class="text-italic">Gerenciador ARES</p>
		</div>
		<div class="modal-body">
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<label class="control-label col-sm-2">Login:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" placeholder="Login de rede.." ng-bind="adadmin.login" ng-model="novoadmin" uib-typeahead="adadmin as adadmin.login for adadmin in adAdmins | filter:{login:$viewValue} | limitTo:8" ng-blur="checkExist(novoadmin)" required>
						<span class="text-danger" ng-show="nok">
							<i class="glyphicon glyphicon-alert"></i> Usuário não configurado no AD
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">Nome:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" placeholder="Nome completo.." ng-model="novoadmin.nome" required>
						<span class="text-primary" ng-show="fromPessoa && !nok">
							<i class="glyphicon glyphicon-info-sign"></i> Nome carregado da base ARES
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">E-mail:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" placeholder="login.rede@sefa.pa.gov.br" ng-model="novoadmin.email" required>
						<span class="text-primary" ng-show="fromPessoa && !nok">
							<i class="glyphicon glyphicon-info-sign"></i> Nome carregado da base ARES
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">Perfil:</label>
					<div class="col-sm-8">
						<ol id="sigleSelection" class="nya-bs-select col-sm-12" ng-model="select" title="Selecione o perfil">
							<li nya-bs-option="option in perfils">
								<a>
									{{option.descricao}}
									<span class="glyphicon glyphicon-ok check-mark"></span>
								</a>
							</li>
						</ol>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-primary" ng-click="ok()" ng-if="novoadmin.login">Criar</button>
        	<button type="button" class="btn btn-danger" ng-click="cancel()">Cancelar</button>
		</div>
	</script>
<!-- Modal de Criação do usuario :: fim -->

<!-- ---------------------------------
 Modal de Remoção do Admin :: inicio 
-------------------------------------- -->
	<script type="text/ng-template" id="removeModal.html">
        <div class="modal-header">
	        <button type="button" class="close" ng-click="cancel()">&times;</button>
    	    <h4 class="modal-title">Remover Admin</h4>
			<p class="text-italic">{{person.nome}}</p>
        </div>
        <div class="modal-body">
		<p class="text-danger">{{admin.nome}},<br><br>Você tem certeza que deseja remover o administrador <b>{{person.login}}</b> ??</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="button" ng-click="ok()">Confirmar</button>
            <button class="btn btn-danger" type="button" ng-click="cancel()">Cancelar</button>
        </div>
	</script>
<!-- Modal de Remoção do usuario :: fim -->

</div>