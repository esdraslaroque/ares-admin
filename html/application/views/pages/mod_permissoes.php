<div ng-controller="RegrasCtrl">
	<ul class="nav nav-pills">
		<li ng-if="admin.perfil == 1"><a ng-click="novo()"><i class="glyphicon glyphicon-plus-sign"></i> Novo</a></li>
		<li><a ng-click="atualizar()"><i class="glyphicon glyphicon-repeat"></i> Atualizar</a></li>
		<div class="col-sm-5 text-primary" ng-esc="search=''">
		    <div class="left-inner-addon">
		        <i class="glyphicon glyphicon-search"></i>
		        <input ng-model="search" class="form-control" placeholder="Pesquisar .." />
		    </div>
		</div>
	</ul>
	<hr>
	<uib-tabset active="active">

		<!-- Regras -->
		<uib-tab index="0" select="setTab(0)">
			<uib-tab-heading><i class="glyphicon glyphicon-tag"></i> &nbsp;Regras</uib-tab-heading>
				<br />
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>Id</th>
							<th>Autor</th>
							<th>Descrição</th>
							<th>Destino</th>
							<th>Protocolo</th>
							<th>Serviço</th>
							<th>Grupo</th>
							<th>Ações</th>
						</tr>
					</thead>
					<tr dir-paginate="regra in regras | filter:search | itemsPerPage:pageSize" current-page="currentPage" class="text-primary">
						<td>{{regra.id}}</td>
						<td>{{regra.login}}</td>
						<td>{{regra.descricao}}</td>
						<td>{{regra.destino}}</td>
						<td>{{regra.proto}}</td>
						<td>{{regra.servico}}</td>
						<td ng-class="{'text-italic text-muted': regra.grupo == 'Default'}">{{regra.grupo=='Default' && 'Sem grupo' || regra.grupo }}</td>
						<td>
							<a class="icon" uib-tooltip="{{regra.acao==1 && 'Aceitar' || 'Rejeitar'}}" ng-click="toggleAcao(regra)"><i ng-class="{'glyphicon glyphicon-thumbs-up text-success': regra.acao==1, 'glyphicon glyphicon-thumbs-down text-danger': regra.acao==0}"></i></a>
							<a class="icon" uib-tooltip="Editar Regra" ng-click="editRule(regra)"><i class="glyphicon glyphicon-pencil"></i></a>
							<a class="icon" uib-tooltip="Excluir Regra" ng-click="confirmDel(regra)"><i class="glyphicon glyphicon-trash"></i></a>
						</td>
					</tr>
				</table>
				<dir-pagination-controls max-size="5" direction-links="true" boundary-links="true"></dir-pagination-controls>
		</uib-tab>
		
		<!-- Grupo de Regras -->
		<uib-tab index="1" select="setTab(1)">
			<uib-tab-heading>
				<i class="glyphicon glyphicon-tags"></i> &nbsp;Grupo de Regras
			</uib-tab-heading>
			<br />
				<uib-accordion close-others="true" ng-init="open=false">
					<uib-accordion-group is-open="open" ng-repeat="grupo in grupos | filter:search" ng-if="grupo.id != 1">
						<uib-accordion-heading>
							<b>{{grupo.descricao}}</b> <i class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': open, 'glyphicon-chevron-right': !open}"></i>
						</uib-accordion-heading>
						<div class="container-fluid typeahead-demo">
							<div class="row">
								<div class="col-sm-3">
								<a ng-click="confirmDelGrupo(grupo)" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i>&nbsp;Excluir grupo</a>
								<!-- 
									<div class="input-group">
										<input placeholder="Adicione uma regra.." type="text" ng-model="ngModelOptionsSelected" ng-model-options="modelOptions" uib-typeahead="grupo for grupo in regrasOptions | filter:$viewValue | limitTo:8" class="form-control">
										<span class="input-group-addon" ng-click="test()"><i class="glyphicon glyphicon-plus"></i></span>
									</div>
								-->
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-sm-3">
									<label>Regras:</label><br>
									<a class="label type-hint" ng-repeat="regra in regras | filter:{grupo_regra_id: grupo.id}">{{regra.descricao}}<br ng-if="$index != 0 && $index % 5 == 0" /></a>
								</div>
					      	</div>
						</div>
				    </uib-accordion-group>
				</uib-accordion>
		</uib-tab>
	</uib-tabset>


