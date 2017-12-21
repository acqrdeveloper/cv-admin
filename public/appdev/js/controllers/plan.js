define(['app','roles'], function(app, roles){
	app.controller('PlanCtrl', controller);
	controller.$inject = ['PlanSrv','$uibModal','toastr'];

	function controller(mySrv, modal, toastr){
		var vm = this;
		var auxs = {
			'estado': 'A',
			'searching': false
		};

		function modalController(instance, items){
			var ctrl = this;

			if(items.edit && items.params !== undefined){
				ctrl.params = items.params;
			}

			ctrl.sending = false;

			ctrl.close = function(){
				instance.dismiss('close');
			};

			ctrl.send = function(){
				ctrl.sending = true;
				mySrv.send(ctrl.params).then(function(r){
					toastr.success(r.data.message, 'Éxito');
					ctrl.close();
					search();
				}).catch(function(r){
					toastr.error(r.data.message, 'Error');
				}).finally(function(){
					ctrl.sending = false;
				});
			};

			ctrl.updateStatus = function(){
				ctrl.sending = true;
				mySrv.updateStatus({id:ctrl.params.id, estado: ctrl.params.estado}).then(function(r){
					toastr.success(r.data.message, 'Éxito');
					ctrl.close();
					search();
				}).catch(function(r){
					toastr.error(r.data.message, 'Error');
				}).finally(function(){
					ctrl.sending = false;
				});
			};
		}

		function openModal(action, item){

			var e = false;

			if(item !== undefined){
				e = true;
			}

            modal.open({
                animation: true,
                templateUrl: '/templates/modals/plan/' + action + '.html',
                controller: ['$uibModalInstance', 'items', modalController],
                controllerAs: 'ctrl',
                //size:'lg',
                resolve: {
                    items: function(){
                        return {params: angular.copy(item), edit: e};
                    }
                }
            }).result.then(function(){}, function(){});
		}

		function search(){
			auxs.searching = true;
			mySrv.search({'estado': auxs.estado}).then(function(r){
				vm.data = r.data;
			}).finally(function(){
				auxs.searching = false;
			});
		}

		angular.extend(vm, {
			'auxs': auxs,
			'data': [],
			'openModal': openModal,
			'search': search,
			'roles': roles
		});
	}
});