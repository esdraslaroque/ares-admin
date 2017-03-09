<div ng-controller="UsuariosCtrl">
	<ul class="nav nav-pills">
		<li ng-if="admin.perfil == 1"><a ng-click="createUser()"><i class="glyphicon glyphicon-plus-sign"></i> Novo</a></li>
		<li ng-if="!searchType"><a ng-click="atualiza()"><i class="glyphicon glyphicon-repeat"></i> Atualizar</a></li>
		<li ng-if="searchType"><a ng-click="atualiza()"><i class="glyphicon glyphicon-filter"></i> Sem Filtro</a></li>
		<div class="col-sm-5 text-primary" ng-esc="search=''">
		    <div class="left-inner-addon">
		        <i class="glyphicon glyphicon-search"></i>
		        <input ng-model="search" class="form-control" placeholder="Pesquisar .."/>
		    </div>
		</div>
		<li ng-show="!loaded" ><img src="/images/loading.gif" width="35px" alt="Loading.." /></li>
	</ul>
	<hr>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>Id</th>
				<th>Flags</th>
				<th>Login</th>
				<th>Nome</th>
				<th>Chave</th>
				<th>Validade</th>
				<th>Ações</th>
			</tr>
		</thead>
		<tr dir-paginate="person in user | filter:search | filter:filterDashboard | itemsPerPage:pageSize" current-page="currentPage" ng-class="{'text-primary': person.ativo == 1, 'text-muted text-italic': person.ativo == 0}">
			<td>{{person.id}}</td>
			<td>
				<a ng-if="checkRen(person.validade)" class="text-warning text-warn icon" uib-tooltip="{{'Chave expirando em '+ checkRen(person.validade)}}" ng-click="checkRen2(person.validade)"><i class="glyphicon glyphicon-flag"></i></a>
				<a ng-if="checkExpired(person.validade)" class="text-danger icon" uib-tooltip="Chave expirada!"><i class="glyphicon glyphicon-flag"></i></a>
				<a ng-if="person.isadmin == 1" uib-tooltip="Administrador" class="icon"><i class="glyphicon glyphicon-briefcase"></i></a>
				<a ng-if="person.isconect == 1" class="text-success icon"><i class="glyphicon glyphicon-flash" popover-placement="right" uib-popover-template="dynamicPopover.templateUrl" popover-title="Conexão" popover-trigger="mouseenter" ng-mouseover="setUser(person)"></i></a>
				<a ng-if="checkAD(person)" class="text-danger text-danger icon" uib-tooltip="{{checkAD(person)}}"><i class="glyphicon glyphicon-ban-circle"></i></a>
			</td>
			<td>{{person.login}}</td>
			<td>{{person.nome}}</td>
			<td>
				<strike ng-if="checkExpired(person.validade)" class="text-danger">{{person.id_key}}</strike>
				<p ng-if="!checkExpired(person.validade)">{{person.id_key}}</p>
			</td>
			<td>{{person.validade | date:"dd/MM/yyyy"}}</td>
			<td class="text-primary">
				<a ng-if="admin.perfil == 2 || admin.perfil == 3" class="icon" uib-tooltip="Mais informações"><i class="glyphicon glyphicon-info-sign" ng-click="editUser(person)"></i></a>
				<a ng-if="admin.perfil == 1" class="icon" uib-tooltip="{{person.ativo == 1 && 'Desabilitar Usuário' || 'Habilitar Usuário'}}" ng-click="ativaUser(person)"><i ng-class="{'glyphicon glyphicon-ok text-success': person.ativo == 0, 'glyphicon glyphicon-remove text-danger': person.ativo == 1}"></i></a>
				<a ng-if="admin.perfil == 1 || admin.perfil == 2" class="icon" uib-tooltip="Renovar Chave"><i class="glyphicon icon-key" ng-click="renewKey(person)"></i></a>
				<!-- <a ng-if="admin.perfil == 1 || admin.perfil == 2" class="icon" uib-tooltip="Gerar Kit ARES"><i class="glyphicon glyphicon-compressed" ng-click="genKit(person)"></i></a>  -->
				<a class="icon" uib-tooltip="Gerar Kit ARES"><i class="glyphicon glyphicon-compressed" ng-click="genKit(person)"></i></a>				
				<a ng-if="admin.perfil == 1" class="icon" uib-tooltip="Editar Usuário"><i class="glyphicon glyphicon-pencil" ng-click="editUser(person)"></i></a>
				<a ng-if="admin.perfil == 1" class="icon" uib-tooltip="Excluir Usuário"><i class="glyphicon glyphicon-trash" ng-click="confirmDel(person)"></i></a>
			</td>
		</tr>
	</table>
	<dir-pagination-controls max-size="15" direction-links="true" boundary-links="true"></dir-pagination-controls>
	
	<script type="text/ng-template" id="userConnectedPopover.html">
		<div ng-controller="ConnUserCtrl">
			<p class="small"><b>Interface:</b> {{conexao.interface}}</p>
			<p class="small"><b>IP:</b> {{conexao.ip_cliente}}</p>
			<p class="small"><b>PID:</b> {{conexao.pid}}</p>
			<p class="small"><b>Duração:</b> {{conexao.inicio | moment: 'fromNow'}}</p>
		</div>
	</script>