<!-- ---------------------------------
 Modal de Remoção de Regra :: inicio 
-------------------------------------- -->
	<script type="text/ng-template" id="removeModal.html" ng-if="admin.perfil == 1">
        <div class="modal-header">
	        <button type="button" class="close" ng-click="cancel()">&times;</button>
    	    <h4 class="modal-title" ng-if="tabSelected==0">Remover Regra</h4>
			<h4 class="modal-title" ng-if="tabSelected==1">Remover Grupo</h4>
			<p class="text-italic" ng-if="tabSelected==0">{{regra.descricao}}</p>
			<p class="text-italic" ng-if="tabSelected==1">{{grupo.descricao}}</p>
        </div>
        <div class="modal-body">
		<p class="text-danger" ng-if="tabSelected==0">
			{{admin.nome}},<br><br>Você tem certeza que deseja remover a regra <b>{{regra.descricao}}</b> ??<br />
			Esta ação implicará na remoção de permissões de <b>todos</b> os usuários que a utilizam!
		</p>
		<p class="text-danger" ng-if="tabSelected==1">
			{{admin.nome}},<br><br>Você tem certeza que deseja remover o grupo <b>{{grupo.descricao}}</b> ??<br />
			Esta ação implicará na remoção de permissões de <b>todos</b> os usuários que a utilizam!
		</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="button" ng-click="ok()">Confirmar</button>
            <button class="btn btn-danger" type="button" ng-click="cancel()">Cancelar</button>
        </div>
	</script>
<!-- Modal de Remoção de Regra :: fim -->

<!-- ---------------------------------
 Modal de Criação de Regra :: inicio 
-------------------------------------- -->
	<script type="text/ng-template" id="createModal.html" ng-if="admin.perfil == 1">
        <div class="modal-header">
	        <button type="button" class="close" ng-click="cancel()">&times;</button>
    	    <h4 class="modal-title" ng-if="tabSelected==0">Criar Regra</h4>
			<h4 class="modal-title" ng-if="tabSelected==1">Criar Grupo</h4>
			<p class="text-italic" ng-init="newrule.admin_id = admin.id" ng-if="tabSelected==0">Autor: {{admin.username}}</p>
        </div>
        <div class="modal-body">

		<form class="form-horizontal" role="form" ng-show="tabSelected==0">
			<div class="form-group">
				<label class="control-label col-sm-2" for="login">Descrição:</label>
				<div class="col-sm-8">          
					<input type="text" class="form-control" ng-model="newrule.descricao" placeholder="Nome para a regra.." required>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="nome">Destino:</label>
				<div class="col-sm-8">          
					<input type="text" class="form-control" ng-model="newrule.destino" placeholder="Destino da conexão..">
				</div>
			</div>			
			<div class="form-group">
				<label class="control-label col-sm-2" for="nome">Protocolo:</label>
				<div class="col-sm-8">          
					<label class="radio-inline"><input type="radio" ng-model="newrule.proto" value="tcp"> TCP</label>
					<label class="radio-inline"><input type="radio" ng-model="newrule.proto" value="udp"> UDP</label>
				</div>
			</div>			
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Serviço:</label>
				<div class="col-sm-8">          
					<input type="text" class="form-control" ng-model="newrule.servico" placeholder="Porta do serviço..">
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-2" for="grupo">Grupo:</label>
				<div class="col-sm-10">
					<ol class="nya-bs-select col-sm-6" ng-model="groupSelected" data-live-search="true"
					 	data-selected-text-format="count>3" data-size="8" title="Selecione um grupo">
					  <li nya-bs-option="option in grupos">
					    <a>
					    	{{option.id == 1 && 'Sem grupo' || option.descricao}}
					    	<span class="glyphicon glyphicon-ok check-mark"></span>
					    </a>
					  </li>
					</ol>
 				</div>
			</div>
		</form>

		<form class="form-horizontal" role="form" ng-show="tabSelected==1">
			<div class="form-group">
				<label class="control-label col-sm-2" for="login">Descrição:</label>
				<div class="col-sm-8">          
					<input type="text" class="form-control" ng-model="newgroup" placeholder="Nome para o grupo.." required>
				</div>
			</div>
		</form>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="button" ng-click="ok()">Criar</button>
            <button class="btn btn-danger" type="button" ng-click="cancel()">Cancelar</button>
        </div>
	</script>
