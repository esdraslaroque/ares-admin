<div ng-controller="DashboardCtrl">

	<div class="row" ng-controller="smallBoxCtrl">

		<div class="col-lg-3 col-xs-6">
			<div class="small-box bg-online">
				<div class="inner">
					<h3>{{usuarios.acesso_online == null && '0' || usuarios.acesso_online}}</h3>
					<p>Total de Acessos Online</p>
				</div>
				<div class="icon">
					<i class="ion ion-stats-bars"></i>
				</div>
				<a ng-click="accessUser()" class="small-box-footer">Mais infomações</a>
			</div>
		</div>

		<div class="col-lg-3 col-xs-6">
			<div class="small-box bg-expiration">
				<div class="inner">
					<h3>{{usuarios.qtd_15day}}</h3>
					<p>Total usuarios com 15 dias para expirar</p>
				</div>
				<div class="icon">
					<i class="ion ion-person"></i>
				</div>
				<a ng-click="exp15User()" class="small-box-footer">Mais infomações</a>
			</div>
		</div>

		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-totalmes">
				<div class="inner">
					<h3>{{usuarios.qtd_user_conect_mes == null && '0' || usuarios.qtd_user_conect_mes}}</h3>
					<p ng-controller="datCtrl">Acessos no Mês de {{ today | date : "MMMM" }}</p>
				</div>
				<div class="icon">
					<i class="ion ion-calendar"></i>
				</div>
				<a class="small-box-footer">&nbsp;</a>
			</div>
		</div>
	</div>

	<div class="row">

		<div class="col-lg-8">

			<div class="box" ng-controller="TabsGraphCtrl">
				<uib-tabset active="active" style="margin-top: 4px">
					<uib-tab index="0">
