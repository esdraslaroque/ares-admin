var app = angular.module('Usuarios', ['nya.bootstrap.select',
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

app.filter('numberFixedLen', function () {
    return function (n, len) {
        var num = parseInt(n, 10);
        len = parseInt(len, 10);
        if (isNaN(num) || isNaN(len)) {
            return n;
        }
        num = ''+num;
        while (num.length < len) {
            num = '0'+num;
        }
        return num;
    };
});

/************************************
 * Definicoes para Módulo de usuários
 ************************************/
app.controller('UsuariosCtrl', ['$scope','$http','$rootScope','$uibModal','$interval', '$log', 'ngToast',
function($scope, $http, $rootScope, $uibModal, $interval, $log, ngToast){
	
	$scope.user = [];
	$scope.currentPage = 1;
	$scope.pageSize = 16;
	
	$rootScope.searchType;

	$scope.carregaUsuarios = function() {
		$http.get('/app/usuarios/').
			success(function(data, status, headers, config) {
				$scope.user = data;
				$scope.loaded = true;
			}).
			error(function(data, status, headers, config){
				feedback('warning','Problema no módulo de usuários..');
			});
	}

	$scope.carregaUsuarios();

	$scope.atualiza = function() {
		delete $rootScope.searchType;
		$scope.carregaUsuarios();
	}

	$rootScope.realtime = $interval(function(){
		$http.get('/app/conexoes/ativas/').
			success(function(data){
				for (var i=0; i < $scope.user.length; i++) {
					$scope.user[i].isconect = 0;

					if (! data.length < 1) {
						for (var j=0; j < data.length; j++) {
							if ($scope.user[i].id == data[j].usuario_id && data[j].autenticado == 1) {
								$scope.user[i].isconect = 1;
							}
						}
					}
				}
			}).
			error(function(){
				$log.warn('UsuariosCtrl: Problema na consulta periódica de status para conexões!');
			});
	}, 20000);

	$scope.filterDashboard = function(person) {
		if (! $rootScope.searchType)
			return true;

		if ($rootScope.searchType == 1)
			if(person.ativo == 1 && $scope.checkExpired(person.validade))
				return true;
		if ($rootScope.searchType == 2)
			if (person.ativo == 0)
				return true;
		if ($rootScope.searchType == 3)
			if(person.ativo == 1 && ! $scope.checkExpired(person.validade))
				return true;
		if ($rootScope.searchType == 4)
			if($scope.checkExp15(person.validade))
				return true;
		if ($rootScope.searchType == 5)
			if(person.isconect == 1)
				return true;
		if ($rootScope.searchType == 6)
			if(person.ad_status == 1)
				return true;
		if ($rootScope.searchType == 7)
			if(person.ad_status == 2)
				return true;
		if ($rootScope.searchType == 8)
			if(person.ad_status == 3)
				return true;
	}

	$scope.checkExpired = function(date) {
		if (date == null)
			return false;
		
		var today = new Date();
		var dateS = date.split('-');
		var dateV = new Date(dateS[0], dateS[1]-1, dateS[2]);

		if (today > dateV)
			return true;

		return false;
	}

	$scope.checkExp15 = function(date) {
		if (date == null)
			return false;
		
		var today = new Date();
		var someday = new Date();
		someday.setDate(someday.getDate() + 15);

		var dateS = date.split('-');
		var dateV = new Date(dateS[0], dateS[1]-1, dateS[2]);

		 if (dateV > today && dateV <= someday) {
		 	return true
		 }
	}

	$scope.checkRen = function(date) {
		if (date == null)
			return false;
		
		var today = new Date();
		var dateS = date.split('-');
		var dateV = new Date(dateS[0], dateS[1]-1, dateS[2]);
		var renDate = new Date(dateS[0], dateS[1]-1, dateS[2]);
		renDate.setDate(renDate.getDate() - 15);

		if (today > dateV)
			return false;
		if (today >= renDate) {
			if (daysBetween(today, dateV) > 0)
				return daysBetween(today, dateV) + 1 +' dias';
			else
				return daysBetween(today, dateV) + 1 +' dia';
		}

		return false;
	}

	$scope.checkAD = function(person) {
		var cod = $scope.user[$scope.user.indexOf(person)].ad_status;
		var msg = null;

		if (cod == 1)
			msg = 'Não existe no AD';
		else if (cod == 2)
			msg = 'Bloqueado no AD';
		else if (cod == 3)
			msg = 'Fora do grupo ARES no AD';

		if (msg)
			return msg;

		return false;
	}

	$scope.ativaUser = function(person) {
		var idx = $scope.user.indexOf(person);
		var user = person;
		var status;

		(user.ativo == 1) ? status = 0 : status = 1;

		$http.get('/app/usuarios/ativa_usuario/'+user.id+'/'+status).
			success(function(){
				$scope.user[idx].ativo = status;
			});
	} // ativaUser

	$scope.genKit = function (person) {
		var user = person;
		$http.get('/app/usuarios/gera_kit/'+user.login+'/true').
			success(function(data){
				if (data.cod == 1)
					feedback('info', data.msg);
				else if (data.cod == 0)
					feedback('success', data.msg);
			});
	} // genKit

	$scope.confirmDel = function (person) {
		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'removeModal.html',
			controller: 'RmUserCtrl',
			resolve: {
				person: function(){ return person }
			}
		});

		modalInstance.result.then(function(){
			$scope.user.splice($scope.user.indexOf(person), 1);
		});
	} // confirmDel

	$scope.renewKey = function (person) {
		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'renewModal.html',
			controller: 'RenewCtrl',
			resolve: {
				person: function(){ return person }
			}
		});
		modalInstance.result.then(function(renew){
			if (renew.cod == 1) {
				feedback('danger', renew.msg);
			} else {
				var idx = $scope.user.indexOf(person);
				$scope.user[idx].id_key = renew.id_key;
				$scope.user[idx].validade = renew.expira;
				feedback('success', 'Chave para <b>'+person.login+'</b> renovada!');
			}
		});
	} // renewKey

	$scope.editUser = function (person) {

		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'editModal.html',
			controller: 'EdtUserCtrl',
			resolve: {
				person: function(){ return person }
			}
		});

