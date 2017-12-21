define(['app','roles'], function(app, roles){
	app.controller('DetLlamadaCtrl', controller);
	controller.$inject = ['$filter','$http','$stateParams','$uibModal','toastr'];
	function controller($filter,$http,params, uimodal, toastr){
		var vm = this;
		var firstDay = new Date();
		firstDay.setDate(1);

		vm.isOpenedCldr1 = false;
		vm.isOpenedCldr2 = false;
		vm.loading = false;
		vm.data = [];

		vm.params = {
			disposicion: "ALL",
			fin: new Date(),
			inicio: firstDay
		};

		search();

		vm.openCalendar1 = function openCalendar1(){
			vm.isOpenedCldr1 = true;
		};

		vm.openCalendar2 = function openCalendar2(){
			vm.isOpenedCldr2 = true;
		};

		vm.search = search;

		function search(){
			vm.loading = true;
			var p = angular.copy(vm.params);
			p.inicio = $filter('date')(p.inicio, 'yyyy-MM-dd', 'America/Lima');
			p.fin = $filter('date')(p.fin, 'yyyy-MM-dd', 'America/Lima');
			p.cdr = empresa.basic.preferencia_cdr;

			$http({
				url: '/empresa/' + params.empresaID + '/llamadas/search',
				method: 'GET',
				params: p
			}).then(function(response){
				vm.data = response.data;
			}).finally(function(){
				vm.loading = false;
			});
		}
	}
});