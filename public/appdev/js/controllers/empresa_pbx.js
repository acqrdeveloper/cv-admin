define(['app','roles'], function(app, roles){

	app.controller('PbxCtrl', controller);

	controller.$inject = ['$uibModal','CentralSrv','toastr'];

	function controller(uimodal, mySrv, toastr){

		var vm = this;

		var aux = {
			create: true,
			data: [],
			info: {},
			loading: false,
			mainView: true,
			numbers: []
		};

		var fn = {
			activePbx: activePbx,
			deletePbx: deletePbx,
			init: init,
			openForm: openForm,
			openModal: openModal,
			save: save
		};

		var params = {
			id: 0,
			cid_name: '',
			customer_id: empresa.id,
			description: '',
			song: '',
			record_id: null
		};

		angular.extend(vm, {
			aux: aux,
			fn: fn,
			params: params,
			roles: roles
		});

		function activePbx(pbx){
			if(window.confirm('¿Desea habilitar la central ' + pbx.number + '?')){
				aux.processing = true;
				mySrv.activePbx({number_id: pbx.id, customer_id: empresa.id}).then(function(r){
					toastr.success(r.data.message);
					getNumbers();
				}).catch(function(e){
					toastr.error(e.data.message);
				}).finally(function(){
					vm.aux.processing = false;
				});
			}
		}

		function deletePbx(pbx, idx){
			if(window.confirm('¿Desea inhabilitar la central ' + pbx.number + '?')){
				aux.processing = true;
				mySrv.deletePbx({number_id: pbx.id, customer_id: empresa.id}).then(function(r){
					toastr.success(r.data.message);
					getNumbers();
				}).catch(function(e){
					toastr.error(e.data.message);
				}).finally(function(){
					vm.aux.processing = false;
				});
			}

			return false;
		}

		function getFreeNumbers(){
			mySrv.getFreeNumbers().then(function(r){
				vm.aux.numbers = r.data;
			}, function(e){ console.log(e); });
		}

		function getNumbers(){
			aux.loading = true;
			mySrv.getNumbers(empresa.id).then(function(r){
				vm.aux.data = r.data;
			}).finally(function(){
				vm.aux.loading = false;
			});
		}

		function init(){
			getFreeNumbers();
			getNumbers();
		}

		function modalController(instance, items){
			var ctrl = this;

			ctrl.params = {
				customer_id: empresa.id,
				number_id: vm.params.id,
				ivr_id: vm.params.ivr_id,
				destiny_type: 'EXTENSION',
				edit: false
			};

			if(items.edit){
				ctrl.edit = true;
				ctrl.params.edit = true;
				ctrl.params.option = items.opcion.option;
				ctrl.params.label = items.opcion.label;
				ctrl.params.label_name = items.opcion.label_name;
				ctrl.params.destiny_type = items.opcion.destiny_type;
				ctrl.params.destiny_id = items.opcion.destiny_id;
				ctrl.params.redirect_to = items.opcion.redirect_to;
				ctrl.params.old_label = angular.copy(items.opcion.label);
			}

			ctrl.close = close;
			ctrl.saveOption = saveOption;
			ctrl.deleteOption = deleteOption;

			function close(){
				instance.dismiss('close');
			}

			function deleteOption(){
				ctrl.sending = true;
				mySrv.deleteOption({'customer_id':empresa.id, 'option':ctrl.params.option,'ivr_id':vm.params.ivr_id}).then(function(response){
					toastr.success(response.data.message, 'Éxito');
					vm.aux.info.opciones.splice(items.index,1);
					fn.init();
					close();
				}).catch(function(error){
					toastr.error(error.data, 'Error en la opción');
				}).finally(function(){
					ctrl.sending = false;
				});
			}

			function saveOption(){
				ctrl.sending = true;
				mySrv.saveOption(ctrl.params).then(function(response){
					toastr.success(response.data.message, 'Éxito');

					var item = {
						option: ctrl.params.option,
						label: ctrl.params.label,
						label_name: ctrl.params.label_name,
						redirect_to: ctrl.params.redirect_to,
						ivr_id: vm.params.ivr_id,
						destiny_type: ctrl.params.destiny_type
					};

					if(ctrl.params.destiny_type === 'OPERATOR'){
						item.label = '';
						item.label_name = '';
						item.redirect_to = '';
					}

					if(items.edit){
						vm.aux.info.opciones[items.index] = item;						
					} else {

						if(vm.aux.info.opciones === undefined)
							vm.aux.info.opciones = [];

						vm.aux.info.opciones.push( item );
					}

					fn.init();
					
					close();

				}).catch(function(e){
					toastr.error(e.data.error, 'Error en la opción');
				}).finally(function(){
					ctrl.sending = false;
				});
			}
		}

		function openForm(pbx){

			aux.mainView = !aux.mainView;

			if(pbx !== undefined){
				aux.create = false;
				params.id = pbx.id;
				params.cid_name = pbx.cid_name;
				aux.info.number = pbx.number;
				params.ivr_id = pbx.destiny.id;
				params.description = pbx.destiny.description;
				params.song = pbx.destiny.song;
				params.record_id = pbx.destiny.record_id;
				aux.info.opciones = pbx.destiny.options;
			} else {
				aux.create = true;
				params.id = 0;
				params.cid_name = '';
				params.ivr_id = undefined;
				params.record_id = null;
				params.song = '';
				params.description = '';
				aux.info.number = '';

				if(document.getElementById("audio_file") !== null){
					document.getElementById("audio_file").value = "";					
				}
			}
		}


		function openModal(action, row, idx){
			var e = false;

			if(row !== undefined)
				e = true;

			uimodal.open({
				animation: true,
				templateUrl: '/templates/modals/central/' + action + '.html',
				controller: ['$uibModalInstance','items', modalController],
				controllerAs: 'ctrl',
				resolve: {
					items: function(){
						return {edit:e, opcion: row, index: (idx===undefined?-1:idx)};
					}
				}
			}).result.then(function(){}, function(){});
		}

		function save(){
			aux.saving = true;

			var p = new FormData();
			p.append("id", params.id);
			p.append("cid_name", params.cid_name);
			p.append("customer_id", params.customer_id);
			p.append("description", params.description);
			p.append("song", params.song);
			p.append("record_id", params.record_id);
			p.append("create", aux.create);

			if(document.getElementById("audio_file").files[0] !== undefined)
				p.append("file", document.getElementById("audio_file").files[0]);

			mySrv.save(p).then(function(r){

				if(vm.aux.create){
					document.getElementById("audio_file").value = "";
					vm.params.ivr_id = r.data.ivr_id;
					vm.aux.info.number = r.data.number;
					vm.params.record_id = r.data.record_id;
					vm.aux.create = false;
					toastr.success(r.data.message);
				} else {
					toastr.success(r.data.message);
					vm.params.record_id = r.data.record_id;
				}

				// Call init
				init();

			}).catch(function(e){
				toastr.error(e.data.error);
			}).finally(function(){
				vm.aux.saving = false;
			});
		}
	}

});