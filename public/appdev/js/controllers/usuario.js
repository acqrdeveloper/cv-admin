/**
 * created by kevin baylon <kbaylonh@outlook.com>
 */
define(['app','roles'], function(app, roles){

    app.controller('UsuarioCtrl', controller);

    controller.$inject = ['$http', 'toastr'];

    function controller($http, toastr){
        var vm = this;

        var auxs = {
            estados: [
                {id: 'A', name: 'Activos'},
                {id: 'E', name: 'Eliminados'}
            ],
            search: {
                estado: 'A',
                pagina: 1,
                limite: 20
            },
            saving: false,
            searching: false,
            viewForm: false,
            roles: angular.copy(window.iVariables.roles)
        };

        auxs.roles.unshift({id:0, nombre:'Sin rol'});

        var fn = {
            changeState: changeState,
            filter: filter,
            search: search,
            init: init,
            openForm: openForm,
            save: save
        };

        var params = {
            nombre: '',
            email: '',
            asesor: 'N',
            login: '',
            contrasenia: '',
            rol_id: 2,
        };

        var table = [];

        var permissions = roles;

        angular.extend(vm, {
            'auxs': auxs,
            'fn': fn,
            'params': params,
            'permissions': permissions,
            'table': table,
        });

        function changeState(user, state){
            if(confirm('¿Deseas realizar este cambio en el usuario ' + user.nombre + '?')){
                $http({
                    url: '/usuario/' + user.id + '/estado',
                    method: 'PUT',
                    data: {estado:state}
                }).then(function(r){
                    toastr.success(r.data.message);
                    filter();
                }).catch(function(e){
                    toastr.error(e.data.error);
                }).finally(function(){
                });
            }
        }

        function filter(){
            auxs.search.pagina = 1;
            search();
        }

        function init(){
            search();
        }

        function openForm(user){
            // Means that user info will be edited
            if(user !== undefined){
                params.id = user.id;
                params.nombre = user.nombre;
                params.login = user.login;
                params.email = user.email;
                params.asesor = user.asesor;
                params.rol_id = user.rol_id;
                params.contrasenia = user.contrasenia;
            } else {
                params.nombre = '';
                params.login = '';
                params.email = '';
                params.asesor = 'N';
                params.contrasenia = '';
                params.rol_id = 2;

                delete params.id;
            }

            console.log(params);

            auxs.viewForm = !auxs.viewForm;
        }

        function search(){
            vm.auxs.searching = true;
            $http({
                url: '/usuario/all',
                method: 'GET',
                params: auxs.search
            }).then(function (r) {
                if (r.data.load) {
                    vm.table = r.data.rows;
                    vm.totalItems = r.data.total;
                    vm.selection = [];
                } else {
                    vm.totalItems = 0;
                    vm.table = [];
                }
                vm.auxs.searching = false;
            }).catch(function (r) {
            });
        }

        function save(){
            auxs.saving = true;

            var u = "/usuario"; var m = "POST";

            if(params.id !== undefined && params.id>0){
                u =  '/usuario/' + params.id;
                m = "PUT";
            }

            $http({
                url: u,
                method: m,
                data: params
            }).then(function(r){
                toastr.success(r.data.message);
                openForm();
                vm.auxs.search.estado = 'A';
                filter();
            }).catch(function(e){
                toastr.error(e.data.error);
            }).finally(function(){
                vm.auxs.saving = false;
            });
        }
    }
});