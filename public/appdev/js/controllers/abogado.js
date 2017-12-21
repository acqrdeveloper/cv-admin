/**
 * Created by QuispeRoque on 02/05/17.
 */
define([
    'app',
    'roles',
    'appdev/js/services/abogado'
], function (app, roles) {

    app.controller('AbogadoCtrl', abogado_controller);

    abogado_controller.$inject = ['$uibModal','AbogadoSrv', 'CorrespondenciaSrv','listYears', 'listMonths', 'filterCompany', 'toastr'];

    function abogado_controller(uibmodal, dataservice, CorresSrv, listYears, listMonths, filterCompany, toastr) {

        var now = new Date();
        //variables
        var vm = this;
        vm.roles = roles;
        vm.data = [];
        vm.validateLoading = true;
        vm.validateFailedLoading = false;
        vm.totalItems = 0;
        vm.selection = [];
        vm.params = {
            anio: now.getUTCFullYear(),
            mes: now.getUTCMonth() + 1,
            estado: '-',
            limite: 20,
            pagina: 1,
            empresa_id: 0
        };
        vm.filters = {
            years: listYears,
            months: listMonths,
            filterCompanies: filterCompany,
            arrayEstados: [
                {id: '-', name: 'todos'},
                {id: 'A', name: 'activo'},
                {id: 'C', name: 'concluido'},
                {id: 'E', name: 'eliminado'}
            ],
            arrayCasos: [
                {id: '1', name: 'demandado'},
                {id: '2', name: 'demandante'},
                {id: '3', name: 'demandante / demandado'}
            ],
            selected: ''
        };
        vm.filters.selectedYear = now.getUTCFullYear();
        vm.filters.selectedMonth = now.getUTCMonth() + 1;
        vm.filters.selectedEstado = {id: '-', name: 'todos'};
        vm.filters.selectedCaso = {id: '1', name: 'demandado'};

        //metodos
        vm.methods = {
            applyChange: function () {
                vm.subparams = {
                    anio: vm.filters.selectedYear,
                    mes: vm.filters.selectedMonth,
                    estado: vm.filters.selectedEstado.id,
                    empresa_id: vm.filters.selected.id,
                    tipofiltro: vm.filters.selectedCaso.id,
                    filtro: vm.filters.filtro,
                    limite: vm.params.limite,
                    pagina: vm.params.pagina
                };
                fnGetAllData(vm.subparams);
            },
            clearCompanies: function () {
                vm.filters.selected = '';
                vm.params.empresa_id = 0;
                vm.methods.applyChange();
            }
        };

        vm.openModal = function openModal(row){
            uibmodal.open({
                animation: true,
                templateUrl: '/templates/modals/correspondencia/create.html',
                controller: ['$uibModalInstance','items', modalController],
                controllerAs: '$ctrl',
                resolve: {
                    items: function(){
                        return {edit:true, row: angular.copy(row)};
                    }
                }
            }).result.then(function(){}, function(){});
        };

        //funciones javascript
        activate();
        function activate() {
            fnGetAllData(vm.params);
        }

        function fnGetAllData(params) {
            vm.data = [];
            vm.validateLoading = true;
            vm.validateFailedLoading = false;
            dataservice.getAllData(params)
                .then(function (r) {
                    if (r.load) {
                        if (r.rows.length > 0) {
                            vm.validateLoading = false;
                            vm.validateFailedLoading = false;
                            vm.data = r.rows;
                            vm.totalItems = r.total;
                            vm.selection = [];
                        } else {
                            vm.totalItems = 0;
                            vm.data = [];
                            vm.validateLoading = false;
                            vm.validateFailedLoading = true;
                        }
                    }
                });
        }

        function modalController(instance, items){
            console.log(items.row);
            var $ctrl = this;
            $ctrl.cc = false;
            $ctrl.ccText = "Cc: Enviar una copia a otro correo";
            $ctrl.edit = items.edit;
            $ctrl.locales = window.iVariables.locales;
            $ctrl.params = {
                empresa_id: items.row.empresa_id,
                empresa_nombre: items.row.empresa_nombre,
                remitente: items.row.demandado
            };
            $ctrl.sending = false;
            $ctrl.close = function(){
                instance.dismiss('cancel');
            };
            $ctrl.showCC = function(evt){
                $ctrl.cc = !$ctrl.cc;
            };
            $ctrl.send = function(){
                $ctrl.sending = true;
                CorresSrv.send($ctrl.params).then(function(response){
                    toastr.success("Correspondencia creada", "Exito");
                    $ctrl.close();
                }).catch(function(response){
                    toastr.error(response.data);
                }).finally(function(){
                    $ctrl.sending = false;
                });
            };
        }
    }
});