var app = angular.module('Configs', ['ngToast']);

app.config(['ngToastProvider', function(ngToastProvider) {
    ngToastProvider.configure({
  	maxNumber: 4,
  	timeout: 4000,
      animation: 'fade',
      newestOnTop: false
    });
}]);

app.controller('ConfigsCtrl', function($scope, $http, ngToast){
	
	$scope.conf;
	$scope.msg = { text: ''} ;
	$scope.tipos = [{name: 'Novo ARES', value: 1},{name: 'Renovação de Chave', value: 2},{name: 'Reenvio de Kit', value: 3}];
	var tipoMsg = 0;
	
	$scope.carregaConfs = function() {
		$http.get('/app/configs/all').
			success(function(data){
				$scope.conf = data;
			}).
			error(function(){ feedback('danger','Falha ao carregar configurações') });
	}
	
	$scope.carregaConfs();
	
	$scope.setMensagem = function (tipo) {
		tipoMsg = tipo;
		
		if (tipo == 1)
			$scope.msg.text = $scope.conf.email_mensagem_novo;
		if (tipo == 2)
			$scope.msg.text = $scope.conf.email_mensagem_renovacao;
		if (tipo == 3)
			$scope.msg.text = $scope.conf.email_mensagem_kit;
	}
	
	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	};
	
	$scope.save = function (conf) {
		var dataConf = {};
		
		if (conf == 'ad') {
			dataConf.ad_user = $scope.conf.ad_user;
			dataConf.ad_pass = $scope.conf.ad_pass;
			dataConf.ad_server = $scope.conf.ad_server;
			dataConf.ad_port = $scope.conf.ad_port;
		}
		
		if (conf == 'email') {
			dataConf.email_smtp = $scope.conf.email_smtp;
			dataConf.email_remetente = $scope.conf.email_remetente;
			dataConf.tipo = tipoMsg;
			dataConf.msg = null;
			
			if (tipoMsg == 1)
				dataConf.msg = $scope.msg.text;
			if (tipoMsg == 2)
				dataConf.msg = $scope.msg.text;
			if (tipoMsg == 3)
				dataConf.msg = $scope.msg.text;
		}
		
		var data = {'object':angular.toJson(dataConf)};

		$http({
			method: 'POST',
			url: '/app/configs/set/'+conf,
			data: $.param(data),
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			}
		}).
		success(function(data){
			if (data.cod == 0)
				feedback('success', data.msg);
			else
				feedback('danger', data.msg);

			$scope.carregaConfs();
		}).
		error(function(){
			$scope.carregaConfs();
			feedback('danger', 'Falha na requisição ao servidor');
		});
		
		
	}
	
});