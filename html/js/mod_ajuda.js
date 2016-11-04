var app = angular.module('Ajuda', []);

app.controller('AjudaCtrl', function($scope, $http){
	$scope.manuais = [];
	
	$scope.next = function () {
		$scope.passo++;
	};

	$scope.previous = function () {
		if ($scope.passo == 1)
			return;
		$scope.passo--;
	};
	
	$http.get('/app/ajuda/list_manuais').
		success(function(data){
			$scope.manuais = data;
		});
	
	$scope.tplUrl = function(manual) {
		return '/app/ajuda/'+ $scope.manuais[$scope.manuais.indexOf(manual)].indice;
	};
	
});

//app.directive('load', function ($http, $compile) {
//	return {
//		scope: { page: '@', passo: '@' },
//		link: function (scope, element, attrs) {
//			$http.get('/app/ajuda/'+attrs.page).success(function (response) {
//				var contents = angular.element("<div>").html(response).find('#passo'+attrs.passo);
//				element.empty().append($compile(contents)(scope));
//			});
//		}
//	}
//});