define(['app','roles'], function(app, roles){

	app.controller('EmpresaCtrl', empresaCtrl);
	empresaCtrl.$inject = ['$http','$state','$uibModal','toastr','dataservice','listAllYears','listMonths'];

	function empresaCtrl($http, $state, $uibModal, toastr, dataservice,listAllYears,listMonths){
		var vm = this; // ViewModel
		vm.empresas = [];
		vm.extras = [{id:'-', nombre:'Extras'}].concat(window.iVariables.extras);
		vm.planes = [{id:'-', nombre:'Planes'}].concat(window.iVariables.planes);
		vm.auxs = {
			loading: false,
			totalItems: 0,
			inputph: 'Nombre de la empresa'
		};
		
		vm.years = listAllYears;
		vm.months = listMonths;
		vm.changePlaceholder = changePlaceholder;
		vm.defaultParams = defaultParams;
		vm.filterCompanies = filterCompanies;
		vm.filterCustom = filterCustom;
		vm.getCentral = getCentral;
		vm.searchFilter = searchFilter;
		vm.search = search;
		vm.gotoCorrespondencia = gotoCorrespondencia;
		vm.goToCompany = goToCompany;
		vm.exportar = exportar;
		vm.selectedCompany = {nombre:''};
		vm.roles = roles;

		function changePlaceholder(){
			var ph = "";
			switch(vm.params.search_filter){
	            case "empresa_nombre":
	            		ph = "Nombre de la empresa";
	            	break;
	            case "empresa_ruc":
	            		ph = "RUC";
	            	break;
	            case "empresa_rubro":
	            		ph = "Rubro de la empresa";
	            	break;
	            case "representante":
	            		ph = "Nombre del representante";
	            	break;
	            case "encargado":
	            	    ph = "Nombre del encargado";
	            	break;
	            case "pbx":
	            		ph = "Número o Nombre de central";
	            	break;
	            case "empleado":
	            	    ph = "Nombre del empleado";
	            	break;
			}

			vm.auxs.inputph = ph;
		}

		function defaultParams(){
			vm.params = {
				fechafiltro:"C",
				extras: '-',
	            year: "Todos",//(new Date()).getFullYear(),
	            month:0,// ((new Date()).getMonth()+1),
				plan: '-',
				preferencia_estado: 'A',
				pagina: 1,
				limite: 20,
				preferencia_facturacion: '-',
				preferencia_fiscal: '-',
				central: '-',
				del_state: '-',
				promo: 'N',
				convenio: '-',
				extrastipo: '-',
				factura_id: '-',
				search_filter: 'empresa_nombre',
				search_value: ''
			};
		}

		function filterCompanies(value){
			if(value.length > 3 && (vm.params.search_filter === 'empleado' || vm.params.search_filter === 'representante')){
			} else {
				return [];
			}
		}

		function filterCustom(value){
			return $http({
				url: '/' + vm.params.search_filter + '/filter',
				method: 'GET',
				params: {'value': value}
			}).then(function(r){
				return r.data;
			}).catch(function(e){
				toastr.error(e.data.error, 'Error en ' + e.data.modulo);
			});
		}

		function goToCompany(){
			$state.go('empresa.info', {empresaID: vm.selectedCompany.empresa_id});
		}

		function getCentral(id){
			dataservice.getCentral(id).then(function(response){
				$uibModal.open({
					animation: true,
					templateUrl: 'templates/modals/central/info.html',
					controller: ['$uibModalInstance', modalController],
					controllerAs: '$ctrl'
				}).result.then(function(){}, function(){});

				function modalController($uibModalInstance){
					var $ctrl = this;
					$ctrl.row = response.data;
					$ctrl.close = function(){
						$uibModalInstance.dismiss('cancel');
					};
				}
			}).catch(function(response){
				console.log(response);
				toastr.error(response.data, 'Error al obtener la central');
			});
		}

		function gotoCorrespondencia(id, nombre){
			$state.go('recado-correspondencia.correspondencia', {empresa_id:id, empresa_nombre:nombre});
		}

		function searchFilter(){
			vm.params.pagina = 1;
			search();
		}

		function exportar(){
            var json = angular.copy(vm.params);
            if( json.limite !== undefined ){
            	delete json.limite;
            }
            window.open('/export/empresa?json=' + JSON.stringify(json),'');
		}

		function search(){
			vm.auxs.loading = true;
			dataservice.searchCompanies(vm.params).then(function(data){
				vm.empresas = data.rows;
				vm.auxs.totalItems = data.total;
			}).finally(function(){
				vm.auxs.loading = false;
			});
		}
	}
});