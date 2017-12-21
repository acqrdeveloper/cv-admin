define(['app','roles'], function(app, roles){
	app.controller('EmpPBXCtrl', controller);

	controller.$inject = ['$stateParams','$uibModal','CentralSrv','toastr'];

	function controller(parms, uimodal, mySrv, toastr){
		var vm = this;
		var empresaID = parms.empresaID;
		var aux = {
			data: [],
			loading: false,
			mainView: true,
			saving: false
		};
		var params = {};
		var fn = {
			clearSelection: function(){
				params.cdr = '';
			},
			delete: function(pbx, idx){
				if(confirm('¿Desea eliminar la central ' + pbx.cdr + '(' + pbx.numero + ') ?')){
					mySrv.detelePBX(pbx.id).then(function(r){
						toastr.success(r.data.message);
						vm.aux.data.splice(idx, 1);
					}).catch(function(e){
						toastr.error(e.data.error, 'Error al eliminar Central');
					}).finally(function(){
					});
				}

				return false;
			},
			filterCDRs: function(cdr){
				return mySrv.getCDRs(cdr);
			},
			get: function(){
				aux.loading = true;
				mySrv.getByCompany(empresaID).then(function(r){
					vm.aux.data = r.data;
				}).finally(function(){
					vm.aux.loading = false;
				});
			},
			openForm: function(pbx){
				aux.mainView = !aux.mainView;
				params.empresa_id = empresaID;
				params.cdr = "";
				params.numero = "";
				params.texto = "";
				params.cancion = "";
				params.opciones = [];
				params.central_id = undefined;

				if(pbx !== undefined){
					params.cdr = pbx.cdr;
					params.numero = pbx.numero;
					params.texto = pbx.texto;
					params.cancion = pbx.cancion;
					params.central_id = pbx.central_id;
					try {
						params.opciones = JSON.parse(pbx.opciones);
					} catch (e) {
						params.opciones = [];
					}
				}
			},
			openModal: function(action, row, idx){
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
							return {edit:e, opcion: row, index: (idx===undefined?-1:idx)};
						}
					}
				}).result.then(function(){}, function(){});
			},
			savePBX: function(){
				aux.saving = true;
				var p = angular.copy(params);
				delete p.opciones;
				mySrv.savePBX(p).then(function(r){
					toastr.success(r.data.message);
					fn.get();
					// al momento de crear, central_id no existe!
					if(vm.params.central_id === undefined){
						vm.params.central_id = r.data.central.id;
					}
				}).catch(function(e){
					toastr.error(e.data.error);
				}).finally(function(){
					vm.aux.saving = false;
				});
			}
		};

		fn.get();

		angular.extend(vm, {
			aux: aux,
			fn: fn,
			params: params,
			roles: roles.getRolesByEntity("empresas")
		});

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
					anexo_nombre:'',
					anexo_numero:'',
					central_id: vm.params.central_id,
					empleado:"off",
					opcion_nombre:'',
					opcion_numero:'',
					redireccion: ''
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
					vm.params.opciones.splice(items.index,1);
					fn.get();
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
					if(!response.data.edit){
						vm.params.opciones.push(response.data.opcion);
					} else {
						vm.params.opciones[items.index] = response.data.opcion;
					}
					$ctrl.close();
					fn.get();
				}).catch(function(error){
					toastr.error(error.data, 'Error en la opción');
				}).finally(function(){
					$ctrl.sending = false;
				});
			};
		}
	}
});