<!-- ---------------------------------
 Modal de Edição/Informação do usuario :: inicio
-------------------------------------- -->
	<script type="text/ng-template" id="editModal.html">
   		<div class="modal-header">
       		<button type="button" class="close" ng-click="cancel()">&times;</button>
       		<h4 class="modal-title">{{person.nome}}</h4>
			<p class="text-italic">
				Criado em: {{person.criado | date:"dd/MM/yyyy"}}
				<span ng-class="{'label label-success': person.ativo == 1, 'label label-danger sm': person.ativo == 0}">{{person.ativo == 1 && 'Habilitado' || 'Desabilitado'}}</span>
			</p>
   		</div>
   		<div class="modal-body">
			<form class="form-horizontal" role="form">
				<div class="form-group form-inline">
					<label class="control-label col-sm-2">Login:</label>
					<div class="col-sm-3">
						<p class="form-control-static">{{person.login}}</p>
					</div>
					<label class="control-label col-sm-2">Chave:</label>
					<div class="col-sm-3">
						<p class="form-control-static">{{person.id_key}}</p>
					</div>
				</div>
				<div class="form-group form-inline">
					<label class="control-label col-sm-2">Expedida:</label>
					<div class="col-sm-3">
						<p class="form-control-static">{{person.expedida | date:"dd/MM/yyyy"}}</p>
					</div>
					<label class="control-label col-sm-2">Validade:</label>
					<div class="col-sm-4">
						<p class="form-control-static">{{person.validade | date:"dd/MM/yyyy"}}</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">Processo:</label>
					<div class="col-sm-8">
						<p class="form-control-static">{{person.processo | numberFixedLen:16}}</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">Home:</label>
					<div class="col-sm-8">
						<p class="form-control-static">{{person.home}}</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="admin">Admin:</label>
					<div class="col-sm-8">
						<p class="form-control-static">{{person.admin}} <span class="text-italic text-muted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Último alterador)</span></p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">E-mail:</label>
					<div class="col-sm-8">
						<p class="form-control-static" ng-show="admin.perfil==2 || admin.perfil==3">{{person.email}}</p>
						<input type="text" class="form-control" ng-init="person2mod.email = person.email" ng-model="person2mod.email" ng-show="admin.perfil==1">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">Nome:</label>
					<div class="col-sm-8">
						<p class="form-control-static" ng-show="admin.perfil==2 || admin.perfil==3">{{person.nome}}</p>
						<input type="text" class="form-control" ng-init="person2mod.nome = person.nome" ng-model="person2mod.nome" ng-show="admin.perfil==1">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">Permissões:</label>
					<div class="col-sm-8" ng-show="admin.perfil==1">
						<ol id="multipleSelection" class="nya-bs-select col-sm-12" ng-model="selected" data-live-search="true" data-selected-text-format="count>3" data-size="8" title="Selecione as permissões" multiple>
							<li nya-bs-option="option in regras group by option.grupo">
								<span class="dropdown-header" ng-show="$group!='Default'">{{$group}}</span>
								<span class="dropdown-header" ng-show="$group=='Default'">Sem Grupo</span>
					    		<a>
									{{option.descricao}}
					    			<span class="glyphicon glyphicon-ok check-mark"></span>
					    		</a>
					  		</li>
						</ol>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<a class="label type-hint" ng-repeat="regra in selected">{{regra.descricao}} <br ng-if="$index != 0 && $index % 2 == 0" /></a>
						</div>
					</div>
				</div>
			</form>
	     </div>
		 <div class="modal-footer">
      		<button type="submit" class="btn btn-primary" ng-click="ok()" ng-show="admin.perfil==1">Salvar</button>
        	<button type="button" class="btn btn-default" ng-click="cancel()">Fechar</button>
		 </div>
	</script>
