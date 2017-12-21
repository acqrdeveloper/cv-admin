define(['app','roles'], function(app, roles){
	app.controller('CentralCtrl', controller);

	controller.$inject = ['$stateParams','$uibModal','CentralSrv','toastr'];

	function controller(params, uimodal, mySrv, toastr){
		var vm = this;

		vm.configEditEnabled = false;
		vm.edit = {
			cdr: false,
			config: false
		};
		vm.roles = roles.getRolesByEntity("empresas");

		vm.saving = false;

		// functions
		vm.clearCdr = clearCdr;
		vm.enableConfigEdit = enableConfigEdit;
		vm.enableCdrEdit = enableCdrEdit;
		vm.openModal = openModal;
		vm.saveCdr = saveCdr;
		vm.saveConfig = saveConfig;

		// Get pbx info
		if(empresa.central === null){
			loadData();
		} else {
			vm.row = angular.copy(empresa.central);
			vm.params = angular.copy(empresa.central);
			vm.row.empresa_id = params.empresaID;
		}

		function clearCdr(){
			vm.params.cdr = '';
		}

		function enableCdrEdit(){
			vm.edit.cdr = !vm.edit.cdr;
			vm.params.cdr = angular.copy(vm.row.cdr);
		}

		function enableConfigEdit(){
			vm.params = angular.copy(vm.row);
			vm.edit.config = !vm.edit.config;
		}

		function modalController($uibModalInstance, items){
			var $ctrl = this;
			$ctrl.sending = false;

			if(items.opcion !== undefined){
				$ctrl.params = {
					anexo_nombre:items.opcion.anexo_nombre,
					anexo_numero:items.opcion.anexo_numero,
					central_id: items.opcion.central_id,
					empleado:"off",
					id: items.opcion.id,
					opcion_nombre:items.opcion.opcion_nombre,
					opcion_numero:items.opcion.opcion_numero,
					redireccion: items.opcion.redireccion
				};
			} else {
				$ctrl.params = {
					anexo_nombre:null,
					anexo_numero:null,
					central_id: vm.params.id,
					empleado:"off",
					opcion_nombre:null,
					opcion_numero:null,
					redireccion: null
				};
			}

			$ctrl.close = function(){
				$uibModalInstance.dismiss('cancel');
			};

			$ctrl.delete = function(){
				$ctrl.sending = true;
				mySrv.deleteOption({'id':$ctrl.params.id,'central_id':$ctrl.params.central_id}).then(function(response){
					toastr.success(response.data.message, 'Éxito');
					$ctrl.close();
					loadData();
				}).catch(function(error){
					toastr.error(error.data, 'Error en la opción');
				}).finally(function(){
					$ctrl.sending = false;
				});
			};

			$ctrl.save = function(){
				$ctrl.sending = true;
				mySrv.saveOption($ctrl.params).then(function(response){
					toastr.success(response.data.message, 'Éxito');
					$ctrl.close();
					loadData();
				}).catch(function(error){
					toastr.error(error.data, 'Error en la opción');
				}).finally(function(){
					$ctrl.sending = false;
				});
			};
		}

		function loadData(){
			vm.loading = true;
			mySrv.getByCompany(params.empresaID).then(function(response){
				empresa.central = angular.copy(response.data);
				vm.row = angular.copy(response.data);
				vm.params = angular.copy(response.data);
				vm.row.empresa_id = params.empresaID;
			}).catch(function(response){
				toastr.error(response.data, 'Error al obtener info. de la Central');
				vm.error = true;
			}).finally(function(){
				vm.loading = false;
			});
		}

		function openModal(action, row){
			var e = false;

			if(action === 'create' && row !== undefined)
				e = true;

			uimodal.open({
				animation: true,
				templateUrl: '/templates/modals/central/' + action + '.html',
				controller: ['$uibModalInstance','items', modalController],
				controllerAs: '$ctrl',
				resolve: {
					items: function(){
						return {edit:e, opcion: row};
					}
				}
			}).result.then(function(){}, function(){});
		}

		function saveCdr(){
			mySrv.saveCdr({empresa_id: vm.row.empresa_id, cdr: vm.params.cdr}).then(function(response){
				toastr.success(response.data.message,'Éxito');
				vm.row.cdr = vm.params.cdr;
				enableCdrEdit();
			}).catch(function(response){
				toastr.error(response.data, 'Error al cambiar CDR');
			});
		}

		function saveConfig(){
			vm.saving = true;
			mySrv.saveConfig({
				id: vm.params.id,
				cancion: vm.params.cancion,
				numero: vm.params.numero,
				texto: vm.params.texto,
				empresa_id: vm.row.empresa_id,
			}).then(function(response){
				toastr.success(response.data.message, 'Éxito');

				if(!response.data.edit){
					vm.row.id = response.data.id;
					vm.params.id = response.data.id;
				}

				vm.row.cancion = vm.params.cancion;
				vm.row.numero = vm.params.numero;
				vm.row.texto = vm.params.texto;
				enableConfigEdit();
			}).catch(function(error){
				toastr.error(error.data, 'Error al guardar configuración');
			}).finally(function(){
				vm.saving = false;
			});
		}
	}
});