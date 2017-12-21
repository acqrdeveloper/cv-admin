define(['app', 'roles'], function(app, roles) {
    app.controller('AsistenciaCtrl', AsistenciaCtrl);
    AsistenciaCtrl.$inject = ['$http', '$filter', '$timeout', '$uibModal', 'ListSrv', 'toastr'];
    function AsistenciaCtrl($http, $filter, $timeout, $uibModal, ListSrv, toastr) {
        var vm = this;
        vm.asistencia = {};
        var now = new Date();
        vm.asistencia.auxs = {
            dateOptions: {
                //minDate: now,
                dateDisabled: function (obj) {
                    return ( obj.mode === 'day' && obj.date.getDay() === 0 );
                }
            },
            selected: '',
            totalItems: 0,
            open1: false,
            validateLoading: false,
            months: ListSrv.months(),
            years: ListSrv.years()
        };

        vm.asistencia.params  = {
            dni:"",
            tipo:"I",
            fecha: new Date(),
            anio: now.getUTCFullYear(),
            mes: now.getUTCMonth() + 1
        }; 

        vm.asistencia.methods = {
            reset:function reset(){
                vm.asistencia.data = [];
            },
            search:function search(){
                vm.asistencia.auxs.validateLoading = true;
                var param    = angular.copy( vm.asistencia.params );
                param.fecha  = $filter('date')( param.fecha, 'yyyy-MM-dd', 'America/Lima');
                $http({
                    url: '/asistencia/search/'+vm.asistencia.params.tipo,
                    method: 'GET',
                    params: param
                }).then(function(r) {
                    if (r.data.load) {
                        vm.asistencia.data = r.data;
                    }
                }).catch(function(response) {
                    toastr.error('No se pudo obtener datos.');
                }).finally(function() {
                    vm.asistencia.auxs.validateLoading = false;
                    console.log( vm.asistencia );
                });
            },
            openCalendar1: function open1() {
                vm.asistencia.auxs.open1 = !vm.asistencia.auxs.open1;
            },
            openMessage: function openMessage(){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/bandeja/create.html',
                    controller: ['$uibModalInstance', modalCtrl],
                    controllerAs: 'ctrl'
                }).result.then(function(){}, function(){});
            },
            openModal: function(row, action){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/asistencia/modal.html',
                    controller: ['$uibModalInstance', 'items', modalCtrl],
                    controllerAs: 'ctrl',
                    resolve: {
                        items: {'row': angular.copy(row), 'action': action}
                    }
                }).result.then(function(){}, function(){});
            }
        };

        function modalCtrl(inst, items){
            var ctrl = this;
            ctrl.action = items.action;
            ctrl.evento = items.row;

            if(ctrl.action === "I"){
                ctrl.persona = vm.asistencia.data.persona[0];
            }

            ctrl.close = function(){
                inst.dismiss('close');
            };

            ctrl.params = {
                dni: "",
                nomape: "",
                email: "",
                movil: ""
            };

            ctrl.sending = false;

            ctrl.confirm = function(){
                $http({
                    url: '/asistencia/' + ctrl.evento.id + '/'  + vm.asistencia.params.dni,
                    method: 'PUT'
                }).then(function(r){
                    toastr.success('Asistencia confirmada');
                    ctrl.close();
                    vm.asistencia.methods.search();
                }, function(e){
                    toastr.error(r.data.message,'Error al confirmar asistencia');
                });
            };

            ctrl.register = function(){
                ctrl.sending = true;
                $http({
                    url: '/asistencia/' + ctrl.evento.id + '/1',
                    method: 'POST',
                    data: ctrl.params
                }).then(function(r){
                    toastr.success('Asistencia registrada');
                    ctrl.close();
                    vm.asistencia.methods.search();
                }).catch(function(e){
                    toastr.error(r.data.message,'Error al registrar asistencia');
                }).finally(function(){
                    ctrl.sending = false;
                });
            };

            ctrl.searchID = function(){
                ctrl.searching = true;
                $http.get('http://service.facturame.pe/consultadni/' + ctrl.params.dni, {
                    headers: {
                        'Accept':'application/json',
                        'X-CSRF-TOKEN': undefined,
                        'X-Requested-With': undefined
                    }
                }).then(function(r){
                    ctrl.searching = false;
                    if(r.data.load){
                        ctrl.params.nomape = r.data.nombre;
                    }
                }, function(e){
                    ctrl.searching = false;
                });
            };
        }
    }
});
