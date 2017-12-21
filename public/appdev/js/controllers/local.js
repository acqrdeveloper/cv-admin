define(['app','roles'], function(app, roles){
	app.controller('LocalCtrl', controller);
	controller.$inject = ['LocalSrv','$uibModal','toastr'];
	function controller(mySrv, modal, toastr){
		var vm = this;
		var auxs = {
			searching: false
		};

		function modalController(instance, item){
			var ctrl = this;
			ctrl.ciudades = angular.copy( window.iVariables.ciudades );
			ctrl.params = {
				distrito: ctrl.ciudades[0].id,
				estado: 'A'
			};

			ctrl.close = function(){
				instance.dismiss('close');
			};

			ctrl.send = function(){
				mySrv.send( angular.copy(ctrl.params) ).then(function(r){
					toastr.success(r.data.message, 'Éxito');
					ctrl.close();
					searchLocales();
				}).catch(function(error){
					toastr.error(error.data.message, 'Error');
				});
			};

			// item populate
			if(item !== undefined){
				if(item.item !== undefined && item.edit){
					ctrl.params = item.item;
				}
			}
		}

		function openModal(action, item){

			var e = false;

			if(action === 'create' && item !== undefined){
				e = true;
			}

            modal.open({
                animation: true,
                templateUrl: '/templates/modals/local/' + action + '.html',
                controller: ['$uibModalInstance', 'items', modalController],
                controllerAs: 'ctrl',
                resolve: {
                    items: function(){
                        return {'item': angular.copy(item), 'edit': e};
                    }
                }
            }).result.then(function(){}, function(){});
		}

		function searchLocales(){
			auxs.searching = true;
			mySrv.search().then(function(r){
				vm.dataLocales = r.data;
			}).catch().finally(function(){
				auxs.searching = false;
			});
		}

		searchLocales();

		angular.extend(vm, {
			'auxs': auxs,
			'dataLocales': [],
			'openModal': openModal,
			'roles': roles
		});
	}
});