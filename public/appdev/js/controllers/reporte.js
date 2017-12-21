/**
 * Created by QuispeRoque on 06/04/17.
 * Remake by Gonzalo A. Del Portal Ch. on 27/04/17.
 */
define(['app', 'roles', 'angular', 'appdev/js/util'], function(app, roles, angular, Util) {
    return app.controller('ReporteCtrl', ['$scope','$http','$uibModal','toastr', 'ListSrv','listAllYears','listYears','listMonths','listtipopago','listciclo','listreporte','filterCompany', function($scope,$http,$uibModal,toastr,ListSrv,listAllYears,listYears,listMonths,listtipopago,listciclo,listreporte,filterCompany){

        $scope.roles = roles;
        $scope.years = listYears;
        $scope.yearspay = listAllYears;
        $scope.months = listMonths;
        $scope.tipopago = listtipopago;
        $scope.reportes = listreporte;
        $scope.days = ListSrv.days();

        //globales
        $scope.data = {};
        $scope.selection             = {};
        $scope.totalItems            = {};
        $scope.validateLoading       = {}; //si cargo
        $scope.validateFailedLoading = {}; //si no cargo

        $scope.params = {
            year: (new Date()).getFullYear(),
            month: ((new Date()).getMonth()+1),
            yearpay: (new Date()).getFullYear(),
            monthpay: ((new Date()).getMonth()+1),
            ciclo:{value:"-", name:"Ciclo"},
            reporte:{value:"empresaactual", name:"Empresas Situacion Actual"},
            
            tipopago:{value:"-", name:"Tipo Pago"},
            day: (new Date()).getDate()
            //limite: 20,
            //pagina: 1,
        };

        $scope.columns = ListSrv.getReportColums( $scope.params.reporte.value);
        $scope.rsColumns = ListSrv.getReportResultSetColumns( $scope.params.reporte.value);

        //metodos $scope
        $scope.methods = {
            getData: function() {
                $scope.validateLoading[$scope.params.reporte.value] = true;
                var param = angular.copy($scope.params);
                param.ciclo = param.ciclo.value;
                param.reporte = param.reporte.value;
                param.tipopago = param.tipopago.value;
                $http({
                    url: "/reporte/"+param.reporte,
                    method: 'GET',
                    params: param
                }).then(function(r) {
                    if (r.data.load) {
                        if ( r.data.rows.length > 0 || ( r.data.rows.adelantos && r.data.rows.adelantos.rows.length > 0 ) || ( r.data.rows.sobrantes && r.data.rows.sobrantes.rows.length > 0 ) ) {
                            $scope.data[param.reporte] = r.data.rows;
                            $scope.totalItems[param.reporte] = r.data.total;
                            $scope.validateLoading[param.reporte] = false;
                            $scope.validateFailedLoading[param.reporte] = false;
                        } else {
                            $scope.data[param.reporte] = [];
                            $scope.totalItems[param.reporte] = 0;
                            $scope.validateLoading[param.reporte] = false;
                            $scope.validateFailedLoading[param.reporte] = true;
                        }
                    }
                }, function(){
                    toastr.error('No se pudo obtener el historial de este recado.');
                });//Util.errorRpta
            },
            changeReport:function(){
                $scope.columns = ListSrv.getReportColums( $scope.params.reporte.value);
                $scope.rsColumns = ListSrv.getReportResultSetColumns( $scope.params.reporte.value);
                $scope.validateFailedLoading[$scope.params.reporte.value] = false;
                if( $scope.data[$scope.params.reporte.value] ){
                    if( $scope.data[$scope.params.reporte.value].length <= 0 ){
                        $scope.methods.getData();
                    }
                }else{
                    $scope.methods.getData();
                }
            },
            applyChange: function() {
                $scope.methods.getData();
            },
            changePaginate: function() {
                $scope.data[$scope.params.reporte.value] = [];
                $scope.validateFailedLoading[$scope.params.reporte.value] = false;
                $scope.methods.doGet();
            },
            clickExportar: function() {
                var param = angular.copy($scope.params);
                param.ciclo = param.ciclo.value;
                param.reporte = param.reporte.value;
                param.tipopago = param.tipopago.value;
                var json = param;
                window.open('/export/'+param.reporte+'?json=' + JSON.stringify(json),'');
            },
            doGet: function() {
                $scope.params.pagina = 1;
                $scope.methods.getData();
            }
        };
        //inicializados
        $scope.methods.getData();
    }]);
});