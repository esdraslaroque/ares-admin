/*
 * AngularJS
 */
var mods = ['Dashboard','Usuarios','Regras','Admins','Registros','Relatorios','Configs','Ajuda'];
//var mods = ['Usuarios','Regras','Admins','Registros','Configs','Ajuda'];

var app = angular.module('adminApp', mods);

app.controller('InitCtrl', function($scope, $http, $rootScope, $window, $interval){
	$scope.admin = [];
	$rootScope.pill = 1;

    $scope.setTab = function (tabId) {
    		$rootScope.pill = tabId;
    };

    $scope.isSet = function (tabId) {
        return $rootScope.pill === tabId;
    };

	$http.get('/app/admins/info/').
		success(function(data, status, headers, config) {
			$scope.admin = data;
			$rootScope.admin = data;
		}).
		error(function(data, status, headers, config){
			alert('Error in InitCtrl for admin infos from AngularJS');
		});

	$rootScope.loadModule = function(mod){
//		console.log('Carregando módulo de '+ mod +'..');
		$interval.cancel($rootScope.realtime);
		$scope.template = '/app/mod/'+ mod +'/';
	}

	$scope.logoff = function() {
		$interval.cancel($rootScope.realtime);
		$window.location.href = '/app/autenticador/admin/logoff';
	}

	$scope.loadModule('dashboard');
	$scope.setTab(0);
	
	$scope.normalizePerfil = function(cod) {
		if (cod == 1)
			return 'Administrador';
		
		if (cod == 2)
			return 'Operador';

		if (cod == 3)
			return 'Consulta';
		
		return false;
	};

});
