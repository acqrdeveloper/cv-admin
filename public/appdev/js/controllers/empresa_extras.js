define(['app', 'roles'], function (app, roles) {
    app.controller('EmpresaExtrasCtrl', controller);
    controller.$inject = ['$filter', '$http', '$stateParams', '$uibModal', 'toastr'];
    function controller($filter, $http, params, uimodal, toastr) {
        var vm = this;
        vm.params = {};
        vm.auxs = {
            extras: ([{ "id":"-", "nombre":"Extras" }]).concat( angular.copy( iVariables.extras ) )
        };
        vm.methods = {
            init:function init(){
                $http({
                    url: '/empresas/' + empresa.id + '/extras',
                    method: 'GET'
                }).then(function(r){
                    if( r.status == 200 ){
                        r.data.forEach(function(x){
                            vm.params[x.extra_id] = x.estado;
                        });
                    }
                }).catch(function(e){
                    toastr.error(e.data.message);
                });
            },insertUpdate:function insertUpdate( extra_id, estado ){
                $http({
                    url: '/empresas/' + empresa.id + '/extras',
                    method: 'POST',
                    data: {'extra_id':extra_id, 'estado':estado} 
                }).then(function(r){
                    if( r.status == 200 ){
                        toastr.success("Actualizado");
                    }
                }).catch(function(e){
                    toastr.error(e.data.message);
                });
            }
        };
        vm.methods.init();
    }
});