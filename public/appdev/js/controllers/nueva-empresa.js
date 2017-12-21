define(['app','moment'], function(app,moment){
	app.controller('NuevaEmpresaCtrl', controller);
	controller.$inject = ['$filter', '$state', '$uibModal', 'EmpresaSrv', 'ListSrv', 'toastr'];
	function controller($filter, $state, modal, mySrv, ListSrv, toastr){
		var vm = this;
		var lists = {
			asesores: angular.copy(window.iVariables.asesores),
			ciclos: angular.copy(ListSrv.ciclos()),
			planes: angular.copy(window.iVariables.planes)
		};
		// splice/add
		lists.ciclos.splice(0,1);
		lists.asesores.unshift({id:0, nombre:'Sin Asesor'});
		lists.planes.unshift({id:0, nombre: 'Seleccione', precio: 0, promocion: 0});

		var auxs = {
			cal1: false,
			cal2: false,
			dateOption: {
				minDate: new Date(),
			},
			cw_cantidad: 1,
			details: [],
			searching: false,
			selected_asesor: lists.asesores[0],
			selected_plan: lists.planes[0]
		};
		var params = {
			asesor: '',
			carrera: 'N',
			contrato: {
				fecha_inicio: moment().toDate(),
				fecha_fin: moment().add(6, 'M').toDate()
			},
			convenio: 'N',
			convenio_duration: '1',
			empresa_nombre: '',
			empresa_direccion: '',
			empresa_rubro: '',
			empresa_ruc: '',
			nombre_comercial: '',
			preferencia_comprobante: 'FACTURA',
			preferencia_fiscal: 'NO',
			fac_nombre: '',
			fac_apellido: '',
			fac_domicilio: '',
			fac_email: '',
			fac_dni: '',
			fac_celular: '',
			fac_telefono: '',
			factura: {
				items: [],
				total: 0
			},
			plan_id: 0,
			preferencia_facturacion: 'QUINCENAL',
			preferencia_fiscal_nro_partida: '',
			promo: 'N',
			representante: {
				nombre: '',
				apellido: '',
				dni: '',
				correo: '',
				telefonos: '',
				domicilio: ''
			},
			servicios_extras: []
		};
		var fn = {
			applyAsesor: function(){
				if(auxs.selected_asesor.id>0 && auxs.selected_plan.promocion>0){
					params.promo = 'N';
					addItem(auxs.selected_plan.nombre, "P", auxs.selected_plan.precio);
				}
			},
			applyConvenio: function(){
				auxs.details = [];
				if(params.convenio === "S"){
					addItem("CONVENIO", "C", 0);
				} else {
					fn.applyPlan();
				}
			},

			applyPlan: function(){
				params.plan_id = auxs.selected_plan.id;

				var precio = (auxs.selected_plan.tipo === "CW")?auxs.selected_plan.precio  * auxs.cw_cantidad:auxs.selected_plan.precio;

				addItem(auxs.selected_plan.nombre, "P", precio);
				addItem("GARANTÍA", "G", precio);

				var idx = -1;

				if(auxs.selected_plan.tipo === "CW"){
					idx = $filter('findInArray')(auxs.details, 'tipo', 'P');
					auxs.details[idx].servicio_extra_id = auxs.cw_cantidad;				
				}

				if(params.promo === 'S'){

					idx = $filter('findInArray')(auxs.details, 'tipo', 'D');

					if( auxs.selected_plan.promocion === null ){
						params.promo = 'N';
						if(idx>=0){
							auxs.details.splice(idx, 1);
						}
					} else {
						if(idx>0){
							// se asume que promocion > 0
							var dscto = 0;

							if(auxs.selected_plan.tipo === "CW")
								dscto = auxs.selected_plan.promocion * auxs.cw_cantidad;
							else
								dscto = auxs.selected_plan.promocion;

							auxs.details[idx].precio = (precio - dscto) * -1;
							auxs.details[idx].fac_precio = angular.copy(auxs.details[idx].precio);
						}
					}
				}
			},
			applyPromocion: function(){
				// agregar
				var idx = $filter('findInArray')(auxs.details, 'tipo', 'D');

				if(params.promo === 'S'){

					if(idx>=0){
						toastr.error('No puede otorgar una promoción a esta empresa si ya cuenta con descuento.');
						return false;
					}

					var precio = (auxs.selected_plan.tipo === "CW")?auxs.selected_plan.precio  * auxs.cw_cantidad:auxs.selected_plan.precio;

					var dscto = 0;

					if(auxs.selected_plan.tipo === "CW")
						dscto = auxs.selected_plan.promocion * auxs.cw_cantidad;
					else
						dscto = auxs.selected_plan.promocion;

					auxs.details.push({
						'descripcion': 'Dscto. Promoción ' + auxs.selected_plan.promocion_mes + ' mes(es)',
						'fac_precio': (precio - dscto) * -1,
						'precio': (precio - dscto) * -1,
						'tipo': 'D',
						'mes': auxs.selected_plan.promocion_mes,
						'servicio_extra_id': 0
					});
				} else {
					if(idx>=0)
						auxs.details.splice(idx, 1);
				}
			},
			blockOffer: function(){
				if(params.convenio === 'S')
					return true;
				else if(auxs.selected_plan.promocion <= 0 || auxs.selected_asesor.id > 0)
					return true;
				return false;
			},
			create: function(){
				var p = angular.copy(params);
				// contrato
				var contrato = p.contrato;
				contrato.fecha_inicio = $filter('date')(contrato.fecha_inicio, 'yyyy-MM-dd', 'America/Lima');
				contrato.fecha_fin = $filter('date')(contrato.fecha_fin, 'yyyy-MM-dd', 'America/Lima');
				p.contrato = contrato;
				
				// asesor
				if(auxs.selected_asesor.id>0){
					p.asesor = auxs.selected_asesor.nombre;
				} else {
					p.asesor = "";
				}

				// details
				for(var i=0;i<auxs.details.length;i++){
					var item = auxs.details[i];

					var factura_item = {
						anio: params.contrato.fecha_inicio.getFullYear(),
						custom_id: 0,
						descripcion: item.descripcion,
						descripcion_sunat: "SERVICIO EXTRA",
						mes: params.contrato.fecha_inicio.getMonth() + 1,
						precio: item.fac_precio,
						tipo: item.tipo
					};

					if(item.tipo === "P" || item.tipo === "G"){
						if(item.tipo === "P"){
							factura_item.custom_id = auxs.selected_plan.id;
							factura_item.descripcion = auxs.selected_plan.nombre + " (Periodo " + factura_item.mes + "/" + factura_item.anio + ")";
							factura_item.descripcion_sunat = "SERVICIO EN OFICINAS VIRTUALES (Periodo " + factura_item.mes + "/" + factura_item.anio + ")";
						} else {
							factura_item.descripcion = "GARANTIA";
							factura_item.descripcion_sunat = "GARANTIA";
						}
					}

					p.factura.items.push(factura_item);

					if((['E','D','P','C']).indexOf(item.tipo)>=0){
						p.servicios_extras.push({
							concepto: ((item.tipo!=='P')?factura_item.descripcion:auxs.selected_plan.nombre),
							tipo: item.tipo,
							monto: ((item.tipo!=='P')?item.precio:item.fac_precio)*1,
							mes: ((item.tipo!=='P')?item.mes:-1),
							servicio_extra_id: (item.servicio_extra_id!==undefined?item.servicio_extra_id:0)
						});
					}
				}

				p.factura.total = $filter('sumByKey')(auxs.details,'fac_precio');

				//console.log(p);

				auxs.sending = true;

				mySrv.create(p).then(function(r){
					if(r.data.empresa.id !== undefined) {
						toastr.success('Empresa creada.');
						$state.go('empresa.info', {empresaID: r.data.empresa.id});						
					} else {
						toastr.error('Hubo un error al crear la empresa.');
					}
				}).catch(function(e){
					toastr.error(e.data.error);
				}).finally(function(){
					auxs.sending = false;
				});
			},
			checkRequirements: function(){
				return auxs.selected_plan.id<=0 || ([11,8]).indexOf(params.empresa_ruc.length) < 0 || ((['10','15','20']).indexOf(params.empresa_ruc.substr(0,2)) < 0 && params.empresa_ruc.length === 11);
			},
			openCalendar1: function(){
				auxs.cal1 = true;
			},
			openCalendar2: function(){
				auxs.cal2 = true;
			},
			openServiceModal: function(){
				modal.open({
						animation: true,
						templateUrl: '/templates/modals/servicio/add-servicio.html',
						controller: ['$uibModalInstance', serviceCtrl],
						controllerAs: 'ctrl'
				}).result.then(function(){}, function(){});
			},
			removeItem: function(idx){
				auxs.details.splice(idx, 1);

				if(params.promo == 'S')
					params.promo = 'N';
			},
			searchByName: function(name){
				auxs.searching = true;
				return mySrv.searchInSunatByName(name).then(function(r){
					auxs.searching = false;
					if(r.data.data !== undefined){
						var rs = [];
						for(var i = 0; i < 10; i++){
							if( r.data.data[i] !== undefined){
								rs[i] = r.data.data[i];
							} else {
								break;
							}
						}
						return rs;
					} else {
						return [];
					}
				}).catch(function(e){
					toastr.error(e.data.message);
				}).finally(function(){
					auxs.searching = false;
				});
			},
			searchByRuc: function(){
				var prefix = params.empresa_ruc.substr(0,2);
				if(params.empresa_ruc.length === 11 && (prefix === "10" || prefix === "15" || prefix === "20") ){
					auxs.searching = true;
					mySrv.searchInSunatByRuc(params.empresa_ruc).then(function(r){
						if(r.data.rsocial === "DATOS NO ENCONTRADOS")
							toastr.error(r.data.rsocial);
						else
							fillParams(r.data);
					}).finally(function(){
						auxs.searching = false;
					});
				} else {
					toastr.error('Debes ingresar un RUC correcto.');
				}
			},
			setCompany: function(){
				params.empresa_ruc = vm.auxs.empresa_selected.ruc;
				fn.searchByRuc();
			}
		};

		angular.extend(vm, {
			auxs: auxs,
			lists: lists,
			params: params,
			fn: fn
		});

		function addItem(descripcion, tipo, precio){
			if( (['P','G']).indexOf(tipo) >= 0){ // no existe plan y/o garantia
				var idx = $filter('findInArray')(auxs.details, 'tipo', tipo);
				switch(idx){
					case -2:
						toastr.error('Hay un error en el filtro findInArray');
						break;
					case -1:
						auxs.details.push({
							'descripcion': descripcion,
							'fac_precio': precio,
							'precio': precio,
							'tipo': tipo
						});
						break;
					default:
						auxs.details[idx] = {'descripcion': descripcion, 'fac_precio': precio, 'precio': precio, 'tipo': tipo, servicio_extra_id: 0};
						break;
				}
			} else { // cualquiera
				auxs.details.push({
					'descripcion': descripcion,
					'fac_precio': precio,
					'precio': precio,
					'tipo': tipo
				});
			}
		}

		function fillParams(emp){
			params.rsocial = emp.rsocial;
			params.empresa_nombre = emp.rsocial;
			params.empresa_direccion = emp.direccion;
			params.nombre_comercial = emp.nombrecomercial;
			params.fac_domicilio = emp.direccion;
			params.representante.domicilio = emp.direccion;
			if(auxs.empresa_selected === undefined){
					auxs.empresa_selected = {rsocial: emp.rsocial, ruc: emp.ruc };
			}
			if(emp.ruc.substr(0,2) === "10"){
				if(emp.nombrecomercial.length <= 0 || emp.nombrecomercial === '-'){
						params.nombre_comercial = emp.rsocial;
				}
				var pers = emp.tipodocumento.split("-");
				var dni = (pers[0].trim()).split(" ")[1];
				var nombres = (pers[1].trim()).split(",");
				// rellenar resp factura
				params.fac_nombre = nombres[1].trim();
				params.fac_apellido = nombres[0].trim();
				params.fac_dni = dni;
				// rellenar representante
				params.representante.nombre = nombres[1].trim();
				params.representante.apellido = nombres[0].trim();
				params.representante.dni = dni;
				params.representante.telefonos = emp.telefono.split("\/").join(",");
			}
		}

		function serviceCtrl(inst){
			var ctrl = this;
			var is_convenio = false;
			ctrl.sending = false;
			ctrl.auxs = {
				from_new_company: 1,
				planes: window.iVariables.planes,
				selected_plan: window.iVariables.planes[0]
			};
			ctrl.params = {
				tipo: 'E',
				monto: 1,
				mes: 1,
				concepto: 'Servicio Extra',
				servicio_extra_id: 0
			};
			ctrl.close = function(){
				inst.dismiss('close');
			};
			ctrl.add = function(){

				if(ctrl.params.tipo === 'C' && (ctrl.auxs.selected_plan === undefined || ctrl.auxs.selected_plan === null)){
					toastr.error('Seleccione un plan para el combo');
					return false;
				}

				// agregar
				var item = {
					'descripcion': ctrl.params.concepto,
					'fac_precio': ctrl.params.monto,
					'precio': ctrl.params.monto,
					'tipo': ctrl.params.tipo,
					'mes': ctrl.params.mes,
					'servicio_extra_id': 0
				};

				if(item.tipo === 'C'){
					item.descripcion = "COMBO " + ctrl.auxs.selected_plan.nombre;
					item.servicio_extra_id = ctrl.auxs.selected_plan.id;
					/*item.fac_precio = ctrl.auxs.selected_plan.precio;
					item.precio = ctrl.auxs.selected_plan.precio;*/
				}

				if(item.monto<=0){
					toastr.error("Solo se admite valores superiores a 0");
					return false;
				}

				if(item.tipo === 'D'){

					if($filter('findInArray')(auxs.details, 'tipo', 'D')>=0){
						toastr.error("Ya hay un descuento agregado.");
						return false;
					}

					item.fac_precio = ctrl.params.monto * -1;
				}

				auxs.details.push(item);

				ctrl.close();
			};
		}
	}
});