define(['app','roles'], function(app, roles){
	app.controller('DetInfoCtrl', controller);
	controller.$inject = ['$filter','$http','$stateParams','$uibModal','toastr'];
	function controller($filter,$http,params, uimodal, toastr){
		var vm = this;
		vm.btnEdit = 'Editar';
		vm.isEdit = false;
		vm.info = angular.copy(empresa.basic);
		vm.roles = roles;
		vm.tmp = {};
		vm.agent = {
			openModal: function(action, row){
				var e = false;

				if(action === 'create' && row !== undefined)
					e = true;

				uimodal.open({
					animation: true,
					templateUrl: '/templates/modals/representante/' + action + '.html',
					controller: ['$uibModalInstance','RepresentanteSrv','items',AgentCtrl],
					controllerAs: '$ctrl',
					resolve: {
						items: function(){
							return {edit:e, data: angular.copy(row)};
						}
					}
				}).result.then(function(){}, function(){});
			}
		};

		vm.employee = {
			openModal: function(action, row){
				var e = false;

				if(action === 'create' && row !== undefined)
					e = true;

				uimodal.open({
					animation: true,
					templateUrl: '/templates/modals/empleado/' + action + '.html',
					controller: ['$uibModalInstance','EmpleadoSrv','items',employeeCtrl],
					controllerAs: '$ctrl',
					resolve: {
						items: function(){
							return {edit:e, data: angular.copy(row)};
						}
					}
				}).result.then(function(){}, function(){});
			}
		};

		vm.edit = function(){
			vm.isEdit = 1;
			vm.tmp = angular.copy(vm.info);
		};

		vm.cancelEdit = function(){
			delete vm.isEdit;
			vm.info = angular.copy(vm.tmp);
		};

		vm.update = function(){
			//console.log( vm.info );
			vm.isUpdating = 1;
            $http({
                url: '/empresa/'+vm.info.id+'/info',
                method: 'PUT',
                params:vm.info
            }).then(function(response){
                if( response.data.load ){
                	empresa.basic = angular.copy( vm.info );
                    toastr.success("Email Enviado Exitosamente", 'Éxito');
					delete vm.isEdit;
                }/**/
            }).catch(function(error){
                toastr.error(error.data.message, 'Error al actualizar');
            }).finally(function(){
                delete vm.isUpdating;
            });
		};

		vm.openCrediential = function(){
			uimodal.open({
				animation: true,
				templateUrl: '/templates/modals/empresa/credencial.html',
				controller: ['$uibModalInstance', credentialCtrl],
				controllerAs: 'ctrl',
			}).result.then(function(){}, function(){});
		};

		function AgentCtrl(instance,mySrv,items){
			var $ctrl = this;
			$ctrl.sending = false;
			$ctrl.params = {};

			if(items !== undefined){
				$ctrl.params = items.data;
			}

			$ctrl.close = function(){
				instance.dismiss('cancel');
			};
			$ctrl.delete = function(){
				$ctrl.sending = true;
				mySrv.delete(empresa.basic.id, $ctrl.params.id).then(function(r){
					vm.info.representantes = r.data.agents;
					$ctrl.close();
					toastr.success(r.data.message,'Éxito');
				}).catch(function(e){
					toastr.error(e.data.message,'Error al eliminar empleado');
				}).finally(function(){
					$ctrl.sending = false;
				});
			};
			$ctrl.save = function(){
				$ctrl.sending = true;
				mySrv.send(empresa.basic.id, angular.copy($ctrl.params)).then(function(r){
					vm.info.representantes = r.data.agents;
					$ctrl.close();
					toastr.success(r.data.message,'Éxito');
				}).catch(function(e){
					toastr.error(e.data.message,'Error al guardar');
				}).finally(function(){
					$ctrl.sending = false;
				});
			};
			$ctrl.setLogin = function(){
				$ctrl.sending = true;
				mySrv.setLogin(empresa.basic.id, $ctrl.params.id).then(function(r){
					vm.info.representantes = r.data.agents;
					$ctrl.close();
					toastr.success(r.data.message,'Éxito');
				}).catch(function(e){
					toastr.error(e.data.message,'Error');
				}).finally(function(){
					$ctrl.sending = false;
				});
			};
		}

		function employeeCtrl(instance,mySrv,items){
			var $ctrl = this;
			$ctrl.sending = false;
			$ctrl.params = {};

			if(items !== undefined){
				$ctrl.params = items.data;
			}

			$ctrl.close = function(){
				instance.dismiss('cancel');
			};
			$ctrl.delete = function(){
				$ctrl.sending = true;
				mySrv.delete(empresa.basic.id, $ctrl.params.id).then(function(r){
					vm.info.empleados = r.data.employees;
					$ctrl.close();
					toastr.success(r.data.message,'Éxito');
				}).catch(function(e){
					toastr.error(e.data.message,'Error al eliminar empleado');
				}).finally(function(){
					$ctrl.sending = false;
				});
			};
			$ctrl.save = function(){
				$ctrl.sending = true;
				mySrv.send(empresa.basic.id, $ctrl.params).then(function(r){
					vm.info.empleados = r.data.employees;
					$ctrl.close();
					toastr.success(r.data.message,'Éxito');
				}).catch(function(e){
					toastr.error(e.data.message,'Error al guardar');
				}).finally(function(){
					$ctrl.sending = false;
				});
			};
		}

		function credentialCtrl(instance){
			var ctrl = this;

			var correo = empresa.basic.representantes[0].correo;
			var cc = "";

			function close(){
				instance.dismiss('close');
			}

			function send(){
				$http({url: '/empresa/' + vm.info.id + '/send_credentials', method:'GET', params: {cc: ctrl.cc}}).then(function(r){
					toastr.success(r.data.message);
					close();
				}).catch(function(e){
					toastr.error(e.data.message);
				});
			}

			angular.extend(ctrl, {
				correo: correo,
				cc: cc,
				close: close,
				send: send
			});
		}
	}
});