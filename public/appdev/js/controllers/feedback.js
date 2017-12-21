/**
 * Created by QuispeRoque on 11/04/17.
 * Corrected by Gonzalo A. Del Portal Ch. on 27/04/17.
 */
define(['app', 'roles', 'angular', 'appdev/js/util'], function (app, roles, angular, Util) {
    app.controller('FeedbackCtrl', controller);
    controller.$inject = ['$http', '$scope', '$timeout'];

    function controller($http, $scope, $timeout) {

        //globales
        $scope.data = [];
        $scope.filters = Util.fillCbo();//carga de filtros
        $scope.validateLoading = true;//si cargo
        $scope.validateFailedLoading = false;//si no cargo
        $scope.params = {
            anio: Util.date.getUTCFullYear(),
            mes: Util.date.getUTCMonth() + 1,
            limite: 20,
            pagina: 1,
            tipo:'S'
        };//parametros iniciados
        $scope.totalItems = 0;
        $scope.selection = [];

        //metodos $scope
        $scope.methods = {
            getData: function () {
                $scope.validateLoading = true;
                $http({
                    url: Util.rootUrl + 'getFeedback',
                    method: 'GET',
                    params: $scope.params
                }).then(function (r) {
                    if (r.data.load) {
                        if (r.data.rows.length > 0) {
                            $scope.validateLoading = false;
                            $scope.validateFailedLoading = false;
                            $scope.data = r.data.rows;
                            $scope.totalItems = r.data.total;
                            $scope.selection = [];
                        } else {
                            $scope.totalItems = 0;
                            $scope.data = [];
                            $scope.validateLoading = false;
                            $scope.validateFailedLoading = true;
                        }
                    }
                }, Util.errorRpta);
            },
            applyChange: function () {
                $scope.validateFailedLoading = false;
                $scope.params.anio = $scope.filters.selectedYear.id;
                $scope.params.mes = $scope.filters.selectedMonth.value;
                $scope.params.tipo = $scope.selectedTipo.value;
                $scope.methods.getData();
            },
            changePaginate: function () {
                $scope.data = [];
                $scope.validateFailedLoading = false;
                $scope.methods.doGet();
            },
            clickExportar: function () {
                window.open('/export/feedback?json=' + JSON.stringify($scope.params), '');
            },
            doGet: function () {
                $scope.params.pagina = 1;
                $scope.methods.getData();
            }
        };

        //inicializados
        $scope.methods.getData();
        $scope.formulario = Util.getElement('frmload');
        $scope.arrayTipo = [
            {value: '-', name: 'Tipo'},
            {value: 'S', name: 'Sugerencia'},
            {value: 'Q', name: 'Queja'}
        ];
        $scope.selectedTipo = $scope.arrayTipo[0];
        $scope.status = {
            isopen: false
        };
        $scope.toggleDropdown = function ($event) {
            $event.preventDefault();
            $event.stopPropagation();
            $scope.status.isopen = !$scope.status.isopen;
        };
        $scope.appendToEl = angular.element(document.querySelector('#dropdown-long-content'));

    }
});