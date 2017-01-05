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
	$scope.msg = '';
	$scope.tipos = [{name: 'Novo ARES', value: 1},{name: 'Renovação de Chave', value: 2},{name: 'Reenvio de Kit', value: 3}];
	
	$http.get('/app/configs/all').
		success(function(data){
			$scope.conf = data;
		}).
		error(function(){ feedback('danger','Falha ao carregar configurações') });
	
	$scope.setMensagem = function (tipo) {
		if (tipo == 1)
			$scope.msg = $scope.conf.email_mensagem_novo;
		if (tipo == 2)
			$scope.msg = $scope.conf.email_mensagem_renovacao;
		if (tipo == 3)
			$scope.msg = $scope.conf.email_mensagem_kit;
	}
	
	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	};
	
});