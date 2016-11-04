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
	$scope.msg;
	
	$http.get('/app/configs/all').
		success(function(data){
			$scope.conf = data;
		}).
		error(function(){ feedback('danger','Falha ao carregar configurações') });
	
	$scope.setMsg = function (tipo) {
		alert('lalalallal');
		if (! tipo)
			$scope.msg = false;
		
		if (tipo == 1)
			$scope.msg = $scope.conf.email_mensagem_novo;
		else if (tipo == 2)
			$scope.msg = $scope.conf.email_mensagem_renovacao;
		else if (tipo == 3)
			return $scope.conf.email_mensagem_kit;
		else
			$scope.msg = false;
	};
	
	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	};
	
});