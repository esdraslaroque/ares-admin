<div class="container-fluid" ng-app="adminApp">
	<toast></toast>
	<div class="row content" ng-controller="InitCtrl">
		<div class="col-sm-2 sidenav">
			<h4 ng-if="admin.username" id="user" class="text-center">
					<i class="glyphicon icon-user-tie"></i> {{admin.username}}
			</h4>
			<p ng-if="admin.username" class="text-center small">( {{normalizePerfil(admin.perfil)}} )</p>
			<img ng-show="!admin.username" class="text-center" src="/images/loading.gif" width="35px" alt="Loading.." />
			<hr>

			<ul class="nav nav-pills nav-stacked">
				<li ng-class="{'active': isSet(0)}"><a ng-click="setTab(0); loadModule('dashboard')"><i class="glyphicon glyphicon-th"></i> Dashboard</a></li>
				<li ng-class="{'active': isSet(1)}"><a ng-click="setTab(1); loadModule('usuarios')"><i class="glyphicon glyphicon-user"></i> Usuários</a></li>
				<li ng-class="{'active': isSet(2)}" ng-show="admin.perfil == 1"><a ng-click="setTab(2); loadModule('permissoes')"><i class="glyphicon glyphicon-sort"></i> Permissões</a></li>
				<li ng-class="{'active': isSet(3)}" ng-show="admin.perfil == 1"><a ng-click="setTab(3); loadModule('admins')"><i class="glyphicon glyphicon-briefcase"></i> Admins</a></li>
				<li ng-class="{'active': isSet(4)}"><a ng-click="setTab(4); loadModule('registros')"><i class="glyphicon glyphicon-align-justify"></i> Registros</a></li>
				<li ng-class="{'active': isSet(5)}"><a ng-click="setTab(5); loadModule('relatorios')"><i class="glyphicon glyphicon-file"></i> Relatórios</a></li>
				<li ng-class="{'active': isSet(6)}" ng-show="admin.perfil == 1"><a ng-click="setTab(6); loadModule('configuracoes')"><i class="glyphicon glyphicon-cog"></i> Configurações</a></li>
				<li ng-class="{'active': isSet(7)}"><a ng-click="setTab(7); loadModule('ajuda')"><i class="glyphicon glyphicon-question-sign"></i> Ajuda</a></li>
			</ul>

			<hr>

			<div class="input-group">
				<span class="input-group-btn text-center">
					<button class="btn btn-default btn-danger btn-xs" type="button" ng-click="logoff()">
						Logoff <span class="glyphicon glyphicon-off"></span>
					</button>
				</span>
			</div>

		</div>
		<!-- Conteiner para carregamento dos módulos -->
		<div class="col-sm-10" id="modulo" ng-include="template"></div>
	</div>
</div>
<script src="/js/angular.min.js"></script>
<script src="/js/angular-locate_pt-br.js"></script>
<script src="/js/nya-bs-select.min.js"></script>
<script src="/js/dirPagination.js"></script>
<script src="/js/ui-bootstrap.min.js"></script>
<script src="/js/Chart.js"></script>
<script src="/js/angular-chart.min.js"></script>
<script src="/js/moment.min.js"></script>
<script src="/js/angular-sanitize.min.js"></script>
<script src="/js/angular-animate.min.js"></script>
<script src="/js/ngToast.min.js"></script>
<script src="/js/mask.min.js"></script>
<script src="/js/count-to.js"></script>
<script src="/js/ng-device-detector.min.js"></script>
<script src="/js/re-tree.min.js"></script>

<!-- JsvaScript dos módulos (Precisa vir após os plugins) -->
<script src="/js/mod_dashboard.js"></script>
<script src="/js/mod_usuarios.js"></script>
<script src="/js/mod_permissoes.js"></script>
<script src="/js/mod_admins.js"></script>
<script src="/js/mod_registros.js"></script>
<script src="/js/mod_relatorios.js"></script>
<script src="/js/mod_configs.js"></script>
<script src="/js/mod_ajuda.js"></script>
