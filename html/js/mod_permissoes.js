/***************************************
 * Definicoes para Módulo de permissões
 ***************************************/
var app = angular.module('Regras', ['nya.bootstrap.select',
                                    'angularUtils.directives.dirPagination',
                                    'ui.bootstrap',
                                    'ngAnimate',
                                    'ngSanitize',
                                    'ngToast']);

app.config(['ngToastProvider', function(ngToastProvider) {
    ngToastProvider.configure({
  	maxNumber: 4,
  	timeout: 3000,
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

app.controller('RegrasCtrl', function($scope, $http, $rootScope, $uibModal, ngToast){

	$scope.regras = [];
	$scope.grupos = [];
	$scope.regrasOptions = [];
	$scope.tabSelected = 0;

	$scope.currentPage = 1;
	$scope.pageSize = 15;
	
	$scope.setTab = function (index) {
		$scope.tabSelected = index;
		$scope.search = '';
	}
	
	$scope.carregaRegras = function() {
		$http.get('/app/regras/').
			success(function(data) {
				$scope.regras = data;

				for (var i=0; i < data.length; i++) {
					$scope.regrasOptions.push(data[i].descricao);
				}
			}).
			error(function(data, status, headers, config){
				feedback('warning','Falha ao carregar regras');
				//alert('Error in RegrasCtrl for regras from AngularJS');
			});
	}
	
	$scope.carregaGrupos = function() {
		$http.get('/app/regras/grupo_regras/').
			success(function(data) {
				$scope.grupos = data;
			}).
			error(function(data){
				feedback('warning','Falha ao carregar grupos');
				//alert('Error in RegrasCtrl for grupos from AngularJS');
			});
	}

	$scope.carregaRegras();
	$scope.carregaGrupos();
	
	$scope.toggleAcao = function(rule) {
		var idx = $scope.regras.indexOf(rule);
		var regra = $scope.regras[idx];
		var acao = null;
		
		if (rule.acao == 1) {
			acao = 0;
			$http.get('/app/regras/regra_acao/'+regra.id+'/'+acao).
				success(function(){
					$scope.regras[idx].acao = acao;
				});
		} else if (rule.acao == 0) {
			acao = 1;
			$http.get('/app/regras/regra_acao/'+regra.id+'/'+acao).
				success(function(){
					$scope.regras[idx].acao = acao;
				});
		} else
			return;
	} // toggleAcao
	
	$scope.confirmDel = function (regra) {
		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'removeModal.html',
			controller: 'RmRuleCtrl',
			resolve: {
				regra: function(){ return regra },
				tab: function(){ return $scope.tabSelected }
			}
		});
		
		modalInstance.result.then(function(){
			feedback('success','Regra <b>'+regra.descricao+'</b> removida');
			$scope.regras.splice($scope.regras.indexOf(regra), 1);
		});
	} // confirmDel
	
	$scope.confirmDelGrupo = function (grupo) {
		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'removeModal.html',
			controller: 'RmGroupCtrl',
			resolve: {
				grupo: function(){ return grupo },
				tab: function(){ return $scope.tabSelected }
			}
		});
		
		modalInstance.result.then(function(){
			feedback('success','Grupo <b>'+grupo.descricao+'</b> removido');
			$scope.grupos.splice($scope.grupos.indexOf(grupo), 1);
			$scope.carregaRegras();
		});
	} // confirmDel
	
	$scope.novo = function () {
		if ($scope.tabSelected == 0)
			$scope.createRule();
		else if ($scope.tabSelected == 1)
			$scope.createGroup();
		else
			return;
	}
	
	$scope.atualizar = function () {
		if ($scope.tabSelected == 0)
			$scope.carregaRegras();
		else if ($scope.tabSelected == 1)
			$scope.carregaGrupos();
		else
			return;
	}
	
	$scope.createRule = function () {
		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'createModal.html',
			controller: 'CrtRuleCtrl',
			resolve: {
				tab: function(){ return $scope.tabSelected },
				grupos: function(){ return $scope.grupos }
			}
		});
		
		modalInstance.result.then(function(regra){
			$scope.newrule = regra;
			$scope.regras.push($scope.newrule);
			feedback('success','Regra <b>'+regra.descricao+'</b> adicionada');
		});
	} // createRule

	$scope.editRule = function (regra) {
		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'editModal.html',
			controller: 'EdtRuleCtrl',
			resolve: {
				regra: function(){ return regra },
				grupos: function(){ return $scope.grupos }
			}
		});
		
