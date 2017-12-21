/**
 * Created by aquispe on 2017/06/12.
 */
define(['app', 'roles'], function (app, roles) {

    app.controller('EmpresaHistorialCtrl', controller);

    controller.$inject = ['$filter', '$http', '$stateParams', '$uibModal', 'toastr'];

    function controller($filter, $http, params, uimodal, toastr) {

        // variable herencia
        var vm = this;
        vm.data = [];

        // variables paginado
        vm.validateLoading = true;
        vm.validateFailedLoading = false;
        vm.totalItems = 0;
        vm.selection = [];
        // variable REQUEST
        vm.params = {
            empresa_id: empresa.id,
            tipo: 1,
            limite: 20,
            pagina: 1
        };
        vm.model = {
            arrayTipo: [
                {id: 1, name: 'historial empresa'},
                {id: 2, name: 'historial contrato'},
                {id: 3, name: 'historial consumo'}
            ],
            tipo: {id: 1, name: 'historial empresa'}
        };

        function init() {
            fnGetListTipoHistorial();
        }
        function fnApplyChange() {
            switch (parseInt(vm.model.tipo.id)) {
                case 2:
                    angular.extend(vm.params, {recurso_id: empresa.id});
                    vm.params.tipo = 2;
                    break;
                case 3:
                    angular.extend(vm.params, {contrato_id: empresa.id});
                    vm.params.tipo = 3;
                    break;
                default :
                    angular.extend(vm.params, {empresa_id: empresa.id});
                    vm.params.tipo = 1;
                    break;
            }
            fnGetListTipoHistorial();
        }
        function fnGetListTipoHistorial() {
            vm.validateLoading = true;
            $http({
                url: '/empresa/servicio/' + empresa.id + '/historial',
                method: 'GET',
                params: vm.params
            }).then(function (r) {
                if (r.data.load) {
                    vm.validateLoading = false;
                    vm.validateFailedLoading = false;
                    vm.data = r.data.rows;
                    vm.totalItems = r.data.total;
                    vm.selection = [];
                } else {
                    vm.totalItems = 0;
                    vm.data = [];
                    vm.validateLoading = false;
                    vm.validateFailedLoading = true;
                }
            }).catch(function (r) {
                alert('ERROR, contactar al administrador');
                console.error(r);
                event.preventDefault();
            });
        }
        vm.fnApplyChange = fnApplyChange;

        init();
    }

});