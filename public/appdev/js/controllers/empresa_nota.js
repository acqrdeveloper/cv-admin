/**
 * Created by Gonzalo A. Del Portal Ch. on 08/05/17.
 */
define(['app', 'roles', 'angular'], function(app, roles, angular) {
    return app.controller('EmpresaNotaCtrl', ['$scope','$http','$stateParams','$uibModal','toastr','listAllYears','listMonths','listTipoNota','filterCompany', function($scope,$http,$stateParams,$uibModal,toastr,listAllYears,listMonths,listTipoNota,filterCompany){

        var now = new Date();

        $scope.roles = roles;
        $scope.yearspay = listAllYears;
        $scope.months = listMonths;
        $scope.tiponota = listTipoNota;
        //globales
        $scope.data = {};
        $scope.totalItems            = 0;
        $scope.validateLoading       = false; //si cargo
        $scope.validateFailedLoading = false; //si no cargo

        $scope.params = {
            anio: now.getFullYear(),
            mes: now.getMonth()+1,
            tiponota: {value:"-", name:"Tipo Nota"}
        };

        $scope.methods = {
            sendSunat:function(factura_id){
                $scope.validateLoading = true;
                $http({
                    url: '/sunat/notasend/' + factura_id,
                    method: 'GET'
                }).then(function(response){
                    if( response.data.load ){
                        toastr.success("Nota declarada Exitosamente", 'Éxito');
                        $scope.methods.getData();
                    }
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error al enviar a sunat');
                }).finally(function(){
                    $scope.validateLoading = false;
                });
            },
            getData: function() {
                $scope.validateLoading = true;
                var param = angular.copy($scope.params);
                param.tiponota = param.tiponota.value;
                param.empresa_id = empresa.id;
                $http({
                    url: "/nota/search",
                    method: 'GET',
                    params: param
                }).then(function(r) {
                    if ( r.data.rows.length > 0 ) {
                        empresa.nota = r.data.rows;
                        $scope.totalItems = r.data.total;
                    } else {
                        empresa.nota = [];
                        $scope.totalItems = 0;
                    }
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error al obtener Notas');
                }).finally(function() {
                    $scope.validateLoading = false;
                    $scope.validateFailedLoading = false;
                });
            },
            applyChange: function() {
                $scope.methods.getData();
            },
            changePaginate: function() {
                $scope.data = [];
                $scope.validateFailedLoading = false;
                $scope.methods.doGet();
            },
            clickExportar: function() {
                var param = angular.copy($scope.params);
                param.tiponota = param.tiponota.value;
                param.empresa_id = empresa.id;
                var json = param;
                window.open('/export/nota?json=' + JSON.stringify(json),'');
            },
            doGet: function() {
                $scope.methods.getData();
            },
            openModalMail: function(nota_id){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/factura/mail_nota.html',
                    controller: ['$uibModalInstance','items',mailCtrl],
                    controllerAs: 'ctrl',
                    resolve: {
                        items: function(){
                            return {'nota_id':nota_id};
                        }
                    }
                }).result.then(function(){}, function(){});
            }
        };

        if( !empresa.nota || empresa.nota === {} || empresa.nota === null ){
            $scope.methods.getData();
        }

        function mailCtrl(instance, items){
            var ctrl = this;

            var correo = "";

            if(empresa.basic.fac_email === undefined || empresa.basic.fac_email === null){
                correo = empresa.basic.representantes[0].correo;
            } else {
                correo = empresa.basic.fac_email;
            }
            
            var cc = "";

            function close(){
                instance.dismiss('close');
            }

            function send(){
                $scope.validateLoading = true;
                $http({url: '/email/notasend/' + items.nota_id, method:'GET', params: {cc: ctrl.cc}}).then(function(r){
                    toastr.success(r.data.message);
                    close();
                }).catch(function(e){
                    toastr.error(e.data.message);
                }).finally(function(){
                    $scope.validateLoading = false;
                });
            }

            angular.extend(ctrl, {
                correo: correo,
                cc: cc,
                close: close,
                send: send
            });
        }
    }]);
});