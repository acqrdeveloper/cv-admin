define(['app','roles'], function(app, roles){
	app.controller('OficinaCtrl', controller);
	controller.$inject = ['OficinaSrv','$uibModal','toastr','ListSrv'];
	function controller(mySrv, modal, toastr, listSrv){
		var vm = this;

		var auxs = {
			searching: false,
			locales: [{id:0,nombre:'Todos los locales'}].concat( window.iVariables.locales ),
			modelos: [{id:0,nombre:'Todos los tipos'}].concat( window.iVariables.modelos )
		};

		var params = {
			estado: 'A',
			local_id: 0,
			modelo_id: 0
		};

		function banController(instance, items){
		 	var ctrl = this;

		 	ctrl.close = function(){
		 		instance.dismiss('close');
		 	};

		 	ctrl.aux = {
		 		days: [false,false,false,false,false,false,false],
		 		data: [],
		 		day_name: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'],
		 		searching: false,
		 		times1: listSrv.getTimes(),
            	times2: listSrv.getTimes()
		 	};

		 	ctrl.params = {
		 		hini: '09:00:00',
		 		hfin: '10:00:00',
		 		oficina_id: items.oficina.id,
		 		dia: [],
		 		empresa_id: 0
		 	};

		 	ctrl.delete = function(r){
		 		mySrv.deleteBan(r).then(function(r){
		 			ctrl.get();
		 			toastr.success('Regla eliminada');
		 		}).catch(function(e){
		 			toastr.error(e.data.error);
		 		});
		 	};

		 	ctrl.get = function(){
		 		ctrl.aux.searching = true;
		 		ctrl.aux.data = [];
		 		mySrv.getBan(items.oficina.id).then(function(r){
		 			if(r.data.rows.length>0){
		 				r.data.rows.forEach(function(i){
		 					ctrl.aux.data.push({
		 						hini: i.hini,
		 						hfin: i.hfin,
		 						dia_nombre: i.dia.split(',').map(function(x){
		 							return ctrl.aux.day_name[x];
		 						}).join(','),
		 						dia: i.dia,
		 						empresa_id: i.empresa_id,
		 						oficina_id: i.oficina_id
		 					});
		 				});
		 			} else {
		 				ctrl.aux.data = [];
		 			}

		 			console.log(ctrl.aux.data);
		 		}).catch(function(e){
		 			toastr.error('Hubo un error al obtener el listado de reglas de bloqueo');
		 		}).finally(function(){
			 		ctrl.aux.searching = false;
		 		});
		 	};

		 	ctrl.saveBan = function(){
		 		ctrl.params.dia = [];
		 		ctrl.aux.days.forEach(function(item){
		 			if(item!==false)
		 				ctrl.params.dia.push(item);
		 		});
		 		ctrl.params.dia = ctrl.params.dia.join(',');
		 		mySrv.ban(ctrl.params).then(function(r){
		 			if((r.data.load*1) === 1){
			 			toastr.success('Regla creada');
			 			ctrl.get();	
		 			} else {
		 				toastr.error("La regla ya fue agregada o existe un cruce con otra regla.");
		 			}
		 		}).catch(function(e){
		 			toastr.error(e.data.error);
		 		});
		 	};

		 	ctrl.get();
		}

		function modalController(instance, item){
			var ctrl = this;
			ctrl.locales = angular.copy( window.iVariables.locales );
			ctrl.modelos = angular.copy( window.iVariables.modelos );
			ctrl.params = {
				local_id: ctrl.locales[0].id,
				modelo_id: ctrl.modelos[0].id
			};

			ctrl.close = function(){
				instance.dismiss('close');
			};

			ctrl.send = function(){
				mySrv.send( angular.copy(ctrl.params) ).then(function(r){
					toastr.success(r.data.message, 'Éxito');
					ctrl.close();
					search();
				}).catch(function(error){
					toastr.error(error.data.message, 'Error');
				});
			};

			ctrl.updateStatus = function(){
				mySrv.updateStatus({id: ctrl.params.id, estado: ctrl.params.estado}).then(function(r){
					toastr.success(r.data.message, 'Éxito');
					ctrl.close();
					search();
				}).catch(function(error){
					toastr.error(error.data.message, 'Error');
				});
			};

			// item populate
			if(item !== undefined){
				if(item.item !== undefined){
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
                templateUrl: '/templates/modals/oficina/' + action + '.html',
                controller: ['$uibModalInstance', 'items', modalController],
                controllerAs: 'ctrl',
                resolve: {
                    items: function(){
                        return {'item': angular.copy(item), 'edit': e};
                    }
                }
            }).result.then(function(){}, function(){});
		}

		function openBan(oficina){
            modal.open({
                animation: true,
                templateUrl: '/templates/modals/oficina/ban.html',
                controller: ['$uibModalInstance', 'items', banController],
                controllerAs: 'ctrl',
                resolve: {
                    items: function(){
                        return {'oficina': angular.copy(oficina)};
                    }
                }
            }).result.then(function(){}, function(){});
		}

		function search(){
			auxs.searching = true;
			mySrv.search( angular.copy(params) ).then(function(r){
				vm.data = r.data;
			}).catch().finally(function(){
				auxs.searching = false;
			});
		}

		search();

		angular.extend(vm, {
			'auxs': auxs,
			'data': [],
			'openModal': openModal,
			'openBan': openBan,
			'params': params,
			'search': search,
			'roles': roles
		});
	}
});