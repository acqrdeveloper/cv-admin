define(['app','roles','angular'], function(app, roles, angular){
	app.controller("Correspondencia2Ctrl", controller);
	controller.$inject = ['$stateParams', '$timeout', '$uibModal', 'toastr', 'dataservice', 'ListSrv','CorrespondenciaSrv'];
	function controller($stateParams, $timeout, $uibModal, toastr, dataservice, listSrv, myServ){
		var vm = this;
		vm.data = [];
		vm.years = listSrv.years();
		vm.months = listSrv.months();
		vm.roles = roles;
		vm.states = Object.assign({"-":"Todos"}, window.iVariables.estados.correspondencia);
		vm.auxs = {
			loading: false,
			selection: [],
			totalItems: 0
		};
		vm.params = {
			filter: '',
			filter_search: 'asunto',
			anio: (new Date()).getFullYear(),
			mes: (new Date()).getMonth()+1,
			estado: '-',
			limite: 20,
			pagina: 1
		};

		vm.checkAll = checkAll;
		vm.clearSelection = clearSelection;
		vm.filterCompanies = filterCompanies;
		vm.filterSearch = filterSearch;
		vm.export = doExport;
		vm.openModal = openModal;
		vm.search = search;
		vm.selectRow = selectRow;

		if($stateParams.empresa_id !== undefined){
			vm.params.empresa_id = $stateParams.empresa_id;
			vm.auxs.selected = $stateParams.empresa_nombre;
		}

		search();

		var modalController = function($uibModalInstance, items){
			var $ctrl = this;

			if(items !== undefined){
				$ctrl.edit = items.edit;
				if(items.correspondencia !== undefined && items.correspondencia !== null){
					var row = items.correspondencia;
					$ctrl.row = row;
					$ctrl.params = {
						id: row.id,
						empresa_id: row.empresa_id,
						empresa_nombre: row.empresa_nombre,
						remitente: row.remitente,
						entregado_a: row.entregado_a,
						asunto: row.asunto,
						estado: row.estado,
						local_id: row.local_id
					};
				}
			}

			$ctrl.locales = window.iVariables.locales;

			$ctrl.data = [];
			$ctrl.loading = false;
			$ctrl.cc = false;
			$ctrl.ccText = "Cc: Enviar una copia a otro correo";

			$ctrl.clearSelection = function(){
				$ctrl.selected = '';
				$ctrl.params.empresa_id = '';
			};

			$ctrl.close = function(){
				$uibModalInstance.dismiss('cancel');
			};

			$ctrl.delete = function(){
				$ctrl.sending = true;
				myServ.delete($ctrl.params.id, $ctrl.params.observacion).then(function(response){
					toastr.success('Correspondencia eliminada', 'Éxito');
					filterSearch();
					$ctrl.close();
				}).catch(function(response){
					toastr.error(response.data, 'Error al eliminar correspondencia');
				}).finally(function(){
					$ctrl.sending = false;
				});
			};

			$ctrl.deliver = function(){
				if($ctrl.params.id !== undefined && $ctrl.params.id !== null){
					vm.auxs.selection = [];
					vm.auxs.selection.push($ctrl.params.id);
				}

				$ctrl.sending = true;
				var params = {
					entregado_a: $ctrl.params.entregado_a
				};
				params.corresIDs = vm.auxs.selection.join(",");
				myServ.deliver(params).then(function(response){
					$ctrl.close();
					vm.filterSearch();
					toastr.success("Correspondencia(s) entregada(s)", "Exito");
				}).catch(function(){
					toastr.error("Error al entregar correspondencia", "Error");
					$ctrl.sending = false;
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

			$ctrl.getHistory = function(){
				$ctrl.loading = true;
				myServ.getHistory($ctrl.params.id).then(function(response){
					$ctrl.data = response.data;
				}).catch(function(response){
					$ctrl.close();
					toastr.error(response.data, 'Error al obtener el historial');
				}).finally(function(){
					$ctrl.loading = false;
				});
			};

			$ctrl.send = function(){
				$ctrl.sending = true;
				myServ.send($ctrl.params).then(function(response){
					if($ctrl.edit)
						toastr.success("Correspondencia editada", "Exito");
					else
						toastr.success("Correspondencia creada", "Exito");
					$ctrl.close();
					vm.filterSearch();
				}).catch(function(response){
					toastr.error(response.data);
				}).finally(function(){
					$ctrl.sending = false;
				});
			};

			$ctrl.showCC = function(evt){
				$ctrl.cc = !$ctrl.cc;
			};
		};

		function checkAll(){
			if(vm.auxs.isToggle){
				vm.auxs.selection = [];
			} else {
				angular.forEach(vm.data, function(item){
					if(item.estado == 'P' && vm.auxs.selection.indexOf(item.id)<=-1){
						vm.auxs.selection.push(item.id);
					}
				});
			}
			vm.auxs.isToggle = !vm.auxs.isToggle;
		}

		function clearSelection(){
			vm.auxs.selected = '';
			vm.params.empresa_id = '';
			vm.filterSearch();
		}

		function doExport(){
			var json = angular.copy(vm.params);
			delete json.pagina;
			delete json.limite;
			window.open('/export/correspondencia?json=' + JSON.stringify(json),'');
		}

		function filterCompanies(value){
			if(value === '' || value.length < 3){
				vm.params.empresa_id = null;
				return [];
			} else {
				return dataservice.searchCompanies({view:'minimal',empresa_nombre:value});
			}
		}

		function filterSearch(){
			vm.params.pagina = 1;
			search();
		}

		function openModal(action, row){
			var e = false;

			if(action === 'create' && row !== undefined)
				e = true;

			$uibModal.open({
				animation: true,
				templateUrl: '/templates/modals/correspondencia/' + action + '.html',
				controller: ['$uibModalInstance','items', modalController],
				controllerAs: '$ctrl',
				resolve: {
					items: function(){
						return {edit:e, correspondencia: angular.copy(row)};
					}
				}
			}).result.then(function(){}, function(){});
		}

		function search(){
			vm.auxs.loading = true;
			dataservice.searchCorrespondencia(vm.params).then(function(response){
				vm.auxs.loading = false;
				vm.data = response.data.rows;
				vm.auxs.totalItems = response.data.total;
				vm.auxs.selection = [];
			}).catch(function(response){
				toastr.error(response.data, 'Error al obtener las correspondencias');
			});
		}

		function selectRow(id){
		    var idx = vm.auxs.selection.indexOf(id);
		    if (idx > -1)
		    	vm.auxs.selection.splice(idx, 1);
		    else
		    	vm.auxs.selection.push(id);
		}
	}
});