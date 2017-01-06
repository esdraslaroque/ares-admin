<div ng-controller="ConfigsCtrl">
	<ul class="navtitulo">
		<li><a><i class="glyphicon glyphicon-cog"></i> &nbsp;<b>Configurações do Gerenciador</b></a></li>
	</ul>
	<hr>
	<uib-tabset active="active">
		<uib-tab index="0">
			<uib-tab-heading><i class="glyphicon icon-address-book"></i> &nbsp;Active Directory</uib-tab-heading>
			<br />
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<p class="control-label col-sm-1">Usuário:</p>
					<div class="col-sm-3">
						<input type="text" ng-model="conf.ad_user" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<p class="control-label col-sm-1">Senha:</p>
					<div class="col-sm-3">
						<input type="password" ng-model="conf.ad_pass" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<p class="control-label col-sm-1">Servidor:</p>
					<div class="col-sm-3">
						<input type="text" ng-model="conf.ad_server" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<p class="control-label col-sm-1">Porta:</p>
					<div class="col-sm-3">
						<input type="text" ng-model="conf.ad_port" class="form-control">
						<br />
						<button type="submit" class="btn btn-primary" ng-click="save('ad')">Salvar</button>
					</div>
				</div>
			</form>
		</uib-tab>

		<uib-tab index="1">
			<uib-tab-heading><i class="glyphicon glyphicon-envelope"></i> &nbsp;Alerta de E-mail</uib-tab-heading>
			<br />
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<p class="control-label col-sm-1">SMTP:</p>
					<div class="col-sm-3">
						<input type="text" ng-model="conf.email_smtp"  class="form-control">
					</div>
				</div>
				<div class="form-group">
					<p class="control-label col-sm-1">Remetente:</p>
					<div class="col-sm-3">
						<input type="text" ng-model="conf.email_remetente"  class="form-control">
					</div>
				</div>
				<div class="form-group">
					<p class="control-label col-sm-1">Tipo:</p>
					<div class="col-sm-3">
						<select class="form-control" ng-model="tipoSel" ng-change="setMensagem(tipoSel)" data-ng-options="tipo.value as tipo.name for tipo in tipos">
							<option value="">Selecione ..</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<p class="control-label col-sm-1">Mensagem:</p>
					<div class="col-sm-5">
						<textarea rows="10" ng-model="msg.text" class="form-control"></textarea>
						<br />
						<p><b>Macros disponíveis para mensagem:</b><br />
						
						<code ng-non-bindable>{{login}}</code> -> Login de rede do usuário<br />
						<code ng-non-bindable>{{validade}}</code> -> Data de validade da chave (dd/mm/YYY)
						</p>
						<br />
						<button type="submit"  class="btn btn-primary" ng-click="save('email')">Salvar</button>
					</div>
				</div>
			</form>
		</uib-tab>

		<uib-tab index="2">
			<uib-tab-heading><i class="glyphicon icon-database"></i> &nbsp;Banco de Dados</uib-tab-heading>
			<br />
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<p class="control-label col-sm-1">Banco:</p>
					<div class="col-sm-3">
						<input type="text" class="form-control" value="/var/db/ares/ares.sql" disabled="disabled">
						<span class="small">*SQLite 3.6</span>
						<br /><br />
						<button type="submit"  class="btn btn-primary">Backup</button>
					</div>
				</div>
			</form>
		</uib-tab>

	</uib-tabset>

</div>