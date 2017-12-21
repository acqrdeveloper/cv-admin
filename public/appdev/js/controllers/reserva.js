define(['app','roles'], function(app, roles){
    app.controller('ReservaCtrl', controller);
    controller.$inject = ['$filter', '$http', '$state', '$uibModal', 'ListSrv', 'EmpresaSrv', 'ReservaSrv', 'toastr'];
    function controller($filter, $http, $state, $uibModal, ListSrv, EmpresaSrv, mySrv, toastr){
        var vm = this;
        var auxs = {
            dateOptions: {
                dateDisabled: function (obj) {
                    return ( obj.mode === 'day' && obj.date.getDay() === 0 );
                }
            },
            estados: Object.assign({"": "Todos"}, angular.copy(window.iVariables.estados.reserva)),
            loading: false,
            locales: [{id:0, nombre:"Local"}].concat(window.iVariables.locales),
            modelos: [{id:0, nombre:"Tipo"}].concat(window.iVariables.modelos),
            oficinas: [{id:0, nombre_o:"Oficina"}],
            open: false,
            selected: '',
            years: ListSrv.years(),
            months: ListSrv.months(),
            days: ListSrv.days()

        };
        var data = [];
        var params = {estado: 'A', anio: (new Date()).getFullYear(), dia: (new Date()).getDate(), mes: (new Date()).getMonth()+1, local_id: 0, modelo_id: 0, oficina_id: 0 };
        var fn = {
            exportar: function(){
                var json = angular.copy(vm.params);
                if( json.limite !== undefined ){
                    delete json.limite;
                }
                window.open('/export/reserva?json=' + JSON.stringify(json),'');
            },
            active: function(id){
                if( confirm('¿Desea realizar esta accción?') ){
                    mySrv.changeState(id, 'A').then(function(r){
                        toastr.success('Evento aceptado');
                        fn.search();
                    }, function(e){
                        toastr.error('Hubo un error al activar el evento.');
                        console.log(e);
                    });
                }

                return false;
            },
            clearFilter: function(){
                auxs.selected = '';
                params.empresa_id = '';
            },
            confirmar: function(id){
                if( confirm('¿Desea confirmar esta reserva?') ){
                    mySrv.changeState(id, 'P').then(function(r){
                        toastr.success('Evento confirmado');
                        fn.search();
                    }, function(e){
                        toastr.error('Hubo un error al activar el evento.');
                        console.log(e);
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
            filterOffices: function(){
                auxs.oficinas.splice(1);

                if(params.local_id > 0 && params.modelo_id > 0){
                    angular.forEach(angular.copy(window.iVariables.oficinas), function (item) {
                        if (item.local_id === params.local_id && (item.modelo_id === params.modelo_id || params.modelo_id === 0)) {
                            auxs.oficinas.push(item);
                        }
                    });
                }

                params.oficina_id = 0;
            },
            justify: function(id){
                if( confirm('¿Desea justificar esta reserva?') ){
                    mySrv.changeState(id, 'J').then(function(r){
                        toastr.success('Evento justificado');
                        fn.search();
                    }, function(e){
                        toastr.error('Hubo un error al activar el evento.');
                        console.log(e);
                    });
                }

                return false;
            },
            openCalendar: function(){
                vm.auxs.open = true;
            },
            openCreate: function(){
                $state.go('reserva_nueva');
            },
            openEdit: function(r){
                $state.go('reserva_editar', {'reservaID': r.id, 'reserva': angular.copy(r)});
            },
            openModal: function(res, modal){
                $uibModal.open({
                    animation: true,
                    templateUrl: 'templates/modals/reserva/' + modal + '.html',
                    controller: ['$uibModalInstance','items',modalCtrl],
                    controllerAs: '$ctrl',
                    resolve: {
                        items: function(){
                            return {reserva: res, tipo: modal};
                        }
                    }
                }).result.then(function(){}, function(){});
            },
            search: function(){
                auxs.loading = true;
                var p = angular.copy(params);
                delete p.fecha_reserva;
                mySrv.search(p).then(function (response) {
                    vm.auxs.loading = false;
                    vm.data = response.data.rows;
                }, function (error) {
                    console.log(error);
                }).finally(function () {
                    vm.auxs.loading = false;
                });
            },
            searchByFilter: function() {
                fn.search();
            }
        };
        angular.extend(vm, {
            auxs: auxs,
            data: data,
            fn: fn,
            params: params,
            roles: roles
        });
        fn.search();


        function modalCtrl(instance, items){
            var $ctrl = this;
            $ctrl.row = items.reserva;
            console.log($ctrl.row);
            var reserva = items.reserva;
            if(items.tipo !== undefined){
                switch(items.tipo){
                    case 'obs':
                        if (reserva.observacion.length === 0 || reserva.observacion === null) {
                            $ctrl.data = [];
                        } else {
                            $ctrl.data = JSON.parse(reserva.observacion);
                        }
                        break;
                    case 'history':
                        $ctrl.loading = true;
                        $http.get('/reserva/' + reserva.id + '/historial').then(function (response) {
                            $ctrl.loading = false;
                            $ctrl.data = response.data;
                        });
                        break;
                }
            }
            $ctrl.close = function(){
                instance.dismiss('cancel');
            };
            $ctrl.deleteReserve = function () {
                $http({
                    url: '/reserva/' + reserva.id,
                    method: 'DELETE'
                }).then(function (response) {
                    toastr.success('Reserva eliminada', 'Exito');
                    $ctrl.close();
                    fn.searchByFilter();
                }, function (error) {
                    toastr.error(error.data.message, 'Error al eliminar reserva');
                });
            };
            $ctrl.import = function(){
                var file = document.getElementById("file_invitados");
                if(file.files.length>0){
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var allTextLines = (e.target.result).split(/\r\n|\n/);
                        var estructura = [];
                        while(allTextLines.length){
                            var line = allTextLines.shift().split(',');
                            if(line[0].length === 8 && (/^[\d]+$/g).exec(line[0]) !== null){
                                estructura.push({
                                    dni: line[0],
                                    nomape: (line[1]!==undefined?line[1]:''),
                                    email: (line[2]!==undefined?line[2]:''),
                                    movil: (line[3]!==undefined?line[3]:'')
                                });
                            }
                        }
                        // Listo, a subirlo al sistema
                        mySrv.uploadList(reserva.id, estructura).then(function(r){
                            toastr.success('Lista cargada.', 'Éxito');
                            $ctrl.close();
                        }).catch(function(e){
                            toastr.error(e.data.message,'Error');
                        });
                    };
                    reader.readAsText(file.files[0]);
                } else {
                    toastr.error('Debes subir primero la lista de invitados','Error al procesar');
                }
            };
            $ctrl.updateObservacion = function(){
                $http({
                    url: '/reserva/' + reserva.id + '/observacion',
                    method: 'PUT',
                    params: {'observacion': $ctrl.obs}
                }).then(function (response) {
                    toastr.success('Observación actualizada', 'Exito');
                    $ctrl.data.unshift(response.data);
                    reserva.observacion = JSON.stringify($ctrl.data);
                }, function (error) {
                    toastr.success(error.data, 'Error al actualizar la observación');
                });
            };
        }
    }
});