//		modalInstance.result.then(function(){
//			alert('Chave do usuário '+ person.login +' atualizada!');
//		});

	} // editUser

	$scope.createUser = function () {

		var modalInstance = $uibModal.open({
			animation: true,
			templateUrl: 'createModal.html',
			controller: 'CrtUserCtrl'
		});

		modalInstance.result.then(function(newuser){
			if (!newuser)
				return;

			$http.get('/app/usuarios/'+ newuser.login).
				success(function(data){
					$scope.newuser = data;
					$scope.user.push($scope.newuser);

					if (newuser.perms.length > 0) {
						var perms_id = [];

						for (var i=0; i < newuser.perms.length; i++)
							perms_id.push(newuser.perms[i].id);

						var d = {'object': angular.toJson(perms_id) };

						$http({
							url: '/app/permissoes/add/'+$scope.newuser.id+'/'+$rootScope.admin.id,
							method: 'POST',
							data: $.param(d),
							headers: {
								'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
							}
						});
					}
				}).
				error(function(){ return });
		});
	} // createUser

	$scope.setUser = function(person) {
		var idx = $scope.user.indexOf(person);
		$rootScope.usuario = $scope.user[idx];
	}

	$scope.dynamicPopover = {
		    templateUrl: 'userConnectedPopover.html'
	};

	daysBetween = function( date1, date2 ) {
		  //Get 1 day in milliseconds
		  var one_day=1000*60*60*24;

		  // Convert both dates to milliseconds
		  var date1_ms = date1.getTime();
		  var date2_ms = date2.getTime();

		  // Calculate the difference in milliseconds
		  var difference_ms = date2_ms - date1_ms;

		  // Convert back to days and return
		  return Math.round(difference_ms/one_day);
	}

	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	}

}]);