<!-- Modal de Edição/Informação do usuario :: fim -->

<!-- ---------------------------------
 Modal de Criação do usuario :: inicio
-------------------------------------- -->
	<script type="text/ng-template" id="createModal.html" ng-init="periodo=30; persoPeriodo=null; selected=null">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" ng-click="cancel()">&times;</button>
			<h4 class="modal-title">Criação de Usuário</h4>
			<p class="text-italic">Acesso ARES</p>
		</div>
		<div class="modal-body">
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<label class="control-label col-sm-2">Login:</label>
					<div class="col-sm-8">
						<input type="text" ng-bind="usuario.login" ng-model="usuario" placeholder="Login de rede.." uib-typeahead="aduser as aduser.login for aduser in adUsers | filter:{login:$viewValue} | limitTo:12" ng-blur="checkExist(usuario)" class="form-control">
						<span class="text-danger" ng-show="nok">
							<i class="glyphicon glyphicon-alert"></i> Usuário não configurado no AD
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">Nome:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" placeholder="Nome completo.." ng-model="usuario.nome" required>
						<span class="text-primary" ng-show="fromPessoa && !nok">
							<i class="glyphicon glyphicon-info-sign"></i> Nome carregado da base ARES
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">E-mail:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" placeholder="login.rede@sefa.pa.gov.br" ng-model="usuario.email" required>
						<span class="text-primary" ng-show="fromPessoa && !nok">
							<i class="glyphicon glyphicon-info-sign"></i> E-mail carregado da base ARES
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2">Processo:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" ui-mask="999999999999999-9" ng-model="usuario.processo" required>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="validade">Validade:</label>
					<div class="col-sm-8" ng-hide="periodo == 'Informar'">
						<label class="radio-inline"><input type="radio" ng-model="periodo" value="30"> 30 dias</label><br/>
						<label class="radio-inline"><input type="radio" ng-model="periodo" value="90"> 90 dias</label><br/>
						<label class="radio-inline"><input type="radio" ng-model="periodo" value="180"> 180 dias</label><br/>
						<label class="radio-inline"><input type="radio" ng-model="periodo" value="Informar"> Informar</label><br/>
					</div>
					<div class="col-sm-8" ng-show="periodo == 'Informar'">
						<input type="number" min="1" max="180" class="form-control" placeholder="validade (dias)" ng-model="persoPeriodo">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="permissoes">Permissões:</label>
					<div class="col-sm-8">
						<ol id="multipleSelection" class="nya-bs-select col-sm-12" ng-model="selected" data-live-search="true" data-selected-text-format="count>3" data-size="8" title="Selecione as permissões" multiple>
							<li nya-bs-option="option in regras group by option.grupo">
								<span class="dropdown-header" ng-show="$group!='Default'">{{$group}}</span>
								<span class="dropdown-header" ng-show="$group=='Default'">Sem Grupo</span>
					    		<a>
					    			{{option.descricao}}
					    			<span class="glyphicon glyphicon-ok check-mark"></span>
					    		</a>
					  		</li>
						</ol>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-2"></div>
					<div class="col-sm-8">
						<a class="label type-hint" ng-repeat="regra in selected">{{regra.descricao}} <br ng-if="$index != 0 && $index % 2 == 0" /></a>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-primary" ng-click="ok()" ng-if="usuario.login && usuario.processo">Criar</button>
        	<button type="button" class="btn btn-danger" ng-click="cancel()">Cancelar</button>
		</div>
	</script>
