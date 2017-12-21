define(['app','roles'], function(app, roles){
	app.controller('DetalleReservaCtrl', controller);
	controller.$inject = ['$stateParams','$uibModal','ListSrv','ReservaSrv','toastr'];
	function controller(sparams, uimodal, listSrv, mySrv, toastr){
		var vm = this;
		vm.consumo = "00:00";
		vm.years = listSrv.years();
		vm.months = listSrv.months();
		vm.loading = false;
		vm.params = {
			modelo_id: 0,
			anio: (new Date()).getFullYear(),
			mes: (new Date()).getMonth()+1,
			empresa_id: sparams.empresaID,
			consumo: 'on'
		};
        vm.auxs = {
            modelos: [{id:0, nombre:"Tipo"}].concat(window.iVariables.modelos),
        };
        console.log("aaa");

		vm.search = loadData;

		if(empresa.reservas === null){
			loadData();
		} else {
			vm.data = angular.copy(empresa.reservas.rows);
			vm.consumo = angular.copy(empresa.reservas.consumo);
		}

		function loadData(){
			vm.loading = true;
			mySrv.search(angular.copy(vm.params)).then(function(r){
				vm.data = angular.copy(r.data.rows);
				vm.consumo = angular.copy(r.data.consumo);
				empresa.reservas = angular.copy(r.data);
			}).finally(function(){
				vm.loading = false;
			});
		}
	}
});