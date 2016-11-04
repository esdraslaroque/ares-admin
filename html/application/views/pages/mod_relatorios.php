<div ng-controller="RelatorioCtrl" ng-init="searchRel=''">
	<form class="form-inline" role="form">
		<div class="form-group col-sm-3 text-primary"> <!-- ng-esc="searchRel=''">  -->
			<div class="left-inner-addon">
				<i class="glyphicon glyphicon-search"></i>
				<input ng-model="searchRel" class="form-control" placeholder="Pesquisar .."/>
			</div>
		</div>
		<div class="form-group">
			<h4 style="font-size: 17px;" class="text-primary"> &nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;&nbsp;</h4>
		</div>
		<div class="form-group">
			<label class="control-label">De:</label>
			<input type="text" class="form-control" ng-model="de" type="text" ui-mask="99/99/9999" ng-click="de = null">
		</div>
		<div class="form-group">
			<label class="control-label">Até:</label>
			<input type="text" class="form-control" ng-model="ate" type="text" ui-mask="99/99/9999" ng-click="ate = null">
		</div>
		<button type="submit" class="btn btn-primary" ng-click="setPeriodo()">Gerar</button>
	</form>
	<hr>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Login</th>
				<th>Qtd. Acessos</th>
				<th>Duração</th>
				<th>Tráfego</th>
				<th>Último Acesso</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
<!-- 		<tr dir-paginate-start="acesso in acessos | itemsPerPage:pageSize" current-page="currentPage" class="text-primary tr-hover"> -->
		<tr ng-repeat-start="acesso in acessos | filter:{login: searchRel}" ng-class="{'text-primary text-bold tr-hover noanim': !acesso.isopen, 'text-primary tr-hover noanim': acesso.isopen}">
			<td>{{acesso.login}}</td>
			<td>{{acesso.qtd_acessos}}</td>
			<td>{{ formatDuration(acesso.duracao) }}</td>
			<td>{{ formatSizeUnits(acesso.trafego) }}</td>
			<td>{{acesso.ult_acesso}}</td>
			<td>
				<a href="#" uib-tooltip="Mais informações" ng-click="carregaRelByLogin(acesso);"><i ng-class="{'glyphicon glyphicon-chevron-up text-danger': !acesso.isopen, 'glyphicon glyphicon-chevron-down': acesso.isopen}"></i></a>
			</td>
		</tr>
		<tr uib-collapse="acesso.isopen" ng-repeat-end style="background-color: #f5f5f5">
<!-- 		<tr uib-collapse="isCollapsed" dir-paginate-end="" > -->
			<td colspan="6">
				<table class="table table-striped table-condensed table-hover">
					<thead style="background-color: #f5f5f5">
						<tr>
							<th>Início</th>
							<th>Fim</th>
							<th>Duração</th>
							<th>Tráfego</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tr dir-paginate="meuAcesso in acesso.detalhes | itemsPerPage:pageSize" current-page="currentPage" pagination-id="acesso.pageId" class="table-bordered">
						<td>{{meuAcesso.inicio}}</td>
						<td>{{meuAcesso.fim}}</td>
						<td>{{ formatDuration(meuAcesso.duracao) }}</td>
						<td>{{ formatSizeUnits(meuAcesso.trafego) }}</td>
						<td>
							<a href="#"><i class="glyphicon glyphicon-info-sign" popover-placement="left" uib-popover-template="dynamicPopover.templateUrl" popover-title="Detalhes da Sessão" popover-trigger="mouseenter"></i></a>
						</td>
					</tr>
				</table>
				<dir-pagination-controls max-size="5" pagination-id="acesso.pageId" direction-links="true" boundary-links="true"></dir-pagination-controls>
        	</td>
		</tr>
		<tfoot ng-if="tAcessos != null && searchRel == ''">
			<tr class="text-bold">
				<td>Totais:</td>
				<td>{{tAcessos}}</td>
				<td>{{ formatDuration(tDuracao) }}</td>
				<td>{{ formatSizeUnits(tTrafego) }}</td> 
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>
<!-- 	<dir-pagination-controls max-size="10" direction-links="true" boundary-links="true"></dir-pagination-controls> -->

	<script type="text/ng-template" id="detalhesPopover.html">
		<div>
			<p class="small"><b>Interface:</b> {{meuAcesso.interface}}</p>
			<p class="small"><b>IP:</b> {{meuAcesso.ip_cliente}}</p>
			<p class="small"><b>PID:</b> {{meuAcesso.pid}}</p>
			<p class="small" ng-if="meuAcesso.autenticado==1"><b>Atenticado:</b> Sim </p>
			<p class="small" ng-if="meuAcesso.autenticado==0"><b>Atenticado:</b> Não </p>
		</div>
	</script>

</div>