<!-- Modal de Criação do usuario :: fim -->

<!-- ---------------------------------
 Modal de Remoção do usuario :: inicio
-------------------------------------- -->
	<script type="text/ng-template" id="removeModal.html">
        <div class="modal-header">
	        <button type="button" class="close" ng-click="cancel()">&times;</button>
    	    <h4 class="modal-title">Remover Usuário</h4>
			<p class="text-italic">{{person.nome}}</p>
        </div>
        <div class="modal-body">
		<p class="text-danger">{{admin.nome}},<br><br>Você tem certeza que deseja remover o acesso ARES do usuário <b>{{person.login}}</b> ??</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="button" ng-click="ok()" ng-if="admin.perfil == 1 || admin.perfil == 2">Confirmar</button>
            <button class="btn btn-danger" type="button" ng-click="cancel()">Cancelar</button>
        </div>
	</script>
<!-- Modal de Remoção do usuario :: fim -->

<!-- ---------------------------------
 Modal de Renovação de chave :: inicio
-------------------------------------- -->
	<script type="text/ng-template" id="renewModal.html" ng-init="periodo=30; persoPeriodo=null">
      <div class="modal-header">
        <button type="button" class="close" ng-click="periodo=30; persoPeriodo=null; cancel()">&times;</button>
        <h4 class="modal-title">Renovar chave</h4>
		<p class="text-italic">{{person.nome}}</p>
      </div>
      <div class="modal-body">
		<form class="form-horizontal" role="form">
			<div class="form-group" ng-hide="provisorio || person.ativo == 0">
				<label class="control-label col-sm-2" >Processo:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" ng-model="processo" ui-mask="999999999999999-9" required>
				</div>
			</div>
			<div class="form-group" ng-show="processo">
				<label class="control-label col-sm-2" ng-hide="person.ativo == 0">Validade:</label>
				<div class="col-sm-4" ng-hide="periodo == 'Informar' || person.ativo == 0">
					<label class="radio-inline"><input type="radio" ng-model="periodo" value="30"> 30 dias</label><br/>
					<label class="radio-inline"><input type="radio" ng-model="periodo" value="90"> 90 dias</label><br/>
					<label class="radio-inline"><input type="radio" ng-model="periodo" value="180"> 180 dias</label><br/>
					<label class="radio-inline"><input type="radio" ng-model="periodo" value="Informar"> Informar</label><br/>
				</div>
				<div class="col-sm-4" ng-show="periodo == 'Informar'">
					<input type="number" min="1" max="180" class="form-control" placeholder="validade (dias)" ng-model="persoPeriodo">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" >&nbsp;</label>
				<div class="col-sm-8" ng-hide="person.ativo == 0">
					<label class="text-italic small lead" ng-hide="processo"><input type="checkbox" ng-model="provisorio"> Renovação provisória</label>
				</div>
			</div>
		</form>
 		<h5 ng-if="person.ativo == 0" class="text-danger">Primeiramente ative o usuário <b>{{person.login}}</b> para renovar sua chave.</h5>
      </div>
      <div class="modal-footer" ng-show="person.ativo == 1">
      	<button type="submit" class="btn btn-primary" ng-click="ok()" ng-hide="(periodo == 'Informar' && persoPeriodo == null) || (processo == null && !provisorio)">Renovar</button>
        <button type="button" class="btn btn-danger" ng-click="periodo=30; persoPeriodo=null; cancel()">Cancelar</button>
      </div>
      <div class="modal-footer" ng-show="person.ativo == 0">
        <button type="button" class="btn btn-default" ng-click="cancel()">Fechar</button>
      </div>
	</script>
<!-- Modal de Renovação de chave :: fim -->

</div>