/**  Created by Gonzalo A. Del Portal Ch. on 30/05/17. **/
define(['app', 'roles'], function(app, roles) {
    app.controller('BandejaCtrl', BandejaCtrl);
    BandejaCtrl.$inject = ['$filter', '$timeout', '$uibModal', 'EmpresaSrv', 'ListSrv', 'MensajeSrv', 'toastr'];
    function BandejaCtrl($filter, $timeout, $uibModal, EmpresaSrv, ListSrv, mySrv, toastr) {
        var vm = this;
        var data = [];
        var main = true;
        var now = new Date();
        var auxs = {
            asunto: {"Q":"Queja", "S":"Sugerencia", "M":"Mensaje", "H":"Horas", "A":"Auditorio", "E":""},
            empleados: {"1":"Sistema","2":"Administrador","3":"A. Cliente","4":"Cobranza"},
            selected: '',
            totalItems: 0,
            validateLoading: true
        };
        var lists = {
            asunto: [{id:"-",nombre:"Asunto"},{id:"Q", nombre:"Queja"},{id:"S",nombre:"Sugerencia"},{id:"M",nombre:"Mensaje"},{id:"H", nombre:"Horas"},{id:"A",nombre:"Auditorio"}],
            ciclo: ListSrv.ciclos(),
            empleado: [{id:0,nombre:"Empleados"},{id:2, nombre:"Administrador"},{id:3,nombre:"A. Cliente"},{id:4,nombre:"Cobranza"}],
            estadosEmpresa: ListSrv.getEstadosEmpresa(),
            months: ListSrv.months(),
            planes: angular.copy(window.iVariables.planes),
            years: ListSrv.years()
        };
        lists.planes.unshift({ id: "-", nombre: "Plan" });
        var searchParams  = {
            anio: now.getUTCFullYear(),
            asunto: '-',
            ciclo: '-',
            empleado: 0,
            empresa_id: 0,
            mes: now.getUTCMonth() + 1,
            preferencia_estado: '-',
            limite: 20,
            pagina: 1,
            plan:'-'
        };
        var methods = {
            clearCompanies: function clearCompanies(){
                auxs.selected = '';
                searchParams.empresa_id = 0;
                methods.search();
            },
            filterCompanies: function filterCompanies(value, onlyActive){
                if (value === '' || value.length < 3) {
                    return [];
                } else {
                    var params = { view: 'minimal', empresa_nombre: value };
                    if (onlyActive === 1) {
                        params.estado = 'A,S,X';
                    }
                    return EmpresaSrv.search(params);
                }
            },
            filterData: function filterData(){
                searchParams.pagina = 1; methods.search();
            },
            openMessage: function openMessage(){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/bandeja/create.html',
                    controller: ['$uibModalInstance', modalCtrl],
                    controllerAs: 'ctrl'
                }).result.then(function(){}, function(){});
            },
            search: function search(){
                auxs.validateLoading = true;
                mySrv.get(searchParams, 'received', 'E').then(function(r) {
                    if (r.data.load) {
                        if (r.data.rows.length > 0) {
                            vm.data = r.data.rows;
                            vm.auxs.totalItems = r.data.total;
                            vm.data.forEach(function( val, i ){
                                vm.data[i].extra = {};
                                if( val.asunto === "H" ){
                                    try {
                                        js = JSON.parse( val.mensaje );
                                        vm.data[i].mensaje = js.horas + " horas, " +js.obs;
                                        vm.data[i].extra = js;
                                    } catch (e) {
                                        console.log( i, val.mensaje );
                                    }
                                }
                            });
                        } else {
                            vm.data = [];
                            vm.auxs.totalItems = 0;
                        }
                    }
                }).catch(function(response) {
                    toastr.error('No se pudo obtener datos de la Bandeja.');
                    console.log( response );
                }).finally(function() {
                    vm.auxs.validateLoading = false;
                });
            },
            showDetail: function showDetail(m){

                // Make read
                if((m.leido*1) === 0){
                    mySrv.makeRead(m.bandeja_id);
                }

                var mid = (m.padre_id>0)?m.padre_id:m.bandeja_id;
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/bandeja/detail.html',
                    controller: ['$uibModalInstance', 'items', detailCtrl],
                    controllerAs: 'ctrl',
                    resolve: {
                        items: {'message_id': mid, 'empresa_id': m.empresa_id, 'de': m.a}
                    }
                }).result.then(function(){}, function(){});
            }
        };

        function detailCtrl(instance, items){
            var ctrl = this;
            ctrl.close = function(){
                instance.dismiss('close');
            };
            ctrl.messages = [];
            ctrl.loading = false;
            ctrl.sending = false;
            ctrl.empleados = auxs.empleados;

            ctrl.params = {
                a: items.empresa_id,
                asunto: 'M',
                a_tipo: 'C',
                de: items.de,
                de_tipo: 'E',
                empresa_id: items.empresa_id,
                mensaje: '',
                padre_id: items.message_id
            };

            ctrl.send = function send(){
                console.log(ctrl.params);
                ctrl.sending = true;
                // url: /bandeja/mensaje
                var p =  angular.copy(ctrl.params);
                mySrv.sendMessage(p).then(function(r){
                    toastr.success(r.data.message);
                    search();
                    ctrl.params.mensaje = '';
                }).catch(function(e){
                    toastr.error(e.data.message);
                }).finally(function(){
                    ctrl.sending = false;
                });
            };

            ctrl.sendReponse = function sendReponse(m, response){
                ctrl.sending = true;
                mySrv.sendReponse(m.empresa_id, m.bandeja_id, response).then(function(r){
                    toastr.success(r.data.message);
                    search();
                }).catch(function(e){
                    toastr.error(e.data.message);
                }).finally(function(){ctrl.sending = false;});
            };

            function search(){
                ctrl.loading = true;
                mySrv.getDetails(items.message_id).then(function(r){
                    ctrl.messages = r.data.rows;
                    for(var i = 0; i< ctrl.messages.length; i++){
                        if(ctrl.messages[i].asunto === 'H' || ctrl.messages[i].asunto === 'A'){
                            ctrl.messages[i].mensaje = JSON.parse(ctrl.messages[i].mensaje);
                        }
                    }
                }).finally(function(){ctrl.loading = false;});
            }

            search();
        }

        function modalCtrl(instance){
            var ctrl = this;
            ctrl.sending = false;
            ctrl.empleados = [{id:0,nombre:"Empleados"},{id:2, nombre:"Administrador"},{id:3,nombre:"A. Cliente"},{id:4,nombre:"Cobranza"}];
            ctrl.params = {
                empresa_id: 0,
                a: 0,
                asunto: 'M',
                a_tipo: 'C',
                de_tipo: 'E',
                mensaje: ''
            };
            ctrl.params.a = ctrl.params.empresa_id;
            ctrl.close = function(){
                instance.dismiss('close');
            };
            ctrl.filterCompanies = function filterCompanies(value, onlyActive){
                if (value === '' || value.length < 3) {
                    return [];
                } else {
                    var params = { view: 'minimal', empresa_nombre: value };
                    if (onlyActive === 1) {
                        params.estado = 'A,S,X';
                    }
                    return EmpresaSrv.search(params);
                }
            };
            ctrl.clearSelection = function(){
                ctrl.selected = '';
                ctrl.params.empresa_id = 0;
            };
            ctrl.send = function send(){
                console.log(ctrl.params);
                ctrl.sending = true;
                // url: /bandeja/mensaje
                var p =  angular.copy(ctrl.params); p.a = p.empresa_id;
                mySrv.sendMessage(p).then(function(r){
                    toastr.success(r.data.message);
                    methods.search();
                    ctrl.close();
                }).catch(function(e){
                    toastr.error(e.data.message);
                }).finally(function(){
                    ctrl.sending = false;
                });
            };
        }

        angular.extend(vm, {
            'auxs': auxs,
            'lists': lists,
            'main': main,
            'methods': methods,
            'searchParams': searchParams,
            'roles': roles
        });
    }
});
