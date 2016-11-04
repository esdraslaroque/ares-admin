var app = angular.module('Registros', ['ui.bootstrap','angularUtils.directives.dirPagination']);

app.directive('ngEsc', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress keyup", function (event) {
            if(event.which === 27) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEsc);
                });

                event.preventDefault();
            }
        });
    };
});

app.controller('RegistrosCtrl', function($scope, $http, $interval, $rootScope){
	
	$scope.currentPage = 1;
	$scope.pageSize = 25;
	
	$scope.rows = [];
	
	$scope.carregaLinhas = function () {
		$http.get('/app/registros/log').
			success(function(data){
				$scope.rows = data;
				$scope.rows.splice(-1,1);
				$scope.loaded = true;
			}).
			error(function(){ console.log('Error ao carregar linhas de log') });
	};
	
	$scope.carregaLinhas();
	
	$rootScope.realtime = $interval(function(){
		$scope.carregaLinhas();
	}, 5000);
	
	$scope.logType = function (str) {
		if (str.indexOf('PAP peer authentication failed') > -1 || 
			str.indexOf('Failure setting user credentials') > -1 ||
			str.indexOf('User account has expired') > -1 ||
			str.indexOf('ERROR') > -1)
			return 'text-danger';

		if (str.indexOf('WARN') > -1)
			return 'text-warn';
		
		if (str.indexOf('Autenticação da chave ARES realizada com sucesso') > -1)
			return 'text-success';

		return 'text-primary';
	};
	
	$scope.filterPid = function (pid) {
		$scope.search = pid;
	};
	
});