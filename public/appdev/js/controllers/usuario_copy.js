/**
 * Created by aquispe on 2017/05/24.
 */
define(['app', 'roles'], function (app, roles) {
    app.controller('UsuarioCtrl', controller);
    controller.$inject = ['$filter', '$http', '$stateParams', '$uibModal', 'toastr'];
    function controller($filter, $http, params, uimodal, toastr) {

        var vm = this;

        // variables configuracion
        vm.data = [];
        vm.dataPermisos = [];
        vm.dataModulos = [];
        vm.dataPermisos2 = [];
        vm.dataRoles = [];
        var mi_array_temp = [];
        vm.loadPermisos = [];
        vm.permisos_modificados = false;// no se modifico en al actualizacion
        
        vm.permissions = roles;

        vm.roles = {
            reserva: [],
            correspondencia: [],
            recado: [],
            mensajes: [],
            empresas: [],
            factura: [],
            inicio: []
        };

        // variables paginado
        vm.validateLoading = true;
        vm.validateFailedLoading = false;
        vm.totalItems = 0;
        vm.selection = [];

        // vista actual
        vm.view = {
            data: [],
            data2: [],
            validateLoading: true,
            validateFailedLoading: false,
            validateHideView: true
        };
        // vista para crear
        vm.viewCreate = {
            data: [],
            data2: [],
            validateHideView: true
        };
        // vista para actualizar
        vm.viewEdit = {
            data: [],
            data2: [],
            validateHideView: true
        };
        // vista para roles
        vm.viewRoles = {
            data: [],
            data2: [],
            validateHideView: true
        };

        // variables REQUEST y NG-MODEL
        vm.params = {
            id: '',
            datapermisos: {},
            nombre: '',
            login: '',
            contrasenia: '',
            email: '',
            modulo: '',
            asesor: 'N'
        };
        vm.params2 = {
            estado: 'A',
            limite: 20,
            pagina: 1
        };
        vm.params3 = [];

        vm.model = {
            id: '',
            arrayEstados: [
                {id: 'A', name: 'activos'},
                {id: 'E', name: 'eliminados'}
            ],
            selectedAll: false,
            modulo: {id: 3, name: 'Atención al Cliente / Recepción'},
            estado: {id: 'A', name: 'activos'},
            asesor: 'N'
        };

        function init() {
            fnGetListModulos();
            fnGetListAll();
            fnGetListPermisos(0);// listar para actualizar
            fnGetListRoles();
        }
        function fnChangeViewRoles(row) {

            vm.view.validateHideView = !vm.view.validateHideView;
            vm.viewRoles.validateHideView = !vm.viewRoles.validateHideView;
            if (!vm.viewRoles.validateHideView && !vm.view.validateHideView) {

                vm.model.id = row.id;
                var roles_del_usuario = angular.fromJson(row.roles);
                // var roles_del_usuario = angular.fromJson('{"reserva":{"add":"1","edit":"1","delete":"1","observation":"1"},"correspondencia":{"add":"1","edit":"1","delete":"","deliver":"","export":""},"recado":{"add":"","edit":"","delete":"","deliver":""},"mensajes":{"add":"","addConversation":""},"empresas":{"export":"","interview":"","cancelPay":"","nextDate":"","edit":"","central":"","service":"","contract":""},"factura":{"add":"","edit":"","extras":"","delete":"","addItem":"","pay":"","nota":"","cancel":"","sendMail":"","sunat":"","addNumberVoucher":""},"inicio":{"export":""}}');

                // ciclo para recorrer la lista de Roles que llegaron por REQUEST
                angular.forEach(vm.dataRoles, function (v, k) {

                    // ciclo para recorrer los roles del usuario ya siendo este un ARRAY
                    angular.forEach(roles_del_usuario, function (v1, k1) {

                        if (v.page == k1) {// si se ubica en la misma pagina de ROL
                            angular.forEach(v.action, function (v2, k2) {// ciclo para validar la posicion si es TRUE o FALSE
                                // conversion del objeto v1 a ARRAY
                                // get all object property names
                                var res = Object.keys(v1)
                                // iterate over them and generate the array
                                    .map(function (k) {
                                        // generate the array element
                                        return [k, v1[k]];
                                    });
                                if (v2.id == res[k2][0]) {
                                    console.info('valor de ROL');
                                    if (res[k2][1] == '1' || res[k2][1] == 1) {
                                        // console.log(1);
                                        v2.Selected = true;
                                    } else {
                                        // console.log(0);
                                        v2.Selected = false;
                                    }
                                }
                            });
                        }

                    });
                });
            } else {
                fnApplyChange();
                fnCleanForm();
                vm.params3 = [];
            }
        }
        function fnChangeViewCreate() {
            vm.view.validateHideView = !vm.view.validateHideView;
            vm.viewCreate.validateHideView = !vm.viewCreate.validateHideView;
            if (!vm.viewCreate.validateHideView && !vm.view.validateHideView) {
                fnGetListPermisos(1); // cargar permisos para crear
            } else {
                fnApplyChange();
                fnCleanForm();
            }
        }
        function fnChangeViewEdit(row) {
            vm.permisos_modificados = false;
            vm.view.validateHideView = !vm.view.validateHideView;
            vm.viewEdit.validateHideView = !vm.viewEdit.validateHideView;

            if (!vm.view.validateHideView && !vm.viewEdit.validateHideView) {
                vm.model.id = row.id;
                vm.model.nombre = row.nombre;
                vm.model.login = row.login;
                vm.model.contrasenia = row.contrasenia;
                vm.model.email = row.email;
                vm.model.modulo.id = row.modulo_id;
                vm.model.asesor = row.asesor;

                vm.dataPermisos2 = vm.dataPermisos;
                // vm.loadPermisos; // no lo limpiamos porque arrastra el nuevo dato
                // mi_array_temp=[];
                var obj = angular.fromJson(row.permisos);

                angular.forEach(vm.dataPermisos, function (v1, k1) {

                    angular.forEach(obj.modulosLeft, function (v2, k) {

                        if (Object.keys(v2.contenido).length >= 2) {// condicion para recorrer objeto con mas de 2 arrays

                            angular.forEach(v2.contenido, function (v, k) {// ciclo para mas de 2 arrays en objeto
                                if (v[0] !== undefined && v[0] == v1.pages) {
                                    angular.extend(vm.dataPermisos2[k1], v1.modulo + '|' + v1.nombre + '|' + v1.pages + '|' + v1.grupo + '|' + v1.place + '|' + (v2.checked == 'true') ? true : false);
                                    angular.extend(vm.dataPermisos2[k1], {Selected: (v2.checked == 'true') ? true : false});
                                    angular.extend(vm.dataPermisos2[k1], {checked: (v2.checked == 'true') ? true : false});
                                }
                            });

                        } else if (Object.keys(v2.contenido).length === 1) {

                            angular.forEach(v2.contenido, function (v, k) {// ciclo para 1 array en objeto
                                if (v[0] !== undefined && (v[0]*1) === (v1.pages*1)) {
                                    angular.extend(vm.dataPermisos2[k1], v1.modulo + '|' + v1.nombre + '|' + v1.pages + '|' + v1.grupo + '|' + v1.place + '|' + (v2.checked == 'true') ? true : false);
                                    angular.extend(vm.dataPermisos2[k1], {Selected: (v2.checked == 'true') ? true : false});
                                    angular.extend(vm.dataPermisos2[k1], {checked: (v2.checked == 'true') ? true : false});
                                }
                            });

                        }

                    });

                    angular.forEach(obj.modulosTop, function (v, k) {
                        if (v.pages !== undefined && (v.pages*1) === (v1.pages*1)) {
                            angular.extend(vm.dataPermisos2[k1], v1.modulo + '|' + v1.nombre + '|' + v1.pages + '|' + v1.grupo + '|' + v1.place + '|' + (v.checked == 'true') ? true : false);
                            angular.extend(vm.dataPermisos2[k1], {Selected: (v.checked == 'true') ? true : false});
                            angular.extend(vm.dataPermisos2[k1], {checked: (v.checked == 'true') ? true : false});
                        }
                    });
                });

                vm.params.datapermisos = {};
                vm.loadPermisos = [];

                angular.forEach(vm.dataPermisos2, function (item) {
                    vm.loadPermisos.push(item);
                });

                if (vm.loadPermisos.length) {
                    mi_array_temp = [];
                    angular.forEach(vm.loadPermisos, function (value, key) {
                        mi_array_temp.push(value.modulo + '|' + value.nombre + '|' + value.pages + '|' + value.grupo + '|' + value.place + '|' + value.checked);
                    });
                }

                // le pasamos la nueva lista
                vm.dataPermisos = vm.dataPermisos2;

            } else {
                fnApplyChange();
                fnCleanForm();
            }
        }
        function fnGetListPermisos(type) {
            vm.dataPermisos = [];
            vm.dataPermisos2 = [];
            vm.validateLoading = true;

            $http({
                url: '/permisos',
                method: 'GET',
                params: null
            }).then(function (r) {
                if (r.data.load) {

                    if (type == 1) {// create
                        angular.forEach(r.data.data, function (value, key) {
                            vm.dataPermisos.push(angular.extend(value, {checked: false, Selected: false}));
                        });
                        mi_array_temp = []; // vaciamos el array para recxargar cada vez que se cree un usuario en la vista CREATE
                        angular.forEach(vm.dataPermisos, function (v, k) {
                            mi_array_temp.push(v.modulo + '|' + v.nombre + '|' + v.pages + '|' + v.grupo + '|' + v.place + '|' + v.Selected);
                        });
                        // aqui data para enviar por POST
                        angular.extend(vm.params.datapermisos, mi_array_temp);

                    } else if (type === 0) {// update
                        vm.dataPermisos = r.data.data;
                    }

                }
            }).catch(function (r) {
                alert('ERROR, contactar al administrador');
                console.error(r);
                event.preventDefault();
            });

        }
        function fnGetListModulos() {
            vm.validateLoading = true;
            $http({
                url: '/modulos',
                method: 'GET',
                params: null
            }).then(function (r) {
                if (r.data.load) {
                    vm.dataModulos = r.data.data;
                }
            }).catch(function (r) {
                alert('ERROR, contactar al administrador');
                console.error(r);
                event.preventDefault();
            });
        }
        function fnGetListAll() {
            vm.validateLoading = true;
            $http({
                url: '/usuario/all',
                method: 'GET',
                params: vm.params2
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
        function fnCheckSelected(row) {
            vm.permisos_modificados = true;

            if (vm.loadPermisos.length) {// si la lista esta con checks marcados (aplicado en el actualizar)
                console.info('loadPermisos.length = TRUE');
                for (var i = 0; i < vm.loadPermisos.length; i++) {

                    if (row.Selected === true || row.Selected === 'true') {// si esta seleccionado o checked el NG-MODEL
                        console.info('SI ES TRUE');
                        if (vm.loadPermisos[i].pages == row.pages) {
                            mi_array_temp[i] = row.modulo + '|' + row.nombre + '|' + row.pages + '|' + row.grupo + '|' + row.place + '|' + row.Selected;
                        }
                    } else {// sino esta seleccionado
                        console.info('SI ES FALSE');
                        if (vm.loadPermisos[i].pages == row.pages) {
                            mi_array_temp[i] = row.modulo + '|' + row.nombre + '|' + row.pages + '|' + row.grupo + '|' + row.place + '|' + row.Selected;
                        }
                    }

                }
            } else {// si la lista esta sin checks (ningun marcado)(aplicado para crear)
                console.info('loadPermisos.length = FALSE');
                // console.log(vm.dataPermisos);
                angular.forEach(vm.dataPermisos, function (v, k) {

                    if (row.Selected === true || row.Selected === 'true') {// si esta seleccionado
                        console.info('SI ES TRUE');
                        if (v.pages == row.pages) {
                            mi_array_temp[k] = row.modulo + '|' + row.nombre + '|' + row.pages + '|' + row.grupo + '|' + row.place + '|' + row.Selected;
                        }
                    } else {// sino esta seleccionado
                        console.info('SI ES FALSE');
                        if (v.pages == row.pages) {
                            mi_array_temp[k] = row.modulo + '|' + row.nombre + '|' + row.pages + '|' + row.grupo + '|' + row.place + '|' + row.Selected;
                        }
                    }

                });
            }
            // console.log(mi_array_temp);// mostrar salida de datos para el REQUEST.
            // aqui data para enviar por POST o PUT
            angular.extend(vm.params.datapermisos, mi_array_temp);

        }
        function fnCheckSelectedAll(type) {
            console.info('aplico checked TODOS');// si no estan seleccionado todos (no checked en TODOS)
            vm.permisos_modificados = true;
            vm.params.datapermisos = {};
            vm.loadPermisos = [];

            if (type == 1) {// create
                angular.forEach(vm.dataPermisos, function (item) {
                    item.Selected = vm.model.selectedAll;
                    item.checked = vm.model.selectedAll;
                    vm.loadPermisos.push(item);
                });
            } else if (type === 0) {// update
                angular.forEach(vm.dataPermisos2, function (item) {
                    item.Selected = vm.model.selectedAll;
                    item.checked = vm.model.selectedAll;
                    vm.loadPermisos.push(item);
                });
            }

            // array de objetos seleccionados
            if (vm.loadPermisos.length) {
                mi_array_temp = [];// array temporal de seleccionados | obligado limpiar o inicializar en vacio
                angular.forEach(vm.loadPermisos, function (value, key) {
                    mi_array_temp.push(value.modulo + '|' + value.nombre + '|' + value.pages + '|' + value.grupo + '|' + value.place + '|' + value.checked);
                });
            }

            if (!vm.model.selectedAll) {
                console.info('quito checked TODOS');
                // mi_array_temp; // array temporal en actividad no borrar
                vm.params.datapermisos = {};

                angular.forEach(vm.loadPermisos, function (v, k) {
                    mi_array_temp[k] = v.modulo + '|' + v.nombre + '|' + v.pages + '|' + v.grupo + '|' + v.place + '|' + false;
                });
            }

            // aqui data para enviar por POST o PUT
            angular.extend(vm.params.datapermisos, mi_array_temp);

        }
        function fnStore() {

            vm.params.nombre = vm.model.nombre;
            vm.params.login = vm.model.login;
            vm.params.contrasenia = vm.model.contrasenia;
            vm.params.email = vm.model.email;
            vm.params.modulo = vm.model.modulo.id;
            vm.params.asesor = vm.model.asesor;

            $http({
                url: '/usuario/store',
                method: 'POST',
                params: vm.params,
                dataType: 'json'
            }).then(function (r) {
                if (r.data.load) {
                    console.log(r.data);
                }
            }).catch(function (r) {
                alert('ERROR, contácte al administrador');
                console.error(r);
                event.preventDefault();
            }).finally(function () {
                fnChangeViewCreate();
            });
        }
        function fnUpdate() {

            vm.params.nombre = vm.model.nombre;
            vm.params.login = vm.model.login;
            vm.params.contrasenia = vm.model.contrasenia;
            vm.params.email = vm.model.email;
            vm.params.modulo = vm.model.modulo.id;
            vm.params.asesor = vm.model.asesor;

            /* vm.permisos_modificados = FALSE */
            // si no hay modificacion en los permisos envia los datos por defecto en "CHECKED = true"
            if (!vm.permisos_modificados) {
                vm.params.datapermisos = {};
                vm.loadPermisos = [];

                angular.forEach(vm.dataPermisos2, function (item) {
                    vm.loadPermisos.push(item);
                });

                if (vm.loadPermisos.length) {
                    mi_array_temp = [];
                    angular.forEach(vm.loadPermisos, function (value, key) {
                        mi_array_temp.push(value.modulo + '|' + value.nombre + '|' + value.pages + '|' + value.grupo + '|' + value.place + '|' + value.checked);
                    });
                }
                // aqui data para enviar por POST o PUT
                angular.extend(vm.params.datapermisos, mi_array_temp);
            }

            $http({
                url: 'usuario/update/' + vm.model.id,
                method: 'PUT',
                params: vm.params,
                dataType: 'json'
            }).then(function (r) {
                if (r.data.load) {
                    console.log(r.data);
                }
            }).catch(function (r) {
                alert('ERROR, contácte al administrador');
                console.error(r);
                event.preventDefault();
            }).finally(function () {
                fnChangeViewEdit();
            });
        }
        function fnUpdateRoles() {

            if (vm.params3.length) {
                $http({
                    url: '/usuario/update/roles/' + vm.model.id,
                    method: 'PUT',
                    params: vm.params3,
                    dataType: 'json'
                }).then(function (r) {
                    if (r.data.load) {
                        console.log(r.data);
                    }
                }).catch(function (r) {
                    alert('ERROR, contácte al administrador');
                    console.error(r);
                    event.preventDefault();
                }).finally(function () {
                    vm.params3 = [];
                    fnChangeViewRoles();
                });
            } else {
                alert('ATENCION, no se han ingresados cambios!');
                event.preventDefault();
            }

        }
        function fnApplyChange() {
            vm.params2.estado = vm.model.estado.id;
            fnGetListAll();
        }
        function fnCleanForm() {
            vm.model.id = '';
            vm.model.nombre = '';
            vm.model.login = '';
            vm.model.contrasenia = '';
            vm.model.email = '';
            vm.model.modulo.id = 3;
            vm.model.asesor = 'N';
            vm.model.datapermisos = {};
        }
        function fnGetListRoles() {
            vm.dataRoles = [];
            $http({
                url: '/usuario/roles',
                method: 'GET',
                params: null
            }).then(function (r) {
                if (r.data.load) {
                    angular.forEach(r.data.data, function (v, k) {
                        v.action = angular.fromJson(v.action);// parseamos el JSON

                        angular.forEach(v.action, function (v2) {
                            angular.extend(v2, {Selected: false});
                        });
                        vm.dataRoles.push(v);
                    });
                } else {
                    throw (r.data.load);
                }
            }).catch(function (r) {
                alert('ERROR, contactar al administrador');
                console.error(r);
                event.preventDefault();
            });
        }
        function fnCheckRole(colum, subcolum) {
            vm.params3 = [];

            if (subcolum.Selected === undefined || subcolum.Selected === null) {
                subcolum.Selected = true;
            }
            vm.roles = {
                reserva: [],
                correspondencia: [],
                recado: [],
                mensajes: [],
                empresas: [],
                factura: [],
                inicio: []
            };

            angular.forEach(vm.dataRoles, function (v, k) {

                if (v.page == 'reserva') {
                    angular.forEach(v.action, function (v1, k1) {
                        vm.roles.reserva.push({name: v1.id, condicion: (v1.Selected) ? '1' : ''});
                    });
                    return false;
                } else if (v.page == 'correspondencia') {
                    angular.forEach(v.action, function (v1, k1) {
                        vm.roles.correspondencia.push({name: v1.id, condicion: (v1.Selected) ? '1' : ''});
                    });
                } else if (v.page == 'recado') {
                    angular.forEach(v.action, function (v1, k1) {
                        vm.roles.recado.push({name: v1.id, condicion: (v1.Selected) ? '1' : ''});
                    });
                } else if (v.page == 'mensajes') {
                    angular.forEach(v.action, function (v1, k1) {
                        vm.roles.mensajes.push({name: v1.id, condicion: (v1.Selected) ? '1' : ''});
                    });
                } else if (v.page == 'empresas') {
                    angular.forEach(v.action, function (v1, k1) {
                        vm.roles.empresas.push({name: v1.id, condicion: (v1.Selected) ? '1' : ''});
                    });
                } else if (v.page == 'factura') {
                    angular.forEach(v.action, function (v1, k1) {
                        vm.roles.factura.push({name: v1.id, condicion: (v1.Selected) ? '1' : ''});
                    });
                } else if (v.page == 'inicio') {
                    angular.forEach(v.action, function (v1, k1) {
                        vm.roles.inicio.push({name: v1.id, condicion: (v1.Selected) ? '1' : ''});
                    });
                }

            });

            vm.params3.push(vm.roles);

        }
        function fnChangeEstado(row, estado) {

            if (row.estado != estado) {
                var msg = '';
                if (estado == 'A') {
                    msg = 'ACTIVAR este usuario?';
                } else {
                    msg = 'ELIMINAR este usuario?';
                }
                if (confirm('¿esta segura de ' + msg)) {
                    $http({
                        url: '/usuario/update/estado/' + row.id,
                        method: 'PUT',
                        params: {estado: estado},
                        dataType: 'json'
                    }).then(function (r) {
                        if (r.data.load) {
                            console.log(r.data);
                        }
                    }).catch(function (r) {
                        alert('ERROR, contácte al administrador');
                        console.error(r);
                        event.preventDefault();
                    }).finally(function () {
                        fnGetListAll();
                    });
                } else {
                    event.preventDefault();
                }

            } else {
                alert('ERROR 2, contácte al administrador');
            }

        }

        vm.fnChangeViewCreate = fnChangeViewCreate;
        vm.fnChangeViewEdit = fnChangeViewEdit;
        vm.fnChangeViewRoles = fnChangeViewRoles;
        vm.fnCheckSelected = fnCheckSelected;
        vm.fnCheckSelectedAll = fnCheckSelectedAll;
        vm.fnStore = fnStore;
        vm.fnUpdate = fnUpdate;
        vm.fnApplyChange = fnApplyChange;
        vm.fnGetListRoles = fnGetListRoles;
        vm.fnCheckRole = fnCheckRole;
        vm.fnUpdateRoles = fnUpdateRoles;
        vm.fnChangeEstado = fnChangeEstado;

        init();

    }
});