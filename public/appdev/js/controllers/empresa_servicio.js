define(['app','roles'], function(app, roles){
	app.controller('EmpresaServicioCtrl', controller);
	controller.$inject = ['$scope','$filter','$uibModal','EmpServicioSrv','toastr','ListSrv'];
	function controller($scope,$filter,modal, mySrv, toastr, ListSrv){

		var vm = this;

		var basic = empresa.basic;

		var now = new Date();

		var auxs = {
            years: ListSrv.years(),
            months: ListSrv.months(),
			comprobantes: ['FACTURA','BOLETA','PROVICIONAL'],
			ciclos: ['QUINCENAL','MENSUAL'],
			srv: {
				"cantidad_copias": 0,
				"cantidad_impresiones": 0,
				"horas_reunion": 0, 
				"horas_privada": 0,
				"horas_capacitacion": 0
			},
			tipo_servicio : {
				'P': 'Plan',
				'D': 'Descuento',
				'E': 'Extra',
				'C': 'Combo'
			},
			srvHistory: {
				anio: now.getFullYear() * 1,
				mes: now.getMonth() + 1
			}
		};

		auxs.months.splice(0,1);

		if( empresa.basic.preferencia_facturacion != "MENSUAL" ){
			if( ( now.getDate() * 1 ) < 15 ){
			  var d = new Date();
			  d.setMonth(now.getMonth() - 1);
			  auxs.srvHistory.anio = now.getFullYear() * 1;
			  auxs.srvHistory.mes = ( ( now.getMonth() * 1 ) + 1 );
			}
		}

		var params = {
			preferencia_comprobante: angular.copy(basic.preferencia_comprobante),
			preferencia_facturacion: angular.copy(basic.preferencia_facturacion),
		};

		angular.extend(vm, {
			'auxs': auxs,
			'basic': basic,
			'params': params,
			'openModal': openModal,
			'openModalPlan': openModalPlan,
			'openModalResource': openModalResource,
			'openModalServicio': openModalServicio,
			'roles': roles,
			'loadHours': loadHours
		});

		// init
		loadHours();
		loadServices();

		function loadHours(){
			mySrv.getService({'empresa_id': basic.id, 'anio': vm.auxs.srvHistory.anio, 'mes': vm.auxs.srvHistory.mes}).then(function(r){
				if((r.data.total*1) > 0){
					vm.auxs.srv.cantidad_copias = r.data.rows[0].cantidad_copias;
					vm.auxs.srv.cantidad_impresiones = r.data.rows[0].cantidad_impresiones;
					vm.auxs.srv.horas_reunion = r.data.rows[0].horas_reunion; 
					vm.auxs.srv.horas_privada = r.data.rows[0].horas_privada;
					vm.auxs.srv.horas_capacitacion = r.data.rows[0].horas_capacitacion;
				} else {
					vm.auxs.srv.cantidad_copias = 0;
					vm.auxs.srv.cantidad_impresiones = 0;
					vm.auxs.srv.horas_reunion = 0; 
					vm.auxs.srv.horas_privada = 0;
					vm.auxs.srv.horas_capacitacion = 0;
				}
			});
		}

		function loadServices(){
			vm.loadingServices = true;
			mySrv.getServices({empresa_id: empresa.id}).then(function(r){
				vm.recursos_extras = r.data;
			}).finally(function(){
				vm.loadingServices = false;
			});
		}

		function modalCtrl(inst, items){
			var ctrl = this;
			ctrl.auxs = {};
			ctrl.params = {
				empresa_id: basic.id
			};
			switch(items.action){
				case 'ciclo':
					ctrl.auxs.preferencia_facturacion = basic.preferencia_facturacion;
                	ctrl.params.preferencia_facturacion = params.preferencia_facturacion;
					break;
				case 'comprobante':
					ctrl.auxs.preferencia_comprobante = basic.preferencia_comprobante;
                	ctrl.params.preferencia_comprobante = params.preferencia_comprobante;
					break;
				case 'schedule':
					ctrl.auxs.dateOpts = {minDate: now };
					ctrl.auxs.fecha_eliminacion = basic.fecha_eliminacion;
					ctrl.auxs.is_open = false;
					ctrl.params.fecha_eliminacion = now;
					if(basic.fecha_eliminacion !== null){
						ctrl.params.delete = 1;
					}
					break;
			}
			ctrl.changeCiclo = function(){
				mySrv.changeCiclo(ctrl.params).then(function(r){
					toastr.success(r.data.message);
					empresa.basic.preferencia_facturacion = angular.copy(params.preferencia_facturacion);
					ctrl.close();
				}).catch(function(e){
					toastr.error(r.data.message);
				}).finally(function(){});
			};
			ctrl.changeComprobante = function(){
				mySrv.changeComprobante(ctrl.params).then(function(r){
					toastr.success(r.data.message);
					empresa.basic.preferencia_comprobante = angular.copy(params.preferencia_comprobante);
					ctrl.close();
				}).catch(function(e){
					toastr.error(e.data.message);
				}).finally(function(){});
			};
			ctrl.close = function(){
				switch(items.action){
					case 'ciclo':
						params.preferencia_facturacion = basic.preferencia_facturacion;
						break;
					case 'comprobante':
						params.preferencia_comprobante = basic.preferencia_comprobante;
						break;
				}
				inst.dismiss('close');
			};
			ctrl.openCalendar = function(){
				ctrl.auxs.is_open = true;
			};
			ctrl.scheduleDelete = function(){
				if(ctrl.params.fecha_eliminacion !== null){
					ctrl.params.fecha_eliminacion = $filter('date')(ctrl.params.fecha_eliminacion,'yyyy-MM-dd','America/Lima');
				}
				mySrv.scheduleDelete(angular.copy(ctrl.params)).then(function(r){
					toastr.success(r.data.message);
					ctrl.close();
					if(ctrl.params.fecha_eliminacion!== undefined && ctrl.params.fecha_eliminacion !== null && ctrl.params.delete === undefined){
						vm.basic.fecha_eliminacion = angular.copy(ctrl.params.fecha_eliminacion);
					} else {
						vm.basic.fecha_eliminacion = null;
					}
				}).catch(function(e){
					toastr.error(e.data.message);
				}).finally(function(){});
			};
		}

		function modalResourceCtrl(inst, items){
			var ctrl = this;

			ctrl.params = {
				anio: vm.auxs.srvHistory.anio,
				mes: vm.auxs.srvHistory.mes,
				empresa_id: empresa.id,
				facturar: 'on',
				next: 'off',
				observacion: '',
				recurso: items.action,
				tipo: (items.type*1)
			};

			ctrl.addResource = function(){
				ctrl.sending = true;
				mySrv.addResource(ctrl.params).then(function(r){
					console.log(r);
					var datos = r.data;
					vm.auxs.srv.cantidad_copias = datos.recurso.rows[0].cantidad_copias;
					vm.auxs.srv.cantidad_impresiones = datos.recurso.rows[0].cantidad_impresiones;
					vm.auxs.srv.horas_reunion = datos.recurso.rows[0].horas_reunion; 
					vm.auxs.srv.horas_privada = datos.recurso.rows[0].horas_privada;
					vm.auxs.srv.horas_capacitacion = datos.recurso.rows[0].horas_capacitacion;
					toastr.success(ctrl.title + " " + (ctrl.params.tipo===1?"agregado":"reducido") );
					ctrl.close();
				}).catch(function(e){
					toastr.error(e.data.error);
				}).finally(function(){
					ctrl.sending = false;
				});
			};

			ctrl.close = function(){
				inst.dismiss('close');
			};

			// set title
			switch(items.action){
				case 'cantidad_copias':
					ctrl.title = 'Cantidad de copias';
					ctrl.params.next = 'on';
					break;
				case 'cantidad_impresiones':
					ctrl.title = 'Cantidad de impresiones';
					ctrl.params.next = 'on';
					break;
				case 'horas_reunion':
					ctrl.title = 'Horas de reunión';
					break;
				case 'horas_privada':
					ctrl.title = 'Horas privadas';
					break;
				case 'horas_capacitacion':
					ctrl.title = 'horas de capacitación';
					break;
			}
		}

		function modalServiceCtrl(inst, items){
			var ctrl = this;
			ctrl.sending = false;

			if(items !== undefined && items.recurso !== undefined){
				ctrl.servicio = items.recurso;
			}

			ctrl.auxs = {
				from_emp_service: 1,
				planes: window.iVariables.planes,
				selected_plan: window.iVariables.planes[0]
			};

			ctrl.params = {
				empresa_id: angular.copy(empresa.id),
				tipo: 'E',
				monto: 1,
				mes: 1,
				concepto: 'Servicio Extra'
			};

			$scope.$watch(function(){
				return ctrl.auxs.selected_plan;
			}, function(newVal, oldVal){
				if(newVal !== oldVal && newVal.id > 0){
					ctrl.params.monto = newVal.precio;
					ctrl.params.servicio_extra_id = newVal.id;
					ctrl.params.concepto = 'Combo ' + newVal.nombre;
				}
			});

			ctrl.close = function(){
				inst.dismiss('close');
			};
			ctrl.add = function(){
				ctrl.sending = true;

				if(ctrl.params.tipo !== 'P' && ctrl.params.servicio_extra_id !== undefined){
					delete ctrl.params.servicio_extra_id;
				}

				mySrv.addService(ctrl.params).then(function(r){
					toastr.success("Servicio agreagdo");
					loadServices();
					ctrl.close();
				}).catch(function(e){
					toastr.error(e.data.message);
				}).finally(function(){
					ctrl.sending = false;
				});
			};
			ctrl.delete = function(){
				ctrl.sending = true;
				mySrv.deleteService({empresa_id: empresa.id, servicio_id: ctrl.servicio.id}).then(function(r){
					toastr.success("Servicio eliminado");
					loadServices();
					ctrl.close();
				}).catch(function(e){
					toastr.error(e.data.message);
				}).finally(function(){
					ctrl.sending = false;
				});
			};
		}

		function modelPlanCtrl(inst, items) {
			var ctrl = this;
			var contrato = angular.copy(window.empresa.basic.contrato);
			ctrl.dateOptions = {
				minDate: new Date()
			};
			ctrl.auxs = {
				open1: false,
				open2: false
			};

			ctrl.plan = {};

			ctrl.openCalendar1 = function(){
				ctrl.auxs.open1 = true;
			};
			ctrl.openCalendar2 = function(){
				ctrl.auxs.open2 = true;
			};

			ctrl.planes =  angular.copy(window.iVariables.planes);

			ctrl.params = {
				empresa_id: angular.copy(empresa.id),
				fecha_inicio: new Date(contrato.fecha_inicio + " 00:00:00"),
				fecha_fin: new Date(contrato.fecha_fin + " 00:00:00"),
				plan_src: angular.copy(window.empresa.basic.plan_id)
			};

			if( window.empresa.basic.plan !== undefined ){
				ctrl.plan = angular.copy(window.empresa.basic.plan);
			}

			ctrl.params.plan_dst = ctrl.plan.id || ctrl.planes[0].id;

			ctrl.sending = false;

			if(items.renew == 1){

				var now = new Date();
				ctrl.params.renew = 1;
				ctrl.params.fecha_inicio = angular.copy(now);
				now.setMonth( now.getMonth() + 7);
				ctrl.params.fecha_fin = now;
			}

			ctrl.close = function(){
				inst.dismiss('close');
			};

			ctrl.send = function(){
				ctrl.sending = true;
				var params = angular.copy(ctrl.params);
				params.fecha_inicio = $filter('date')(params.fecha_inicio, 'yyyy-MM-dd', 'America/Lima');
				params.fecha_fin = $filter('date')(params.fecha_fin, 'yyyy-MM-dd', 'America/Lima');
				mySrv.editContract(params).then(function(r){
					if(r.data.load){
						toastr.success("Contrato editado");
						empresa.basic.contrato.fecha_inicio = params.fecha_inicio;
						empresa.basic.contrato.fecha_fin = params.fecha_fin;
						if(params.preferencia_estado !== undefined && params.preferencia_estado === "P"){
							empresa.basic.preferencia_estado = params.preferencia_estado;
						}
						// Cambiar plan
						for(var i=0;i<window.iVariables.planes.length;i++){
							if(window.iVariables.planes[i].id === params.plan_dst){
								empresa.basic.plan = angular.copy(window.iVariables.planes[i]);
								break;
							}
						}
						loadServices();
						ctrl.close();
						location.reload();
					} else {
						toastr.error(r.data.message);
					}
				}).catch(function(e){
					toastr.success(e.data.message);
				}).finally(function(){
					ctrl.sending = false;
				});
			};
		}

		function openModal(action){
			modal.open({
				animation: true,
				templateUrl: '/templates/modals/servicio/'+action+'.html',
				controller: ['$uibModalInstance','items', modalCtrl],
				controllerAs: 'ctrl',
				resolve: {
					items: function(){
						return {'action':action};
					}
				}
			}).result.then(function(){}, function(){});
		}

		function openModalPlan(renew){

			var r = renew || 0;

			modal.open({
				animation: true,
				templateUrl: '/templates/modals/servicio/plan.html',
				controller: ['$uibModalInstance','items', modelPlanCtrl],
				controllerAs: 'ctrl',
				resolve: {
					items: function(){
						return {'renew':r};
					}
				}
			}).result.then(function(){}, function(){});
		}

		function openModalResource(action, type){
			modal.open({
				animation: true,
				templateUrl: '/templates/modals/servicio/recurso.html',
				controller: ['$uibModalInstance','items', modalResourceCtrl],
				controllerAs: 'ctrl',
				resolve: {
					items: function(){
						return {'action':action, 'type': type};
					}
				}
			}).result.then(function(){}, function(){});
		}

		function openModalServicio(action, recurso){
			modal.open({
				animation: true,
				templateUrl: '/templates/modals/servicio/'+ action+'-servicio.html',
				controller: ['$uibModalInstance','items', modalServiceCtrl],
				controllerAs: 'ctrl',
				resolve: {
					items: function(){
						return {'action': action, 'recurso': angular.copy(recurso)};
					}
				}
			}).result.then(function(){}, function(){});
		}
	}
});