//		var idx = $scope.regras.indexOf(regra);
		
		modalInstance.result.then(function(data){
			if (data.cod == 1) {
				$scope.carregaRegras();
				feedback('danger', data.msg);
			} else {
				$scope.carregaRegras();
				feedback('success', 'Regra <b>'+ data.regra_id +'</b> editada!');
			}
		});
	} // editRule
	
	$scope.createGroup = function () {
		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'createModal.html',
			controller: 'CrtGroupCtrl',
			resolve: {
				tab: function(){ return $scope.tabSelected }
			}
		});
		
		modalInstance.result.then(function(){
			$scope.carregaGrupos();
		});
	} // createRule

	$scope.modelOptions = {
		debounce: {
		  default: 500,
		  blur: 250
		},
		getterSetter: true
	};

	var _selected;
	$scope.ngModelOptionsSelected = function(value) {
	    if (arguments.length) {
	      _selected = value;
	    } else {
	      return _selected;
	    }
	};

	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	};

});

/**************************************
 * Definicoes para remoção de regras
 **************************************/
app.controller('RmRuleCtrl', function($scope, $uibModalInstance, regra, tab, $http){
	$scope.regra = regra;
	$scope.tabSelected = tab;
	
	$scope.ok = function () {
		$http.get('/app/regras/remove_regra/'+regra.id).
			success(function(){
				$uibModalInstance.close(regra);
			});
	};
	
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	};
	
});

/**************************************
 * Definicoes para remoção de grupos
 **************************************/
app.controller('RmGroupCtrl', function($scope, $uibModalInstance, grupo, tab, $http){
	$scope.grupo = grupo;
	$scope.tabSelected = tab;
	
	$scope.ok = function () {
		$http.get('/app/regras/remove_grupo/'+grupo.id).
			success(function(){
				$uibModalInstance.close(grupo);
			});
	};
	
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	};
	
});

/**************************************
 * Definicoes para criação de regras
 **************************************/
app.controller('CrtRuleCtrl', function($scope, $uibModalInstance, $http, tab, grupos, $rootScope){
	$scope.newrule;
	$scope.regra;
	$scope.groupSelected;
	
	$scope.tabSelected = tab;
	$scope.grupos = grupos;
	
	$scope.ok = function () {
		var data = {'object':angular.toJson($scope.newrule)};
		
		if ($scope.groupSelected) {
			$http({
				method: 'POST',
				url: '/app/regras/add_regra/'+$rootScope.admin.id+'/'+$scope.groupSelected.id,
				data: $.param(data),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
				}
			}).
			success(function(data){
				$scope.regra = data
				$uibModalInstance.close($scope.regra);
			});
		} else {
			$http({
				method: 'POST',
				url: '/app/regras/add_regra/'+$rootScope.admin.id,
				data: $.param(data),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
				}
			}).
			success(function(data){
				$scope.regra = data
				$uibModalInstance.close($scope.regra);
			});
		}
	}
	
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	};
	
});

/**************************************
 * Definicoes para edição de regras
 **************************************/
app.controller('EdtRuleCtrl', function($scope, $uibModalInstance, $http, regra, grupos, $rootScope){
	$scope.regra = regra;
	
	for (var i=0; i < grupos.length; i++) {
		if ($scope.regra.grupo_regra_id === grupos[i].id)
			$scope.groupSelected = grupos[i];
	}
	
	$scope.grupos = grupos;
	
	$scope.ok = function () {
		var data = {'object':angular.toJson($scope.regra)};
		
		if ($scope.groupSelected) {
			$http({
				method: 'POST',
				url: '/app/regras/edita_regra/'+$rootScope.admin.id+'/'+$scope.groupSelected.id,
				data: $.param(data),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
				}
			}).
			success(function(data){
				$uibModalInstance.close(data);
			});
		} else {
			$http({
				method: 'POST',
				url: '/app/regras/edita_regra/'+$rootScope.admin.id,
				data: $.param(data),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
				}
			}).
			success(function(data){
				$uibModalInstance.close(data);
			});
		}
	}
	
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	};
	
});

/***********************************************
 * Definicoes para criação de grupos de regra
 ***********************************************/
app.controller('CrtGroupCtrl', function($scope, $uibModalInstance, tab, $http){

	$scope.tabSelected = tab;
	
	$scope.ok = function () {
		
		$http.get('/app/regras/add_grupo/'+$scope.newgroup).
			success(function(){
				$uibModalInstance.close();
			});
		
	};
	
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	};
	
});