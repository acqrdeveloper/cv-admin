/**
 * Created by QuispeRoque on 21/04/17.
 */
define(['app', 'roles', 'angular', 'appdev/js/util'], function (app, roles, angular, Util) {

    return app.controller('MensajeCtrl', ['$http', '$scope', '$timeout', '$filter', 'toastr', 'listYears', 'listMonths', 'filterCompany', function ($http, $scope, $timeout, $filter, toastr, listYears, listMonths, filterCompany) {

        //globales
        $scope.data = [];
        $scope.validateLoading = true;
        $scope.validateFailedLoading = false;
        $scope.totalItems = 0;
        $scope.selection = [];
        $scope.params = {
            anio: new Date().getUTCFullYear(),
            mes: new Date().getUTCMonth() + 1,
            estado: 'A',
            respondido: '-',
            leido: '-',
            empresa_id: 0,
            tipo: 1,
            limite: 20,
            pagina: 1
        };
        $scope.filters = {
            years: listYears,
            months: listMonths,
            arrayRespondidos: [
                {id: '-', name: 'todos'},
                {id: 1, name: 'respondido'},
                {id: 2, name: 'no respondido'}
            ],
            arrayLeidos: [
                {id: '-', name: 'todos'},
                {id: 1, name: 'leido'},
                {id: 2, name: 'no leido'}
            ],
            arrayTipos: [
                {id: 1, name: 'recibido'},
                {id: 2, name: 'enviado'}
            ],
            arrayEstados: [
                {id: 'A', name: 'activos'},
                {id: 'E', name: 'eliminados'}
            ],
            filterCompanies: filterCompany,
            selected:''
        };
        $scope.filters.selectedYear = new Date().getUTCFullYear();
        $scope.filters.selectedMonth = new Date().getUTCMonth() + 1;
        $scope.filters.selectedRespondido = {id: '-', name: 'todos'};
        $scope.filters.selectedLeido = {id: '-', name: 'todos'};
        $scope.filters.selectedTipo = {id: 1, name: 'recibido'};
        $scope.filters.selectedEstado = {id: 'A', name: 'A'};

        //metodos
        $scope.methods = {
            init: function () {
                $scope.methods.getData();
            },
            getData: function () {
                $scope.validateLoading = true;
                $http({
                    url: '/list-conversations',
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
            conversations: function (row_mensaje_id, row) {
                view.validateLoading = true;
                $http({
                    url: '/conversations',
                    method: 'GET',
                    params: {mensaje_id: row_mensaje_id}
                }).then(function (r) {
                    if (r.data.load) {
                        if (r.data.rows.length > 0) {
                            view.validateLoading = false;//muestra la respuesta
                            view.validateFailedLoading = false;//muestra la respuesta
                            view.data2 = Object.assign({row_response: r.data.rows});//objeto para la carga de respuesta
                            view.data = Object.assign({row_request: row});//objeto para la carga de pregunta
                        } else {
                            if (Object.keys(row).length > 0) {
                                view.validateLoading = false;//muestra notificacion de no hay respuesta
                                view.validateFailedLoading = true;//muestra notificacion de no hay respuesta
                                view.data2 = Object.assign({row_response: {}});//objeto para la carga de respuesta
                                view.data = Object.assign({row_request: row});//objeto para la carga de pregunta
                            } else {
                                view.validateLoading = true;
                                view.validateFailedLoading = false;
                                view.data = [];
                                view.data2 = [];
                            }
                        }
                    }
                }, Util.errorRpta);
            },
            conversation: function () {
                var dataObject = {
                    mensaje_id: document.getElementsByName('mensaje_id')[0].value,
                    respuesta: view.subfilters.respuesta,
                };
                $http({
                    url: '/conversation',
                    method: 'POST',
                    params: dataObject,
                    dataType: 'json'
                }).then(function (r) {
                    if (r.data.load) {
                        toastr.success(r.data.msg, 'CORRECTO');
                    }
                }, Util.errorRpta).finally(function () {
                    $scope.methods.applyClean();
                });
            },
            applyChange: function () {
                $scope.validateFailedLoading = false;
                $scope.params.anio = $scope.filters.selectedYear;
                $scope.params.mes = $scope.filters.selectedMonth;
                $scope.params.respondido = $scope.filters.selectedRespondido.id;
                $scope.params.leido = $scope.filters.selectedLeido.id;
                $scope.params.tipo = $scope.filters.selectedTipo.id;
                $scope.params.estado = $scope.filters.selectedEstado.id;
                $scope.params.empresa_id = $scope.filters.selected.id;
                $scope.methods.getData();
            },
            applyClean: function () {
                view.subfilters.respuesta = '';
                $scope.methods.conversations(document.getElementsByName('mensaje_id')[0].value, view.data.row_request);
            },
            clickHideView: function (row_mensaje_id, row) {
                view.data = [];
                view.data2 = [];
                view.validateHideView = !view.validateHideView;
                if (!view.validateHideView) {
                    $scope.methods.conversations(row_mensaje_id, row);
                }
            },
            clearCompanies: function () {
                $scope.filters.selected = '';
                $scope.params.empresa_id = 0;
                $scope.methods.applyChange();
            }
        };
        var view = {
            data: [],
            data2: [],
            validateLoading: true,
            validateFailedLoading: false,
            validateHideView: true,
            filters: {
                param_select: '1',
                param_select2: '2',
            },
            subfilters: {
                respuesta: ''
            }
        };

        //inicializados
        $scope.methods.init();
        $scope.view = view;

    }]);
});

