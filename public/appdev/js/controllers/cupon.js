define(['app','roles'], function(app, roles){
	app.controller('CuponCtrl', controller);
	controller.$inject = ['$filter','$uibModal', 'CuponSrv', 'EmpresaSrv','ListSrv','toastr'];

	function controller($filter,uimodal, mySrv, EmpresaSrv, listSrv, toastr){
		var vm  = this;
		var now = new Date();

		var aux = {
			months: listSrv.months(),
			searching: false,
			table: {
				total: 0,
				rows: []
			},
			years: listSrv.years()
		};

		var params = {
			aniouso: now.getFullYear(),
			usado: "-",
			limite: 25,
			mesuso: now.getMonth()+1,
			pagina: 1
		};

		var fn = {
            clearCompanies: function(){
                aux.selected_company = '';
                params.empresa_id = 0;
            },
            delete: function(id){
            	if(confirm('¿Desea eliminar este cupón?')){
            		mySrv.delete(id).then(function(r){
            			toastr.success(r.data.message);
            			fn.filterSearch();
            		}).catch(function(e){
            			toastr.success(e.data.error);
            		});
            	}

            	return false;
            },
			filterCompanies: function(value, onlyActive){
                if(value === '' || value.length < 3){
                    return [];
                } else{
                    var params = {view:'minimal',empresa_nombre:value};
                    if(onlyActive === 1){
                        params.estado = 'A,X';
                    }
                    return EmpresaSrv.search(params);
                }
			},
			filterSearch: function(){
				params.page = 1;
				fn.search();
			},
			openForm: function(){
				uimodal.open({
					animation: true,
					templateUrl: '/templates/modals/cupon/create.html',
					controller: ['$uibModalInstance', modalController],
					controllerAs: 'ctrl'
				}).result.then(function(){}, function(){});
			},
			search: function(){
				aux.searching = true;
				mySrv.search(params).then(function(r){
					vm.aux.table = r.data;
				}).finally(function(){
					vm.aux.searching = false;
				});
			}
		};

		angular.extend(vm, {
			'aux': aux,
			'fn': fn,
			'params': params,
			'roles': roles
		});

		function modalController(instance){
			var ctrl = this;

			ctrl.sending = false;

			ctrl.params = {
				finicio: new Date(),
				ffin: new Date(),
				monto: 40
			};

			ctrl.close = function(){
				instance.dismiss('close');
			};

			ctrl.openCalendar1 = function(){
				ctrl.cal1 = true;
			};

			ctrl.openCalendar2 = function(){
				ctrl.cal2 = true;
			};

			ctrl.send = function(){
				ctrl.sending = true;

				var p = angular.copy(ctrl.params);

				p.finicio = $filter('date')(p.finicio, 'yyyy-MM-dd', 'America/Lima');
				p.ffin = $filter('date')(p.ffin, 'yyyy-MM-dd', 'America/Lima');

				mySrv.create(p).then(function(r){
					toastr.success(r.data.message);
					fn.filterSearch();
					ctrl.close();
				}).catch(function(e){
					toastr.error(e.data.error);
				}).finally(function(){
					ctrl.sending = false;
				});
			};
		}
	}
});