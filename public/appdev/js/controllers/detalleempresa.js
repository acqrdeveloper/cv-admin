define(['app','roles'], function(app, roles) {
    return app.controller('DetalleEmpresaCtrl', ['$scope','$timeout','$filter','$http','$state','$stateParams','$uibModal','EmpresaSrv','toastr', function($scope,$timeout,$filter,$http,$state,$stateParams,uimodal,mySrv,toastr){
        empresa             = {};
        empresa.id          = $stateParams.empresaID;
        empresa.info        = null;
        empresa.central     = null;
        empresa.servicio    = null;
        empresa.facturacion = null;
        empresa.seguimiento = null;
        empresa.reservas    = null;
        empresa.mensajes    = null;
        empresa.llamadas    = null;

        $scope.estados      = {'A':'Activo','S':'Inactivo','E':'Eliminado'};
        $scope.validateLoading = true;
        $scope.empresa      = empresa;
        $scope.error        = false;
        $scope.loading      = true;
        $scope.roles        = roles;
        $scope.empresamain  = {
            preferencia_estado : 'S'
        };

        $http({
            url: "/basic/"+empresa.id,
            method: 'GET'
        }).then(function(r) {
            if (r.data.load) {
                $scope.validateLoading = false;
                empresa.basic       = r.data.rows;

                $scope.tabs = [
                    { heading: "Info",        rol_id:10, route: "empresa.info",        active: false, isVisible: true, href: $state.href("empresa.info") },
                    { heading: "Central",     rol_id:11, route: "empresa.central",     active: false, isVisible: true, href: $state.href("empresa.central") },
                    { heading: "Servicio",    rol_id:12, route: "empresa.servicio",    active: false, isVisible: true, href: $state.href("empresa.servicio") },
                    { heading: "Coworking",   rol_id:12, route: "empresa.coworking",   active: false, isVisible: (empresa.basic.plan_id === 25), href: $state.href("empresa.coworking") },
                    { heading: "Facturacion", rol_id:13, route: "empresa.facturacion", active: false, isVisible: true, href: $state.href("empresa.facturacion") },
                    { heading: "Nota",        rol_id:14, route: "empresa.nota",        active: false, isVisible: true, href: $state.href("empresa.nota") },
                    { heading: "Seguimiento", rol_id:15, route: "empresa.seguimiento", active: false, isVisible: true, href: $state.href("empresa.seguimiento") },
                    { heading: "Reservas",    rol_id:16, route: "empresa.reservas",    active: false, isVisible: true, href: $state.href("empresa.reservas") },
                    { heading: "Mensajes",    rol_id:17, route: "empresa.mensajes",    active: false, isVisible: true, href: $state.href("empresa.mensajes") },
                    { heading: "Llamadas",    rol_id:18, route: "empresa.llamadas",    active: false, isVisible: true, href: $state.href("empresa.llamadas") },
                    { heading: "Historial",   rol_id:19, route: "empresa.historial",   active: false, isVisible: true, href: $state.href("empresa.historial") },
                    { heading: "Extras",      rol_id:20, route: "empresa.extras",      active: false, isVisible: true, href: $state.href("empresa.extras") }
                ];

                angular.forEach(angular.copy(window.iVariables.planes), function(plan){
                    if(plan.id === empresa.basic.plan_id){
                        empresa.basic.plan = plan;
                        return false;
                    }
                });

                $scope.empresamain.preferencia_estado = angular.copy(empresa.basic.preferencia_estado);
            }
        }).catch(function(response){
            toastr.error('No se pudo obtener datos de la empresa.');
            $scope.error = true;
            $scope.errorDetails = response.data.message;
        }).finally(function(){
            $scope.loading = false;
        });

        $scope.activeCompany = activeCompany;

        $scope.changeInterviewState = function(tipo){
            var c = confirm('¿Seguro de realizar esta acción?');
            if(c){
                mySrv.changeInterview(empresa.basic.id,tipo).then(function(response){
                    toastr.success(response.data.message, 'Éxito');
                    empresa.basic.entrevistado = tipo;
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error al actualizar entrevista');
                });
            }
        };

        $scope.postponeInterview = function(){
            uimodal.open({
                animation: true,
                templateUrl: '/templates/modals/empresa/postpone-interview.html',
                controller: ['$uibModalInstance', modalController],
                controllerAs: '$ctrl'
            }).result.then(function(){}, function(){});     
        };

        $scope.updateState = function(){

            console.log($scope.empresamain.preferencia_estado);
            var c = confirm('¿Seguro de realizar esta acción?');
            if(c){
                mySrv.updateState(empresa.basic.id, {'estado': $scope.empresamain.preferencia_estado}).then(function(r){
                    toastr.success(r.data.message,'Éxito al actualizar estado');
                    empresa.basic.preferencia_estado = angular.copy($scope.empresamain.preferencia_estado);
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error al actualizar estado');
                });
            } else {
                $scope.empresamain.preferencia_estado = angular.copy(empresa.basic.preferencia_estado);
            }
        };

        function activeCompany(){
            $scope.activating = true;

            $http({
                url: '/empresa/' + empresa.id + '/activar',
                method: 'PUT'
            }).then(function(r){
                if((r.data.load*1) === 1){
                    $timeout(function(){
                        toastr.success(r.data.message);
                        empresa.basic.preferencia_estado = "A";
                    }, 100);
                } else {
                    toastr.error(r.data.message);
                }
            }).catch(function(e){
                toastr.error(e.data.error);
            }).finally(function(){
                $timeout(function(){
                    $scope.activating = false;
                }, 100);
            });
        }

        function isDebtor(){
            $http.get('/empresa/' + empresa.id + '/deuda/PENDIENTE').then(function(r){
                if(r.data.length>0)
                    empresa.esDeudor = {};
                else
                    empresa.esDeudor = 0;
            });
        }

        function modalController($instance){
            var $ctrl = this;
            $ctrl.is_open = false;
            $ctrl.sending = false;
            $ctrl.dateOptions = {
                minDate: new Date(),
                showWeeks: false
            };
            $ctrl.close = function(){
                $instance.dismiss('cancel');
            };
            $ctrl.openCalendar = function(){
                $ctrl.is_open = true;
            };
            $ctrl.postpone = function(){
                $ctrl.sending = true;
                mySrv.postponeInterview(empresa.basic.id,{estado:'P',fecha: $filter('date')($ctrl.fecha, 'yyyy-MM-dd', 'America/Lima')}).then(function(response){
                    toastr.success(response.data.message, 'Éxito');
                    empresa.basic.entrevistado = 'P';
                    empresa.basic.entrevista_recordatorio = $filter('date')($ctrl.fecha, 'yyyy-MM-dd', 'America/Lima');
                    $ctrl.close();
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error');
                });
            };
        }
    }]);
});
