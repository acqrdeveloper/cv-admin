define(['app','roles','angular'], function(app, roles, angular){
	return app.controller("FacturaCtrl", ['$http', '$scope', '$timeout', '$uibModal', function($http, $scope, $timeout, $uibModal) {
		$scope.years = [];
		for(var i = (new Date()).getFullYear(); i>=2015; i--){
			$scope.years.push(i);
		}
		$scope.params = {
			filter: '',
			anio: (new Date()).getFullYear(),
			mes: (new Date()).getMonth(),
			ciclo:  ( (new Date()).getDate() * 1 ) >= 25 ?  'MENSUAL' : 'QUINCENAL'
		};
		$scope.months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
		$scope.states = Object.assign({"-":"Todos"},angular.copy(window.iVariables.estados.factura));
		$scope.loading = true;
		$scope.data = [];

		$scope.clear = function(){
			$scope.methods.searchFromForm();
		};

		$scope.methods = {
			sendsunat: function(){
				window.location.replace("factura/send_facturame");
			},
			export: function(){
				var json = angular.copy($scope.params);
				window.open('/export/facturanualreporte?json=' + JSON.stringify(json),'');
			},
			search : function(){
				$scope.loading = true;
				$http({
					url: '/factura/facturacion_empresas/' + $scope.params.anio + '/' + (($scope.params.mes*1)+1) + '/' + $scope.params.ciclo,
					method: 'GET'
				}).then(function(response){
						$scope.loading = false;
						$scope.data = response.data.rows;
						$scope.nextnumero = response.data.nextnumero;
				});
			},searchFromForm: function(){
				$scope.methods.search();
			}
		};

		$scope.methods.search();
		$scope.roles = roles;
	}]);
});