<!-- Modal de Criação de Regra :: fim -->

<script type="text/ng-template" id="customPopupTemplate.html">
  <div class="custom-popup-wrapper"
     ng-style="{top: position().top+'px', left: position().left+'px'}"
     style="display: block;"
     ng-show="isOpen() && !moveInProgress"
     aria-hidden="{{!isOpen()}}">
    <p class="message">select location from drop down.</p>

    <ul class="dropdown-menu" role="listbox">
        <li ng-repeat="match in matches track by $index" ng-class="{active: isActive($index) }"
            ng-mouseenter="selectActive($index)" ng-click="selectMatch($index)" role="option" id="{{::match.id}}">
            <div uib-typeahead-match index="$index" match="match" query="query" template-url="templateUrl"></div>
        </li>
    </ul>
  </div>
</script>

<!-- ---------------------------------
 Modal de Edição de Regra :: inicio 
-------------------------------------- -->
	<script type="text/ng-template" id="editModal.html" ng-if="admin.perfil == 1">
        <div class="modal-header">
	        <button type="button" class="close" ng-click="cancel()">&times;</button>
    	    <h4 class="modal-title">Editar Regra</h4>
			<p class="text-italic" ng-init="regra.admin_id = admin.id">Por: {{admin.username}}</p>
        </div>
        <div class="modal-body">

		<form class="form-horizontal" role="form">
			<div class="form-group">
				<label class="control-label col-sm-2" for="login">Descrição:</label>
				<div class="col-sm-8">          
					<input type="text" class="form-control" ng-model="regra.descricao" placeholder="Nome para a regra.." required>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="nome">Destino:</label>
				<div class="col-sm-8">          
					<input type="text" class="form-control" ng-model="regra.destino" placeholder="Destino da conexão..">
				</div>
			</div>			
			<div class="form-group">
				<label class="control-label col-sm-2" for="nome">Protocolo:</label>
				<div class="col-sm-8">          
					<label class="radio-inline"><input type="radio" ng-model="regra.proto" value="tcp"> TCP</label>
					<label class="radio-inline"><input type="radio" ng-model="regra.proto" value="udp"> UDP</label>
				</div>
			</div>			
			<div class="form-group">
				<label class="control-label col-sm-2" for="email">Serviço:</label>
				<div class="col-sm-8">          
					<input type="text" class="form-control" ng-model="regra.servico" placeholder="Porta do serviço..">
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-2" for="grupo">Grupo:</label>
				<div class="col-sm-10">
					<ol class="nya-bs-select col-sm-6" ng-model="groupSelected" data-live-search="true"
					 	data-selected-text-format="count>3" data-size="8" title="Selecione um grupo">
					  <li nya-bs-option="option in grupos">
					    <a>
							{{option.id == 1 && 'Sem grupo' || option.descricao}}
					    	<span class="glyphicon glyphicon-ok check-mark"></span>
					    </a>
					  </li>
					</ol>
 				</div>
			</div>
		</form>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="button" ng-click="ok()">Salvar</button>
            <button class="btn btn-danger" type="button" ng-click="cancel()">Cancelar</button>
        </div>
	</script>
<!-- Modal de Edição de Regra :: fim -->
</div>