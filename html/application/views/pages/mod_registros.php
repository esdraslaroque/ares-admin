<div ng-controller="RegistrosCtrl">
	<ul class="nav nav-pills">
		<li><a ng-click="carregaLinhas()"><i class="glyphicon glyphicon-repeat"></i> Atualizar</a></li>
		<div class="col-sm-5 text-primary" ng-esc="search=''">
		    <div class="left-inner-addon">
		        <i class="glyphicon glyphicon-search"></i>
		        <input ng-model="search" class="form-control" placeholder="Pesquisar .."/>
		    </div>
		</div>
		<li ng-show="!loaded" ><img src="/images/loading.gif" width="35px" alt="Loading.." /></li>
	</ul>
	<hr>
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th>Data</th>
				<th>Serviço</th>
				<th>PID</th>
				<th>Log</th>
			</tr>
		</thead>
		<tr dir-paginate="row in rows | filter:search | itemsPerPage:pageSize" current-page="currentPage" class="small" ng-class="logType(row.text)">
			<td>{{row.date}}</td>
			<td><a class="label" ng-class="{'perfil-cons':row.service=='pppd', 'perfil-oper':row.service=='ares'}">{{row.service}}</a></td>
			<td><a href="" ng-click="filterPid(row.pid)">{{row.pid == '' ? 'NULL' : row.pid}}</a></td>
			<td>{{row.text | limitTo: 120}}{{row.text.length > 120 ? ' [..]' : ''}}</td>
		</tr>
	</table>
	<dir-pagination-controls max-size="10" direction-links="true" boundary-links="true"></dir-pagination-controls>
</div>