/**************************************
 * Definicoes para Inforcao de Conexao
 **************************************/
app.filter('moment', function () {
	  return function (input, momentFn /*, param1, param2, ...param n */) {
	    var args = Array.prototype.slice.call(arguments, 2),
	        momentObj = moment(input);
	    return momentObj[momentFn].apply(momentObj, args);
	  };
});

app.controller('ConnUserCtrl', function($scope, $http, $rootScope){
	var user_id = $rootScope.usuario.id;
	$scope.conexao = [];

	$http.get('/app/usuarios/conexao_info/'+user_id).
		success(function(data) {
			$scope.conexao = data;
		});
});

/**************************************
 * Definicoes para renovação de chave
 **************************************/
app.controller('RenewCtrl', function($scope, $uibModalInstance, $http, person, $rootScope){
	$scope.periodo = 30;
	$scope.persoPeriodo = null;
	$scope.processo = null;
	$scope.person = person;
	$scope.provisorio = false;

	var validade;

	$scope.ok = function() {
		var uri;

		if ($scope.provisorio) {
			validade = 1;
			uri = '/app/usuarios/renova_chave/'+person.login+'/'+validade+'/'+$rootScope.admin.id+'/'+person.id;
		} else {
			($scope.persoPeriodo) ? validade = $scope.persoPeriodo : validade = $scope.periodo;
			uri = '/app/usuarios/renova_chave/'+person.login+'/'+validade+'/'+$rootScope.admin.id+'/'+person.id+'/'+$scope.processo;
		}

//		alert('Login: '+ person.login +'\nValidade: '+ validade +'\nAdmin_id: '+$rootScope.admin.id +
//				'\nPessoa_id: '+person.id +'\nProcesso: '+ $scope.processo);
		$http.get( uri ).
			success(function(data){
				if (data.cod == 1) {
					$uibModalInstance.close(data);
					return;
				}
				$http.get('/app/usuarios/consulta_chave/'+person.login).
					success(function(data){
						$uibModalInstance.close(data);
				});
			}).
			error(function(data){
				$uibModalInstance.close(data);
			});
	}

	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	};

});

/**************************************
 * Definicoes para remoção de usuários
 **************************************/
