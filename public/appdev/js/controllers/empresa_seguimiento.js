/**
 * Created by Gonzalo A. Del Portal Ch. on 19/05/17.
 **/
define(['app', 'roles', 'angular'], function(app, roles, angular) {
    return app.controller('EmpresaSeguimientoCtrl', ['$scope', '$http', '$stateParams', '$uibModal', '$filter', 'toastr', function($scope, $http, $stateParams, $uibModal, $filter, toastr) {
        $scope.usuarios = [];
        var tipocrm = angular.copy(window.iVariables.crm_tipo);
        tipocrm.unshift({ id: "-", nombre: "CRM" });

        if(!empresa.seguimiento){
            empresa.seguimiento = {};
        }

        empresa.seguimiento.roles = roles;

        empresa.seguimiento.params = {
            fecha_reserva1: (new Date()).setMonth((new Date()).getMonth() - 1), //new Date()
            fecha_reserva2: new Date(),
            selectedTipofecha: { value: 'programacion', name: 'Programacion' },
            selectedCRM: { id: '-', nombre: 'CRM' },
        };

        empresa.seguimiento.extras = {
            listCrm: tipocrm,
            tipoFecha: [
                { value: 'programacion', name: 'Programacion' },
                { value: 'creacion', name: 'Creacion' }
            ]
        };

        empresa.seguimiento.auxs = {
            open1: false,
            open2: false,
            totalItems: 0,
            validateLoading: false,
            txtNota:"",
            selectedCRM: { id: '-', nombre: 'CRM' }
        };

        empresa.seguimiento.methods = {
            openCalendar1: function open1() {
                empresa.seguimiento.auxs.open1 = !empresa.seguimiento.auxs.open1;
            },
            openCalendar2: function open2() {
                empresa.seguimiento.auxs.open2 = !empresa.seguimiento.auxs.open2;
            },
            export: function() {

            },
            getData: function() {
                empresa.seguimiento.auxs.validateLoading = true;
                var param = angular.copy(empresa.seguimiento.params);
                param.fini = $filter('date')(empresa.seguimiento.params.fecha_reserva1, 'yyyy-MM-dd');
                param.ffin = $filter('date')(empresa.seguimiento.params.fecha_reserva2, 'yyyy-MM-dd');
                param.tipofecha = empresa.seguimiento.params.selectedTipofecha.value;
                param.crm = empresa.seguimiento.params.selectedCRM.id;
                param.empresa_id = empresa.id;
                $http({
                    url: '/seguimiento/list-notes-enterprice',
                    method: 'GET',
                    params: param
                }).then(function(r) {
                    if (r.data.load) {
                        if (r.data.rows.length > 0) {
                            empresa.seguimiento.data = r.data.rows;
                            empresa.seguimiento.auxs.totalItems = r.data.total;
                        } else {
                            empresa.seguimiento.data = [];
                            empresa.seguimiento.auxs.totalItems = 0;
                        }
                    }
                }).catch(function(response) {
                    toastr.error('No se pudo obtener datos del Seguimiento.');
                }).finally(function() {
                    empresa.seguimiento.auxs.validateLoading = false;
                });
            },
            newNota:function(){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/empresa/crm.html',
                    controller: ['$uibModalInstance', 'items', modalController],
                    controllerAs: '$ctrl',
                    resolve: {
                        items: function(){
                            return { 
                                data:{ 
                                    empresa_id:empresa.id,
                                    tipocrm:tipocrm 
                                }
                            };
                        }
                    }
                }).result.then(function(){}, function(){});
            },
            registerNote: function() {
                var dataObject = {
                    crm_tipo_id: empresa.seguimiento.auxs.selectedCRM.id,
                    nota: empresa.seguimiento.auxs.txtNota,
                    empresa_id: empresa.id
                };
                empresa.seguimiento.auxs.validateLoading = true;
                $http({
                    url: '/seguimiento/create-note-crm',
                    method: 'POST',
                    params: dataObject,
                    dataType: 'json'
                }).then(function(r) {
                    if (r.data.load) {
                        toastr.success('CORRECTO', r.data.msg);

                        empresa.seguimiento.methods.getNotesById();
                    }
                }).catch(function(response) {
                    toastr.error('No se pudo obtener Agregar la Nota.');
                }).finally(function() {
                    empresa.seguimiento.auxs.validateLoading = false;
                });
            }
        };

        var modalController = function($uibModalInstance, items){
            var now = new Date();
            var $ctrl = this;

            $ctrl.edit = false;
            $ctrl.extras = {
                horas:[9,10,11,12,13,14,15,16,17,18,19,20,21,22],
                minutos:[0,5,10,15,20,25,30,35,40,45,50,55],
                listCrm:angular.copy(window.iVariables.crm_tipo)
            };
            $ctrl.params = {
                fecha: now,
                nota: '',
                hora: now.getHours() * 1,
                minuto:30,
                crm_tipo_id:{ id: 1, nombre: 'COBRO' }
            };

            $ctrl.auxs = {
                calendarOpts: {
                    minDate: now,
                    showWeeks: false
                },
                conceptos: [{
                    id:'-',
                    nombre: 'Seleccione'
                }],
                isOpenCalendar: false,
                sending: false,
                usuarios: angular.copy($scope.usuarios)
            };

            $ctrl.auxs.to = $ctrl.auxs.usuarios[0];

            if(items !== undefined){
                if(items.data !== undefined){
                    $ctrl.data = items.data;
                }
            }
            $ctrl.close = function(){
                $uibModalInstance.dismiss('cancel');
            };

            $ctrl.openCalendar = function(){
                $ctrl.auxs.isOpenCalendar = true;
            };

            $ctrl.sendCrm = function(){
                $ctrl.auxs.sending = true;
                $ctrl.params.empresa_id = empresa.id;
                var param = angular.copy( $ctrl.params );
                param.crm_tipo_id = $ctrl.params.crm_tipo_id.id;
                param.usuario_asignado_id = $ctrl.auxs.to.id;
                $http({
                    url: '/seguimiento/create-note-crm',
                    method: 'POST',
                    params: param,
                    dataType: 'json'
                }).then(function(response){
                    toastr.success(response.data.message, 'Éxito');
                    $ctrl.close();
                    empresa.seguimiento.methods.getData();
                    console.log( response );
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error al enviar nota');
                }).finally(function(){
                    $ctrl.auxs.sending = false;
                });
            };
            return $ctrl;
        };

        if( !empresa.seguimiento.data ){
            empresa.seguimiento.methods.getData();
        }

        $http.get('/usuario/all?estado=A').then(function(r){
            $scope.usuarios = r.data.rows;
        }, function(e){ console.log(e); });
    }]);
});
