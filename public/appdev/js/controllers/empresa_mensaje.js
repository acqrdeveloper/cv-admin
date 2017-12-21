/** Created by Gonzalo A. Del Portal Ch. on 01/06/17 **/
define(['app', 'roles'], function(app, roles) {
    app.controller('EmpresaMensajeCtrl', controller);
    controller.$inject = ['$uibModal','ListSrv','MensajeSrv','toastr'];
    function controller($uibModal, ListSrv, mySrv, toastr){
        var vm = this;
        var now = new Date();
        var lists = {
            asunto: [{id:"-",nombre:"Asunto"},{id:"Q", nombre:"Queja"},{id:"S",nombre:"Sugerencia"},{id:"M",nombre:"Mensaje"},{id:"H", nombre:"Horas"},{id:"A",nombre:"Auditorio"}],
            anio: ListSrv.years(),
            mes: ListSrv.months(),
            tipos: [{id:"received",nombre:"Recibido"},{id:"send",nombre:"Enviado"}]
        };
        var params = {
            anio: now.getFullYear(),
            asunto: '-',
            empresa_id: empresa.id,
            empleado: empresa.id,
            mes: now.getMonth() + 1,
            tipo: 'received'
        };
        var auxs = {
            asunto: {"Q":"Queja", "S":"Sugerencia", "M":"Mensaje", "H":"Horas", "A":"Auditorio", "E":""},
            empleados: {"2":"Administrador","3":"A. Cliente","4":"Cobranza"}
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

        function getData(){
            mySrv.get(params, params.tipo, 'C').then(function(r){
                vm.data = r.data.rows;
            }).catch(function(e){
                toastr.error(e.data.message);
            });
        }

        function showDetail(m){
            var mid = (m.padre_id>0)?m.padre_id:m.bandeja_id;
            $uibModal.open({
                animation: true,
                templateUrl: '/templates/modals/bandeja/detail.html',
                controller: ['$uibModalInstance', 'items', detailCtrl],
                controllerAs: 'ctrl',
                resolve: {
                    items: {'message_id': mid}
                }
            }).result.then(function(){}, function(){});
        }

        angular.extend(vm, {
            'auxs': auxs,
            'data': [],
            'lists': lists,
            'params': params,
            'getData': getData,
            'showDetail': showDetail
        });
    }
});