app.controller('RmUserCtrl', function($scope, $uibModalInstance, person, $http, ngToast){
	$scope.person = person;

	$scope.ok = function () {
		$http.get('/app/usuarios/remove_ares/'+person.id).
			success(function(){
				$uibModalInstance.close(person);
				feedback('success','Usuário <b>'+person.login+'</b> removido');
			}).
			error(function(){
				feedback('danger','Falha na remoção do usuário <b>'+ person.login +'<b>');
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
app.controller('CrtUserCtrl', function($scope, $uibModalInstance, $http, $rootScope, ngToast){
	$scope.periodo = 30;
	$scope.persoPeriodo = null;
	$scope.validade;

	$scope.regras = [];
	$scope.selected = [];
	$scope.usuario;
	$scope.adUsers;

	$scope.ok = function () {
		($scope.persoPeriodo) ? $scope.validade = $scope.persoPeriodo : $scope.validade = $scope.periodo;
		var data = {'object':angular.toJson($scope.usuario)};

		$http({
			method: 'POST',
			url: '/app/usuarios/add_usuario/'+ $scope.validade +'/'+ $rootScope.admin.id,
			data: $.param(data),
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			}
		}).
		success(function(data){
			if (data.cod == 0) {
				var result = [];
				result.login = $scope.usuario.login;
				result.perms = $scope.selected;

				$uibModalInstance.close(result);
				feedback('success','Usuário <b>'+result.login+'</b> cadastrado');

				$http.get('/app/usuarios/gera_kit/'+result.login).
					success(function(data){
						if (data.cod == 1)
							feedback('info', data.msg);
						else if (data.cod == 0)
							feedback('success', data.msg);
					});
			}
			else if (data.cod == 1) {
				$uibModalInstance.close();
				feedback('danger',data.msg);
			} else
				$uibModalInstance.close();
		}).
		error(function(){
			$uibModalInstance.close();
			feedback('danger','Falha ao cadastrar usuario no banco de dados!');
		});
	} // OK

	$http.get('/app/regras/').
		success(function(data) {
				$scope.regras = data;
		}).
		error(function(data, status, headers, config){
				alert('Error in CrtUserCtrl from AngularJS');
		});

	$http.get('/app/usuarios/ad_membros/ares_users').
		success(function(response){
			$scope.adUsers = JSON.parse(JSON.stringify(response));
		}).
		error(function(data, status, headers, config){
			alert('Error in CrtUserCtrl from AngularJS');
		});


	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	} // Cancel

	$scope.checkExist = function(usuario) {
		if ($scope.adUsers.indexOf(usuario) == -1)
			$scope.nok = true;
		
		if (!usuario || !usuario.login || usuario.login == null)
			return;
		
		$scope.nok = false;

		$http.get('/app/pessoas/'+ usuario.login).
			success(function(data){
				if (data == null)
					return;

				$scope.usuario.nome = data.nome;
				$scope.usuario.email = data.email;
				
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
app.controller('EdtUserCtrl', function($scope, $log, $http, $rootScope, person, $uibModalInstance, ngToast){
	$scope.person = person;
	$scope.person2mod;

	$scope.selected = [];
	$scope.selectedlength;
	$scope.regras = [];
	$scope.permissoes = [];
	var populado = false;

	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	}

	$scope.ok = function() {
		var data = {'object': angular.toJson($scope.person2mod) };

		$http({
			method: 'POST',
			url: '/app/usuarios/edita_usuario/'+person.id,
			data: $.param(data),
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			}
		}).
//		$http.get('/app/usuarios/edita_usuario/'+$scope.person.id+'/'+$scope.person2mod.nome+'/'+email).
		success(function(){
			$scope.person.nome = $scope.person2mod.nome;
			$scope.person.email = $scope.person2mod.email;

			var perms_id = [];

			for (var i=0; i < $scope.selected.length; i++)
				perms_id.push($scope.selected[i].id);

			var d = {'object': angular.toJson(perms_id) };

			$http({
				url: '/app/permissoes/edit/'+person.id+'/'+$rootScope.admin.id,
				method: 'POST',
				data: $.param(d),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
				}
			});

			$uibModalInstance.close();
			feedback('success','Usuário <b>'+person.login+'</b> editado');
		}).
		error(function(){
			feedback('danger','Falha ao editar <b>'+person.login+'</b>');
		});
	} // ok

	$http.get('/app/permissoes/'+$scope.person.id).
		success(function(data) {
			if (data == null)
				return;

			$scope.permissoes = data;

		}).
		error(function(data, status, headers, config){
			alert('Error in EdtUserCtrl from /app/permissoes');
		});

	$scope.$watch('regras', function() {
		if (!populado)
			populaSelected();
			$scope.selectedlength = $scope.selected.length;
	});

	$scope.$watch('permissoes', function() {
		carregaRegras();
	})

	carregaRegras = function () {
		$http.get('/app/regras/').
			success(function(data) {
				$scope.regras = data;
			}).
			error(function(data, status, headers, config){
				alert('Error in EdtUserCtrl from /app/regras');
			});
	}

	populaSelected = function () {
		for (var i=0; i < $scope.regras.length; i++)
			for (var j=0; j < $scope.permissoes.length; j++)
				if ($scope.regras[i].id == $scope.permissoes[j].regra_id) {
					$scope.selected.push($scope.regras[i]);
				}
		if ($scope.permissoes.length > 0)
			populado = true;
	}

	feedback = function (type, msg) {
		ngToast.create({
			className: type,
			content: msg
		});
	}

});