<!-- 						<uib-tab-heading><i class="ion ion-ios-pulse-strong"></i> &nbsp;Tráfego de Dados</uib-tab-heading> -->
						<uib-tab-heading><i class="glyphicon icon-stats-dots"></i> &nbsp;Tráfego de Rede</uib-tab-heading>
						
						<div class="box-body">
							<div class="row">
								<div class="col-md-12">
									<p class="text-center">
										<strong>Tráfego de Rede (kb/s)</strong>
									</p>
									<div class="chart">
										<canvas id="line" style="height: 300px;" class="chart chart-line" chart-data="linesData" chart-labels="linesLabels" chart-legend="true" chart-series="linesSeries"></canvas>
									</div>
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- ./box-body -->

						<div class="box-footer">
							<div class="row">
								<div class="col-lg-6">
									<div class="description-block border-right">
										<span class="description-percentage text-primary text-bold"><i class="fa fa-caret-up"></i> {{ usuarios.bytes_out*100 / (usuarios.bytes_out+usuarios.bytes_in) | number:0 }}%</span>
										<h5 class="description-header"><i class="glyphicon glyphicon-open"></i> {{ formatSizeUnits(usuarios.bytes_out) }}</h5>
										<span class="description-text">total enviado</span>
									</div><!-- /.description-block -->
								</div><!-- /.col -->
								<div class="col-lg-6">
									<div class="description-block">
										<span class="description-percentage text-expirados text-bold"><i class="fa fa-caret-left"></i> {{ usuarios.bytes_in*100 / (usuarios.bytes_out+usuarios.bytes_in) | number:0 }}%</span>
										<h5 class="description-header"><i class="glyphicon glyphicon-save"></i> {{ formatSizeUnits(usuarios.bytes_in) }}</h5>
										<span class="description-text">TOTAL recebido</span>
									</div><!-- /.description-block -->
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div>
					</uib-tab>
					<uib-tab index="1">
						<uib-tab-heading><i class="glyphicon glyphicon-stats"></i> &nbsp;Estatísticas de Conexão</uib-tab-heading>

						<div ng-controller="EstatisticasConexao">
							<p class="text-center">
								<br> <strong>Conexões por {{periodo}}</strong>
							</p>

							<canvas id="bar" class="chart chart-bar" style="height: 300px" chart-data="barData" chart-labels="barLabels" chart-legend="true" chart-options="barOptions" chart-click="onClick"></canvas>
							<div class="input-group" ng-show="nivel > 1">
								<span class="input-group-btn text-center">
									<button class="btn btn-default bg-totalmes btn-sm" type="button" ng-click="loadGraph(1)"><span class="glyphicon glyphicon-chevron-left"></span> voltar</button>
								</span>
							</div>
						</div>

					</uib-tab>
					<uib-tab index="2">
						<uib-tab-heading><i class="icon-key"></i> &nbsp;Estatísticas de Renovação</uib-tab-heading>
						<br />

						<div ng-controller="EstatisticasARES">
							<p class="text-center">
								<br> <strong>Renovações por {{periodo}}</strong>
							</p>

							<canvas id="bar" class="chart chart-bar" style="height: 300px" chart-colours="barADColors" chart-data="barData" chart-labels="barLabels" chart-legend="true" chart-options="barOptions" chart-click="onClick"></canvas>

							<div class="input-group" ng-show="nivel > 1">
								<span class="input-group-btn text-center">
									<button class="btn btn-default bg-totalmes btn-sm" type="button" ng-click="loadGraph(1)"><span class="glyphicon glyphicon-chevron-left"></span> voltar</button>
								</span>
							</div>

						</div>

					</uib-tab>
				</uib-tabset>
			</div><!-- /.box -->
		</div><!-- /.col -->

		<!-- AREA CHART -->
		<div class="col-lg-4">


			<div class="box box-default" ng-controller="TabsGraphCtrl">
				<uib-tabset active="active" style="margin-top: 4px">
					<uib-tab index="0">
						<uib-tab-heading><i class="glyphicon glyphicon-user"></i> &nbsp;Situação ARES</uib-tab-heading>


						<div class="box-body">
							<div class="chart-responsive">
								<canvas id="doughnut" class="chart chart-doughnut" chart-data="doughnutData" chart-labels="doughnutLabels"></canvas>
							</div>
						</div><!-- /.box-body -->
						<div class="box-footer no-padding" ng-controller="SituacaoARES">
							<ul class="nav nav-pills nav-stacked">
								<li><a ng-click="validUser()"><i class="ion ion-android-radio-button-on text-validos"></i> Válidos <span class="pull-right text-validos text-bold">{{usuarios.ativo*100/(usuarios.ativo+usuarios.expirado+usuarios.inativo) | number :2}}%</span></a></li>
								<li><a ng-click="expiradoUser()"><i class="ion ion-android-radio-button-on text-expirados"></i> Expirados <span class="pull-right text-expirados text-bold">{{usuarios.expirado*100/(usuarios.ativo+usuarios.expirado+usuarios.inativo) | number :2}}%</span></a></li>
								<li><a ng-click="inativoUser()"><i class="ion ion-android-radio-button-on text-red"></i> Inativos <span class="pull-right text-red text-bold">{{usuarios.inativo*100/(usuarios.ativo+usuarios.expirado+usuarios.inativo) | number :2}}%</span></a></li>
							</ul>
						</div><!-- /.footer -->


					</uib-tab>
					<uib-tab index="1">
						<uib-tab-heading><i class="glyphicon icon-address-book"></i> &nbsp;Situação AD</uib-tab-heading>
						<div ng-controller="RoscaGraphCtrl" >
							<div class="box-body" >
								<div class="chart-responsive">
									<canvas id="doughnut" class="chart chart-doughnut" chart-colours="chartADColors" chart-data="doughnutData" chart-labels="doughnutLabels"></canvas>
								</div>
							</div><!-- /.box-body -->
							<div class="box-footer no-padding" ng-controller="SituacaoAD">
								<ul class="nav nav-pills nav-stacked">
									<li><a ng-click="notexistAD()"><i class="ion ion-android-radio-button-on text-red"></i> Não existe no AD <span class="pull-right text-red text-bold">{{totais.cod1*100/(totais.cod1+totais.cod2+totais.cod3) | number :2}}%</span></a></li>
									<li><a ng-click="blockedAD()"><i class="ion ion-android-radio-button-on text-green"></i> Bloqueado no AD <span class="pull-right text-green text-bold">{{totais.cod2*100/(totais.cod1+totais.cod2+totais.cod3) | number :2}}%</span></a></li>
									<li><a ng-click="outgroupAD()"><i class="ion ion-android-radio-button-on text-blue"></i> Fora do grupo ARES <span class="pull-right text-blue text-bold">{{totais.cod3*100/(totais.cod1+totais.cod2+totais.cod3) | number :2}}%</span></a></li>
								</ul>
							</div><!-- /.footer -->
						</div>
					</uib-tab>
					</uib-tabset>
			</div><!-- /.box -->

		</div><!-- /.col-lg -->

	</div>

</div>
