var app = angular.module('Admins', ['nya.bootstrap.select',
                                      'angularUtils.directives.dirPagination',
                                      'ui.bootstrap',
                                      'ngAnimate',
                                      'ngSanitize',
                                      'ngToast',
                                      'ui.mask']);

app.config(['ngToastProvider', function(ngToastProvider) {
      ngToastProvider.configure({
    	maxNumber: 4,
    	timeout: 4000,
        animation: 'fade',
        newestOnTop: false
      });
}]);

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

/************************************
 * Definicoes para Módulo de usuários
 ************************************/
app.controller('AdminsCtrl', ['$scope','$http','$rootScope','$uibModal','$interval', '$log', 'ngToast',
function($scope, $http, $rootScope, $uibModal, $interval, $log, ngToast){
	
	$interval.cancel($rootScope.realtime);
	
	$scope.admins;
	$scope.currentPage = 1;
	$scope.pageSize = 15;

	$scope.carregaAdmins = function() {
		$http.get('/app/admins/').
			success(function(data, status, headers, config) {
				$scope.admins = data;
			}).
			error(function(data, status, headers, config){
				feedback('warning','Problema no módulo de admins..');
			});
	}

	$scope.carregaAdmins();
	
	$scope.ativaAdmin = function(person) {
		var idx = $scope.admins.indexOf(person);
		var status;
		
		(person.ativo == 1) ? status = 0 : status = 1;
		
		$http.get('/app/admins/ativa_admin/'+person.id+'/'+status).
			success(function(){
				$scope.admins[idx].ativo = status;
			});
	} // ativaUser
	
	$scope.confirmDel = function (person) {
		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'removeModal.html',
			controller: 'RmAdmCtrl',
			resolve: {
				person: function(){ return person }
			}
		});
		
		modalInstance.result.then(function(){
			$scope.admins.splice($scope.admins.indexOf(person), 1);
		});
	} // confirmDel
	
	$scope.editAdmin = function (person) {
		
		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'editModal.html',
			controller: 'EdtAdmCtrl',
			resolve: {
				person: function(){ return person }
			}
		});
		
//		modalInstance.result.then(function(){
//			alert('Chave do usuário '+ person.login +' atualizada!');
//		});
		
	} // editUser
	
	$scope.createAdm = function () {

		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'createModal.html',
			controller: 'CrtAdmCtrl'
		});
		
		modalInstance.result.then(function(newadmin){
			if (newadmin)
				$scope.admins.push(newadmin);
		});
	} // createUser
	
	$scope.getPerfil = function (cod) {
		if (cod == 1)
			return 'administrador';
		else if (cod == 2)
			return 'operador';
		else if (cod == 3)
			return 'consulta';
	}
	
	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	}
	
}]);

/**************************************
 * Definicoes para remoção de usuários
 **************************************/
app.controller('RmAdmCtrl', function($scope, $uibModalInstance, person, $http, ngToast){
	$scope.person = person;
	
	$scope.ok = function () {
		$http.get('/app/admins/remove_admin/'+person.id).
			success(function(){
				$uibModalInstance.close(person);
				feedback('success','Admin <b>'+person.login+'</b> removido');
			}).
			error(function(){
				feedback('danger','Falha na remoção do admin <b>'+ person.login +'<b>');
			});
	};
	
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	};
	
	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	}
	
});

/**************************************
 * Definicoes para criação de usuários
 **************************************/
app.controller('CrtAdmCtrl', function($scope, $uibModalInstance, $http, $rootScope, ngToast){
	$scope.novoadmin;
	$scope.adAdmins;
	
	$scope.perfils = [{"perfil": 1, "descricao": 'Administrador'},
	                  {"perfil": 2, "descricao": 'Operador'},
	                  {"perfil": 3, "descricao": 'Consulta'}];	
	
	$http.get('/app/usuarios/ad_membros/ares_admins').
		success(function(response){
			$scope.adAdmins = JSON.parse(JSON.stringify(response));
		}).
		error(function(data, status, headers, config){
			alert('Error in CrtAdmCtrl from AngularJS');
		});
	
	$scope.ok = function () {
		$scope.novoadmin.perfil = $scope.select.perfil;
		var data = {'object':angular.toJson($scope.novoadmin)};
		
		$http({
			method: 'POST',
			url: '/app/admins/add_admin/',
			data: $.param(data),
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			}
		}).
		success(function(data){
			if (data == null) {
				$uibModalInstance.dismiss('error');
				feedback('danger','Falha ao cadastrar admin no banco de dados!');
			}
			
			$uibModalInstance.close(data);
			feedback('success','Admin <b>'+$scope.novoadmin.login+'</b> cadastrado');
		}).
		error(function(){
			$uibModalInstance.close();
			feedback('danger','Falha ao cadastrar admin no banco de dados!');
		});
		
	} // OK
	
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	} // Cancel
	
	$scope.checkExist = function(admin) {
		if ($scope.adAdmins.indexOf(admin) == -1)
			$scope.nok = true;
		
		if (!admin || admin.login == null)
			return;
		
		$scope.nok = false;
		
		$http.get('/app/pessoas/'+ admin.login).
			success(function(data){
				if (data == null)
					return;
				
				$scope.novoadmin.nome = data.nome;
				$scope.novoadmin.email = data.email;
				
				$scope.fromPessoa = true;
			}).
			error(function(){ return });
	} // CheckExist
	
	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	}
});

/*************************************
 * Definicoes para Edição de usuários
 *************************************/
app.controller('EdtAdmCtrl', function($scope, $log, $http, $rootScope, person, $uibModalInstance, ngToast){
	$scope.person = person;
	$scope.person2mod;
	$scope.perfils = [{"perfil": 1, "descricao": 'Administrador'},
	                  {"perfil": 2, "descricao": 'Operador'},
	                  {"perfil": 3, "descricao": 'Consulta'}];
	
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	}
	
	$scope.ok = function() {
		$scope.person2mod.perfil = $scope.select.perfil;
		var data = {'object': angular.toJson($scope.person2mod) };
		
		$http({
			method: 'POST',
			url: '/app/admins/edita_admin/'+person.id,
			data: $.param(data),
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			}
		}).
		success(function(){
			$scope.person.nome = $scope.person2mod.nome;
			$scope.person.perfil = $scope.person2mod.perfil;
			$uibModalInstance.close();
			feedback('success','Admin <b>'+person.login+'</b> editado');
		}).
		error(function(){
			feedback('danger','Falha ao editar <b>'+person.login+'</b>');
		});
	} // ok
	
	$scope.getPerfil = function(perfil) {
		for (var i=0; i < $scope.perfils.length; i++)
			if ($scope.perfils[i].perfil == perfil)
				return $scope.perfils[i];
		return false;
	}

	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	}
	
});