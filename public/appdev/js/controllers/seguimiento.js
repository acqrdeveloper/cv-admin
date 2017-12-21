/**
 * Created by Gonzalo A. Del Portal Ch. on 18/05/17.
 */
define(['app', 'roles'], function(app, roles) {
    app.controller('SeguimientoCtrl', SeguimientoCtrl);
    SeguimientoCtrl.$inject = ['$http', '$scope', '$timeout', '$filter', 'dataservice', 'toastr', 'filterCompany', 'listEstadoEmpresa', 'listciclo'];

    function SeguimientoCtrl($http, $scope, $timeout, $filter, dataservice, toastr, filterCompany, listEstadoEmpresa, listciclo) {
        var vm = this;

        vm.roles = roles;
        vm.seguimiento = {};
        asesores = angular.copy(window.iVariables.noasesores);
        asesores.unshift({ id: "-", nombre: "Todos" });

        tipocrm = angular.copy(window.iVariables.crm_tipo);
        tipocrm.unshift({ id: "-", nombre: "CRM" });

        planes = angular.copy(window.iVariables.planes);
        planes.unshift({ id: "-", nombre: "Plan" });

        vm.seguimiento.extras = {
            listEstadoEmpresa: listEstadoEmpresa,
            tipoFecha: [
                { value: 'programacion', name: 'Programacion' },
                { value: 'creacion', name: 'Creacion' }
            ],
            listciclo: listciclo,
            listAsesores: asesores,
            listCrm: tipocrm,
            listPlanes: planes
        };

        vm.seguimiento.params = {
            limite: 20,
            pagina: 1,
            selectedEstado: { value: '-', name: 'Estado Cliente' },
            selectedTerminado : '-',
            selectedUser: { id: '-', nombre: 'Usuario' },
            selectedPlan: { id: '-', nombre: 'Plan' },
            selectedCRM: { id: '-', nombre: 'CRM' },
            selectedCiclo: { value: '-', name: 'Ciclo' },
            selectedTipofecha: { value: 'programacion', name: 'Programacion' },
            fecha_reserva1: (new Date()).setMonth((new Date()).getMonth() - 1), //new Date()
            fecha_reserva2: new Date(),
            fini: $filter('date')(new Date(), 'yyyy-MM-dd', 'America/Lima'),
            ffin: $filter('date')(new Date(), 'yyyy-MM-dd', 'America/Lima'),
        };

        vm.seguimiento.views = {
            main: 1, 
            detalle: 0
        };

        vm.seguimiento.data = [];
        vm.seguimiento.datadetalle = [];

        vm.seguimiento.auxs = {
            open1: false,
            open2: false,
            totalItems: 0,
            validateLoading: false
        };

        vm.seguimiento.methods = {
            changeState: function(nota_id, estado){
                if(window.confirm('¿Deseas realizar esta acción?')){
                    $http({
                        url: '/seguimiento/estado',
                        method: 'PUT',
                        data: {id: nota_id, terminado: estado}
                    }).then(function(r){
                        toastr.success('Datos actualizados');
                        vm.seguimiento.methods.getData();
                    }).catch(function(e){
                        toastr.error(e.data.message);
                    }).finally(function(){
                    });
                }

                return false;
            },
            openCalendar1: function open1() {
                vm.seguimiento.auxs.open1 = !vm.seguimiento.auxs.open1;
            },
            openCalendar2: function open2() {
                vm.seguimiento.auxs.open2 = !vm.seguimiento.auxs.open2;
            },
            applyChange: function() {
                console.log(vm.seguimiento.params.selectedCRM.id);
                console.log(vm.seguimiento.params.txtNota);
            },
            applyClean: function() {
                vm.seguimiento.params.selectedCRM.id = '1';
                vm.seguimiento.params.txtNota = '';
            },
            filterCompanies: function(value, onlyActive) {
                if (value === '' || value.length < 3) {
                    return [];
                } else {
                    var params = { view: 'minimal', empresa_nombre: value };
                    if (onlyActive === 1) {
                        params.estado = 'A,S,X';
                    }
                    return dataservice.searchCompanies(params);
                }
            },
            clearCompanies: function() {
                vm.seguimiento.params.selected = '';
                vm.seguimiento.params.empresa_id = 0;
                vm.seguimiento.methods.getData();
            },
            clickHideView: function(open, close, empresa_id, crm_id, empresa_nombre) {
                vm.seguimiento.views[open] = 1;
                vm.seguimiento.views[close] = 0;
                vm.seguimiento.params.empresa_id = empresa_id;
                vm.seguimiento.params.crm = crm_id;
                vm.seguimiento.params.empresa_nombre = empresa_nombre;
                if (open == 'detalle') {
                    vm.seguimiento.methods.getNotesById();
                }
            },
            getData: function() {
                vm.seguimiento.auxs.validateLoading = true;
                param = angular.copy(vm.seguimiento.params);
                param.fini = $filter('date')(vm.seguimiento.params.fecha_reserva1, 'yyyy-MM-dd');
                param.ffin = $filter('date')(vm.seguimiento.params.fecha_reserva2, 'yyyy-MM-dd');
                param.estado = vm.seguimiento.params.selectedEstado.value;
                param.plan = vm.seguimiento.params.selectedPlan.id;
                param.tipofecha = vm.seguimiento.params.selectedTipofecha.value;
                param.usuario = vm.seguimiento.params.selectedUser.id;
                param.crm = vm.seguimiento.params.selectedCRM.id;
                param.ciclo = vm.seguimiento.params.selectedCiclo.value;
                if (vm.seguimiento.params.selected) {
                    if (vm.seguimiento.params.selected.id > 0) {
                        param.empresa_id = vm.seguimiento.params.selected.id;
                    }
                }
                $http({
                    url: '/seguimiento/list',
                    method: 'GET',
                    params: param
                }).then(function(r) {
                    if (r.data.load) {
                        if (r.data.rows.length > 0) {
                            vm.seguimiento.data = r.data.rows;
                            vm.seguimiento.auxs.totalItems = r.data.total;
                        } else {
                            vm.seguimiento.data = [];
                            vm.seguimiento.auxs.totalItems = 0;
                        }
                    }
                }).catch(function(response) {
                    toastr.error('No se pudo obtener datos del Seguimiento.');
                }).finally(function() {
                    vm.seguimiento.auxs.validateLoading = false;
                });
            },
            getNotesById: function() {
                vm.seguimiento.auxs.validateLoading = true;
                para = angular.copy(vm.seguimiento.params);
                if (para.crm) {
                    delete para.crm;
                }
                $http({
                    url: '/seguimiento/list-notes-enterprice',
                    method: 'GET',
                    params: para
                }).then(function(r) {
                    if (r.data.load) {
                        if (r.data.rows.length > 0) {
                            vm.seguimiento.datadetalle = r.data.rows;
                        } else {
                            vm.seguimiento.datadetalle = [];
                        }
                    }
                }).catch(function(response) {
                    toastr.error('No se pudo obtener datos del Detalle Seguimiento.');
                }).finally(function() {
                    vm.seguimiento.auxs.validateLoading = false;
                });
            },
            registerNote: function() {
                //console.log("test");
                var dataObject = {
                    crm_tipo_id: vm.seguimiento.params.selectedCRM.id,
                    nota: vm.seguimiento.params.txtNota,
                    empresa_id: vm.seguimiento.params.empresa_id
                };
                vm.seguimiento.auxs.validateLoading = true;
                $http({
                    url: '/seguimiento/create-note-crm',
                    method: 'POST',
                    params: dataObject,
                    dataType: 'json'
                }).then(function(r) {
                    if (r.data.load) {
                        toastr.success('CORRECTO', r.data.msg);

                        vm.seguimiento.methods.getNotesById();
                    }
                }).catch(function(response) {
                    toastr.error('No se pudo obtener Agregar la Nota.');
                }).finally(function() {
                    vm.seguimiento.auxs.validateLoading = false;
                });
            }
        };
    }
});
