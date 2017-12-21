define(['app','roles'], function(app, roles){
	return app.controller('RecadoCtrl', ['$http','$uibModal','dataservice','toastr','listYears','listMonths', 'filterCompany', function($http,$uibModal,dataservice,toastr,listYears,listMonths,filterCompany){
		var recado_layout = this;
		// years
		recado_layout.years = listYears;
		recado_layout.months = listMonths;
		recado_layout.dataTable = [];
		recado_layout.roles = roles;
		// variables auxiliares
		recado_layout.auxs = {
			totalItems: 0,
			loading: false
		};
		// variables de busqueda
		recado_layout.params = {
			year: (new Date()).getFullYear(),
			month: ((new Date()).getMonth()+1),
			estado: 'P',
			empresa_id: 0,
			limite: 20,
			pagina: 1
		};
		var modalController = function($uibModalInstance, items){
			var $ctrl = this;
			$ctrl.edit = false;
			$ctrl.params = {
				local_id: angular.copy(window.iVariables.locales)[0].id
			};
			$ctrl.locales = angular.copy(window.iVariables.locales);

			if(items !== undefined){
				$ctrl.row = items.recado;
				$ctrl.edit = items.edit;

				if(items.data !== undefined){
					$ctrl.data = items.data;
				}

				// Vemos si se esta editando
				if($ctrl.edit){
					$ctrl.params.empresa_id = items.recado.empresa_id;
					$ctrl.params.local_id = items.recado.local_id;
					$ctrl.params.entregado_a = items.recado.entregado_a;
					$ctrl.params.para = items.recado.para;
					$ctrl.params.contenido_paquete = items.recado.contenido_paquete;
				}
			}

			$ctrl.clear = function(){
				$ctrl.selected = '';
				$ctrl.params.empresa_id = '';
			};

			$ctrl.close = function(){
				$uibModalInstance.dismiss('cancel');
			};

			$ctrl.deliver = function(){
				$http({
					url: "/recado/" + $ctrl.row.id + "/deliver",
					method: "POST",
					params: {entregado_a: $ctrl.params.entregado_a}
				}).then(function(response){
					toastr.success('Recado entregado','Éxito');
					recado_layout.search();
					$ctrl.close();
				}, function(error){
					console.log(error);
					toastr.error(error.data.error, 'Error al entregar recado');
				});
			};

			$ctrl.filterCompanies = function(value, onlyActive){
				if(value === '' || value.length < 3){
					return [];
				} else{
					var params = {view:'minimal',empresa_nombre:value};
					if(onlyActive === 1){
						params.estado = 'A,S,X';
					}
					return dataservice.searchCompanies(params);
				}
			};

			$ctrl.send = function(){

				var method = "POST"; var curl = "/recado";

				if($ctrl.edit){
					method = "PUT";
					curl+="/" + $ctrl.row.id;
				}

				$http({
					url: curl,
					method: method,
					params: $ctrl.params
				}).then(function(response){
					if(method === 'POST')
						toastr.success('Recado creado','Éxito');
					else
						toastr.success('Recado editado', 'Éxito');
					recado_layout.search();
					$ctrl.close();
				}, function(error){
					console.log(error);
					toastr.error(error.data.error, 'Error al crear recado');
				}).finally(function(){

				});
			};

			$ctrl.delete = function(){
				$http({
					url: '/recado/' + $ctrl.row.id,
					method: 'DELETE',
					params: {observacion: $ctrl.params.observacion}
				}).then(function(response){
					toastr.success('Recado eliminado');
					$ctrl.close();
					recado_layout.search();
				}, function(error){
					console.log(error);
					toastr.error(error.data.error, 'Error al eliminar el recado');
				});
			};

			return $ctrl;
		};

		// metodos
		recado_layout.clearCompanies = function(){
			recado_layout.selected = '';
			recado_layout.params.empresa_id = 0;
			recado_layout.searchFilter();
		};

		recado_layout.filterCompanies = filterCompany;

		recado_layout.getHistory = function(row){
			$http({
				url: '/recado/' + row.id + '/history',
				method: 'GET'
			}).then(function(response){
				$uibModal.open({
					animation: true,
					templateUrl: '/templates/modals/recado/history.html',
					controller: ['$uibModalInstance','items', modalController],
					controllerAs: '$ctrl',
					resolve: {
						items: function(){
							return {edit:false, recado: row, data: response.data};
						}
					}
				}).result.then(function(){}, function(){});
			}, function(){
				toastr.error('No se pudo obtener el historial de este recado.');
			});
		};

		recado_layout.openForm = function(row){
			$uibModal.open({
				animation: true,
				templateUrl: '/templates/modals/recado/create.html',
				controller: ['$uibModalInstance', modalController],
				controllerAs: '$ctrl'
			}).result.then(function(){}, function(){});
		};

		recado_layout.openModal = function(row, action){

			var e = false;

			if(action === 'create'){
				e = true;
			}

			$uibModal.open({
				animation: true,
				templateUrl: '/templates/modals/recado/' + action + '.html',
				controller: ['$uibModalInstance','items', modalController],
				controllerAs: '$ctrl',
				resolve: {
					items: function(){
						return {edit:e, recado: row};
					}
				}
			}).result.then(function(){}, function(){});
		};

		recado_layout.search = function(){
			recado_layout.auxs.loading = true;
			$http({
				url: '/recado/search/' + recado_layout.params.year + '/' + recado_layout.params.month + '/' + recado_layout.params.estado,
				method: 'GET',
				params: recado_layout.params
			}).then(function(response){
				recado_layout.dataTable = response.data.rows;
				recado_layout.auxs.totalItems = response.data.total;
				recado_layout.auxs.loading = false;
			}, function(){
				recado_layout.auxs.loading = false;
			});
		};

		recado_layout.searchFilter = function(){
			recado_layout.params.pagina = 1;
			recado_layout.search();
		};
	}]);
});