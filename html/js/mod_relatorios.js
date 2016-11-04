var app = angular.module('Relatorios', ['ui.mask','ui.bootstrap','angularUtils.directives.dirPagination']);

//app.directive('ngEsc', function () {
//    return function (scope, element, attrs) {
//        element.bind("keydown keypress keyup", function (event) {
//            if(event.which === 27) {
//                scope.$apply(function (){
//                    scope.$eval(attrs.ngEsc);
//                });
//
//                event.preventDefault();
//            }
//        });
//    };
//});

app.controller('RelatorioCtrl', function($scope, $http){
	
	$scope.acessos = [];
	$scope.currentPage = 1;
	$scope.pageSize = 10;

	$scope.tAcessos = null;
	$scope.tTrafego = null;
	$scope.tDuracao = null;

	var de_l = moment().subtract(7, 'days').format('YYYY[-]MM[-]DD');
	var ate_l = moment().format('YYYY[-]MM[-]DD');
	
	$scope.de = moment().subtract(7, 'days').format('DD[-]MM[-]YYYY');
	$scope.ate = moment().format('DD[-]MM[-]YYYY');
	
	$scope.carregaRelatorio = function(de, ate) {
		$http.get('/app/registros/acessos/'+ de +'/'+ ate).
			success(function(data, status, headers, config) {
				$scope.acessos = data;
				
				$scope.tDuracao = null;
				
				for (var i=0; i < $scope.acessos.length; i++) {
					$scope.tAcessos += $scope.acessos[i].qtd_acessos;
					$scope.tTrafego += $scope.acessos[i].trafego;
					$scope.tDuracao += $scope.acessos[i].duracao;
					$scope.acessos[i].isopen = true;
					$scope.acessos[i].pageId = i;
				}
			}).
			error(function(data, status, headers, config){
				feedback('warning','Problema no módulo de relatórios..');
			});
	};
	
	$scope.carregaRelatorio(de_l, ate_l);

	$scope.carregaRelByLogin = function(acesso) {
		var idx = $scope.acessos.indexOf(acesso);
		
		for (var i=0; i < $scope.acessos.length; i++) {
			if ($scope.acessos[i].login != $scope.acessos[idx].login)
				$scope.acessos[i].isopen = true;
		}

		if ($scope.acessos[idx].isopen === true) {
			$http.get('/app/registros/acessos/'+ de_l +'/'+ ate_l +'/'+ acesso.login).
				success(function(data, status, headers, config) {
					$scope.acessos[idx].detalhes = [];
					$scope.acessos[idx].detalhes = data;
				}).
				error(function(data, status, headers, config){
					feedback('warning','Problema no módulo de relatórios..');
				});
			$scope.acessos[idx].isopen = false;
		} else
			$scope.acessos[idx].isopen = true;
	};
	
	$scope.setPeriodo = function () {
		if ($scope.de == null|| $scope.ate == null )
			return;
		
		$scope.tAcessos = null;
		$scope.tTrafego = null;
		$scope.de = $scope.de.replace(/-/g, '');
		$scope.ate = $scope.ate.replace(/-/g, '');
		de_l = $scope.de.substr(4,4) +'-'+ $scope.de.substr(2,2) +'-'+ $scope.de.substr(0,2);
		ate_l = $scope.ate.substr(4,4) +'-'+ $scope.ate.substr(2,2) +'-'+ $scope.ate.substr(0,2);
		$scope.carregaRelatorio(de_l, ate_l);
	};
	
	$scope.formatSizeUnits = function (bytes) {
		if (!bytes)
			return false
			
        if      (bytes>=1000000000) {bytes=(bytes/1000000000).toFixed(2)+' GB';}
        else if (bytes>=1000000)    {bytes=(bytes/1000000).toFixed(2)+' MB';}
        else if (bytes>=1000)       {bytes=(bytes/1000).toFixed(2)+' KB';}
        else if (bytes>1)           {bytes=bytes+' bytes';}
        else if (bytes==1)          {bytes=bytes+' byte';}
        else                        {bytes='0 byte';}
        return bytes;
	};
	
	$scope.formatDuration = function (secs) {
		if (!secs)
			return;
		
		var duracao;
		
		if 		(secs >= 7200) 	duracao = (secs/3600).toFixed(0)+' horas';
		else if (secs >= 3600)	duracao = (secs/3600).toFixed(0)+' hora';
		else if (secs >= 120)	duracao = (secs/60).toFixed(0)+' minutos';
		else if (secs >= 60)	duracao = (secs/60).toFixed(0)+' minuto';
		else if (secs >= 2)		duracao = secs+' segundos';
		else 					duracao = '1 segundo';
		
		return duracao;
		//return moment.duration(secs, "seconds").humanize();
	};

	$scope.dynamicPopover = {
		templateUrl: 'detalhesPopover.html'
	};
	
});