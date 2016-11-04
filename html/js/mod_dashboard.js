var app = angular.module('Dashboard', ['chart.js']);

app.config(function(ChartJsProvider) {
	ChartJsProvider.setOptions('Bar', [{ hoverBackgroundColor: "rgba(255,99,132,0.4)" }]);
});

app.controller('DashboardCtrl', function($scope, $http, $interval, $rootScope){
	
	$scope.browser = false;

	$scope.usuarios = [];
//#DE5240
	$scope.chartColors = ['#0AA960','#F4A830','#BD3F3F'];
	/* Variaveis para Grafico de rosca */
	$scope.doughnutLabels = ['Válidos','Expirados','Inativos'];
	$scope.doughnutData = [];

	/* Variaveis para Grafico de linhas */
	var agora = new Date();
	$scope.linesLabels = [];
	$scope.linesSeries = ['Recebido','Enviado'];
	$scope.linesData = [];

	$scope.carregaData = function() {
		$http.get('/app/dashboard/trafego_rede').
			success(function(data){
				var traffic = [], b_in = [], b_out = [];

				traffic = data

				$scope.linesData.pop(); $scope.linesData.pop();
				for (var j=0; j < traffic.length-1; j++)
					$scope.linesLabels.pop();

				for (var i=0; i < traffic.length-1; i++) {
					$scope.linesLabels.push(traffic[i].date.substr(6,5));
					b_in.push(Math.floor(traffic[i].in/1000));
					b_out.push(Math.floor(traffic[i].out/1000));
				}
				$scope.linesData.push(b_in);
				$scope.linesData.push(b_out);
			}).
			error(function(){
				alert('falha do grafico de linhas');
			});
	}

	$http.get('/app/dashboard/index/').
		success(function(data) {
			$scope.usuarios = data;
			$scope.doughnutData.push(data.ativo);
			$scope.doughnutData.push(data.expirado);
			$scope.doughnutData.push(data.inativo);
		}).
		error(function(){
			alert('Error in DashboardCtrl from AngularJS');
		});

	$rootScope.realtime = $interval(function(){
		$scope.carregaData();
	}, 60000);

	$scope.carregaData();
	
	$scope.scala = function(num) {
		if (num) {
			if (num < 1023)
				return 'B';
			else if (num < 1048575)
				return 'KB';
			else if (num < 1073741823)
				return 'MB';
			else
				return 'GB';
		} else
			return false;
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

});

/*
*Controller grafico de rosca usuarios AD
*/
app.controller('RoscaGraphCtrl', function($scope, $http, $rootScope){
	$scope.chartADColors = ['#F7464A','#8CD4CC','#97bbcc'];

	$scope.doughnutLabels = ['Não existe no AD','Bloqueado no AD','Fora do grupo ARES'];
	$scope.doughnutData = [];

	$scope.totais = [];

	calcAD = function(status, value, tamanho) {
		var total = 0;
		for (var i=0; i < tamanho; i++)
			if (status == value[i].ad_status)
				total++;
		return total;
	}

	$scope.car = function() {
			$http.get('/app/usuarios/').
				success(function(data) {
					$scope.totais.cod1 = calcAD(1, data, data.length);
					$scope.totais.cod2 = calcAD(2, data, data.length);
					$scope.totais.cod3 = calcAD(3, data, data.length);
					$scope.doughnutData.push($scope.totais.cod1);
					$scope.doughnutData.push($scope.totais.cod2);
					$scope.doughnutData.push($scope.totais.cod3);
				}).
				error(function(){
					alert('Error in RoscaGraphCtrl from AngularJS');
				});
	}

	$scope.car();
});

/**************************************
 *GRAFICO DE BARRA
 **************************************/
/*
 * Controller para estatísticas de conexão
 */
app.controller('EstatisticasConexao', function($http,$scope){
	$scope.barLabels;
	$scope.barData;
	$scope.nivel = 1;
	$scope.refer;

	$scope.loadGraph = function(nivel, dat = null) {
		if (nivel == 1) {
			$scope.nivel = 1;
			$scope.periodo = 'mês';

			$http.get('/app/dashboard/conexao_estatistica/1').success(function(data){
				$scope.barLabels = data.labels;
				$scope.barData = [data.dados];
			});
		} else if (nivel == 2) {
			$scope.nivel = 2;

			$http.get('/app/dashboard/conexao_estatistica/2/'+dat[0].label).success(function(data){
				$scope.barLabels = data.labels;
				$scope.barData = [data.dados];
				$scope.periodo = 'dia ('+ dat[0].label +')';
				$scope.refer = data.refer;
			});

		} else if (nivel == 3) {
			if (! $scope.refer)
				return;

			$scope.nivel = 3;

			$http.get('/app/dashboard/conexao_estatistica/3/'+$scope.refer+'/'+dat[0].label).success(function(data){
				$scope.barLabels = data.labels;
				$scope.barData = [data.dados];
				$scope.periodo = 'hora (dia '+ dat[0].label +')';
			});
		} else
			return false;
	};

	$scope.loadGraph(1, null);

	$scope.onClick = function (points) {
		if ($scope.nivel == 3)
			return;
		else
			$scope.loadGraph($scope.nivel + 1, points);
	};
});
/**************************************
 * grafico de rosca
 **************************************/
/*
 * Controller dos sumários de situação dos usuários no ARES
 */
app.controller('SituacaoARES', function($scope, $rootScope){
	$scope.validUser = function(){
		$rootScope.searchType = 3;
		$rootScope.pill = 1;
        $rootScope.loadModule('usuarios');
	}

	$scope.inativoUser = function(){
		$rootScope.searchType = 2;
		$rootScope.pill = 1;
		$rootScope.loadModule('usuarios');
	}

    $scope.expiradoUser = function(){
        $rootScope.searchType = 1;
        $rootScope.pill = 1;
        $rootScope.loadModule('usuarios');
    }
});
/*
 * Controller dos sumários de situação dos usuários no AD
 */
app.controller('SituacaoAD', function($scope, $rootScope){
	$scope.notexistAD = function(){
		$rootScope.searchType = 6;
		$rootScope.pill = 1;
        $rootScope.loadModule('usuarios');
	}

	$scope.blockedAD = function(){
		$rootScope.searchType = 7;
		$rootScope.pill = 1;
		$rootScope.loadModule('usuarios');
	}

    $scope.outgroupAD = function(){
        $rootScope.searchType = 8;
        $rootScope.pill = 1;
        $rootScope.loadModule('usuarios');
    }
});
/**************************************
 * smallBoxCtrl
 **************************************/
app.controller('smallBoxCtrl', function ($scope, $sce, $rootScope) {
	$scope.exp15User = function(){
	    $rootScope.searchType = 4;
	    $rootScope.pill = 1;
	    $rootScope.loadModule('usuarios');
	}

	$scope.accessUser = function(){
	    $rootScope.searchType = 5;
	    $rootScope.pill = 1;
	    $rootScope.loadModule('usuarios');
	}

	// $scope.exp15User = function(){
	//     $rootScope.searchType = 4;
	//     $rootScope.pill = 1;
	//     $rootScope.loadModule('usuarios');
	// }
});
/**************************************
 * Date
 **************************************/
app.controller('datCtrl', function($scope) {
    $scope.today = new Date();
});

/**************************************
 *Tabs
 **************************************/

app.controller('TabsGraphCtrl', function ($scope, $window) {
  $scope.model = {
    name: 'Tabs'
  };
});

/**************************************
 *GRAFICO DE BARRA -  RENOVAÇÔES ARES
 **************************************/
 app.controller('EstatisticasARES', function($http,$scope){
 	$scope.barADColors = ['#68CD77'];
	$scope.barLabels;
	$scope.barData;
	$scope.nivel = 1;
	$scope.refer;

	$scope.loadGraph = function(nivel, dat = null) {
		if (nivel == 1) {
			$scope.nivel = 1;
			$scope.periodo = 'mês';

			$http.get('/app/dashboard/ares_estatistica/1').success(function(data){
				$scope.barLabels = data.labels;
				$scope.barData = [data.dados];
			});
		} else if (nivel == 2) {
			$scope.nivel = 2;

			$http.get('/app/dashboard/ares_estatistica/2/'+dat[0].label).success(function(data){
				$scope.barLabels = data.labels;
				$scope.barData = [data.dados];
				$scope.periodo = 'dia ('+ dat[0].label +')';
				$scope.refer = data.refer;
			});

		} else if (nivel == 3) {
			if (! $scope.refer)
				return;

			$scope.nivel = 3;

			$http.get('/app/dashboard/ares_estatistica/3/'+$scope.refer+'/'+dat[0].label).success(function(data){
				$scope.barLabels = data.labels;
				$scope.barData = [data.dados];
				$scope.periodo = 'hora (dia '+ dat[0].label +')';
			});
		} else
			return false;
	};

	$scope.loadGraph(1, null);

	$scope.onClick = function (points) {
		if ($scope.nivel == 3)
			return;
		else
			$scope.loadGraph($scope.nivel + 1, points);
	};
});