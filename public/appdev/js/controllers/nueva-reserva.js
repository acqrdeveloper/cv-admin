define(['app','angular'], function(app, angular){
	app.controller('NuevaReservaCtrl', controller);
	controller.$inject = ['$scope','$filter', '$state', '$stateParams', '$uibModal', 'toastr', 'CuponSrv', 'EmpresaSrv', 'ListSrv', 'ReservaSrv'];
	function controller($scope, $filter, $state, $stateParams, $uibModal, toastr, CuponSrv, EmpresaSrv, listSrv, mySrv){

		var vm = this;
        var aux = {
            'blockCupon': false,
            'coffeebreak': angular.copy(window.iVariables.coffeebreak),
            'cocheras': [{cochera_id:0,cochera_nombre:'No deseo cochera'}],
            'dateOptions': { minDate: new Date() },
            'edit': false,
            'locales': [{ id:0, nombre:"Local"}],
            'modelos': [{ id:0, nombre:"Modelo"}].concat(window.iVariables.modelos),
            'oficinas': [],
            'saving': false,
            'searching_spaces': false,
            'searching_cupon': false,
            'selected_company': null,
            'selected_coffeebreak': window.iVariables.coffeebreak[0],
            'selected_cupon': {precio:0},
            'space_image': 'preload.jpg',
            'time_list': [],
            'times1': listSrv.getTimes(),
            'times2': listSrv.getTimes(),
            'unavailable_space': false,
            'loadingTimes': false,
            'prices': {precio: 0}
        };

        // delete
        aux.times2.splice(0,1);
        aux.modelos.splice(4,1);

        var params = {
            empresa_id: 0,
            audio: 'N',
            cochera_id: 0,
            coffeebreak: 'N',
            cupon: '',
            selected_cb: {
                id: 0,
                cantidad: 0,
                precio: 0
            },
            local_id: 0,
            modelo_id: 0,
            nombre: '',
            observacion: '',
            oficina_id: 0,
            reserva_id: 0,
            fecha: new Date(),
            hini: "08:00:00",
            hfin: "09:00:00",
            silla: 20,
            mesa: "0",
            detalle: [],
            limpieza: 1
        };

        var fn = {
            blockSaveButton: blockSaveButton,
            cancel: cancel,
            clearCB: clearCoffeeBreak,
            clearCompanies: clearCompanies,
            create: create,
            getAvailableV1: obtenerDisponibles,
            getTotalCost: getTotalCost,
            filterCompanies: filterCompanies,
            filterHq: filterHq,
            init: init,
            getCocheras: getCocheras,
            openCalendar: openCalendar,
            selectCoffeebreak: selectCoffeebreak,
            selectOffice: selectOffice,
            validateCupon: validateCupon
        };

        angular.extend(vm, {
            aux: aux,
            params: params,
            fn: fn
        });

        $scope.$watch(function(){
            return vm.aux.selected_company;
        }, function(newVal, oldVal){
            if(oldVal !== newVal && typeof newVal === 'object'){
                getPrices();
            }
        });

        function cancel(){
            $state.go('reserva');
        }

        function clearCoffeeBreak(){
            if(params.coffeebreak === 'S'){
                params.selected_cb.id = aux.coffeebreak[0].id;
                params.selected_cb.precio = aux.coffeebreak[0].precio;
                if(params.selected_cb.cantidad<=0)
                    params.selected_cb.cantidad = 20;
            } else {
                params.selected_cb.id = 0;
                params.selected_cb.cantidad = 0;
                params.selected_cb.precio = 0;
            }
        }

        function clearCompanies(){
            aux.selected_company = null;
            params.empresa_id = 0;
        }

        function create(){
            var p = angular.copy(params);

            // validadores
            if(p.coffeebreak === 'S' && p.selected_cb.cantidad<20){
                toastr.error('Para coffee break, debe ser mínimo 20 personas');
                return false;
            }

            if(p.modelo_id!=3 && (p.silla<20 || p.silla>60)){
                toastr.error('Solo se permite de 20 a 60 sillas');
                return false;
            }

            p.fecha = $filter('date')(p.fecha, 'yyyy-MM-dd', 'America/Lima');

            if(([2,3]).indexOf(p.modelo_id) >= 0){
                var file = document.getElementById("file_invitados");
                if(file !== null && file.files.length>0){
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var allTextLines = (e.target.result).split(/\r\n|\n/);
                        p.estructura = [];
                        while(allTextLines.length){
                            var line = allTextLines.shift().split(',');
                            if(line[0].length === 8 && (/^[\d]+$/g).exec(line[0]) !== null){
                                p.estructura.push({
                                    dni: line[0],
                                    nomape: (line[1]!==undefined?line[1]:''),
                                    email: (line[2]!==undefined?line[2]:''),
                                    movil: (line[3]!==undefined?line[3]:'')
                                });
                            }
                        }
                        // send
                        createReserva(p);
                    };
                    reader.readAsText(file.files[0]);
                } else {
                    createReserva(p);
                }
            } else {
                createReserva(p);
            }
        }

        function createReserva(p){

            //console.log(p);

            if(p.empresa_id === undefined || p.empresa_id <= 0){
                toastr.error("Seleccione una empresa");
                return false;
            }

            if(p.empresa_id === undefined || p.empresa_id <= 0){
                toastr.error("Seleccione una oficina");
                return false;
            }

            if(p.coffeebreak === 'S'){
                p.detalle = [{
                    'concepto': p.selected_cb.id,
                    'precio': p.selected_cb.precio,
                    'cantidad': p.selected_cb.cantidad
                }];
            } else {
                p.detalle = [];
            }

            delete p.selected_cb;

            aux.saving = true;

            if(vm.aux.edit){
                mySrv.update($stateParams.reservaID, p).then(function(r){
                    toastr.success('Reserva realizada');
                    $state.go('reserva');
                }).catch(function(e){
                    toastr.error(e.data.message, 'Error');
                }).finally(function(){
                    vm.aux.saving = false;
                });
            } else {
                mySrv.create(p).then(function(r){
                    toastr.success('Reserva realizada');
                    $state.go('reserva');
                }).catch(function(e){
                    toastr.error(e.data.message, 'Error');
                }).finally(function(){
                    vm.aux.saving = false;
                });
            }
        }

        function filterHq(){
            aux.locales.splice(1);
            angular.forEach(window.iVariables.locales, function(local){
                if( local.modeloids.split(',').indexOf( params.modelo_id + "" ) >= 0 ){
                    aux.locales.push(local);
                }
            });
        }

        function filterCompanies(value, onlyActive){
            if(value === '' || value.length < 3){
                return [];
            } else{
                var params = {view:'minimal',empresa_nombre:value};
                if(onlyActive === 1){
                    params.estado = 'A,X';
                }
                return EmpresaSrv.search(params);
            }
        }

        function getCocheras(){
            mySrv.getCocheras(params.reserva_id, {fecha: $filter('date')(params.fecha,'yyyy-MM-dd','America/Lima'), local_id: params.local_id, hini: params.hini, hfin: params.hfin}).then(function(r){
                vm.aux.cocheras.splice(1);
                vm.aux.cocheras = vm.aux.cocheras.concat(r.data);
            });
        }

        function getTotalCost(){
            aux.diffTime = (params.hfin.substr(0,2)*1) - (params.hini.substr(0,2)*1);
            return (vm.params.selected_cb.cantidad * vm.aux.selected_coffeebreak.precio) + (vm.aux.prices.precio * aux.diffTime) - (vm.aux.selected_cupon.precio);
        }

        function getDisponibilidad(){
            vm.aux.loadingTimes = true;
            var fecha = $filter('date')(params.fecha,'yyyy-MM-dd','America/Lima');
            mySrv.getAvailable({'fecha': fecha, 'oficina_id': params.oficina_id, 'reserva_id': params.reserva_id}).then(function(r){
                if(r.data.length>0){
                    vm.aux.time_list = [];
                    var date = $filter('date')(new Date(), 'yyyy-MM-dd', 'America/Lima');
                    var time = $filter('date')(new Date(), 'HH:mm:ss', 'America/Lima');
                    r.data.forEach(function(item){
                        if(date === fecha && item.hini <= time){
                            return false;
                        }
                        vm.aux.time_list.push(item);
                    });
                }
            }).finally(function(){
                vm.aux.loadingTimes = false;
            }).catch(function(e){
                toastr.error(e.data.error, 'Error en Horarios');
            });
        }

        function init(){
            if($stateParams.reserva !== undefined){
                // Es una edicion de reserva

                var reserva = $stateParams.reserva;

                //console.log(reserva);

                params.limpieza = reserva.limpieza;
                params.local_id = reserva.local_id;
                params.modelo_id = reserva.modelo_id;
                params.empresa_id = reserva.empresa_id;
                params.fecha = new Date(reserva.fecha_reserva + " 00:00:00");
                params.hini = reserva.hora_inicio;
                params.hfin = reserva.hora_fin;
                params.placa = reserva.placa;
                params.reserva_id = reserva.id;
                aux.selected_company = reserva.empresa_nombre;
                aux.edit = true;

                obtenerDisponibles();

                params.cochera_id = reserva.cochera_id;
                params.oficina_id = reserva.oficina_id;
                params.proyector = reserva.proyector;

                // Auditorio
                if(params.modelo_id != 1){
                    params.nombre = reserva.evento_nombre;
                    params.mesa = reserva.mesa;
                    params.audio = reserva.audio;
                    params.silla = reserva.silla;

                    // Cargar detalle de la reserva (coffeebreak)
                    mySrv.getDetails($stateParams.reservaID).then(function(r){
                        if(r.data.length>0){
                            vm.params.coffeebreak = "S";
                            vm.params.selected_cb = {
                                id: r.data[0].concepto_id,
                                cantidad: parseInt(r.data[0].cantidad),
                                precio: (r.data[0].precio * 1)
                            };

                            vm.aux.selected_coffeebreak = {
                                id: r.data[0].concepto_id,
                                cantidad: parseInt(r.data[0].cantidad),
                                precio: (r.data[0].precio * 1)
                            };
                        }
                    });
                }

                aux.unavailable_space = true;

                getDisponibilidad();

                if(reserva.observacion.length <= 0){
                    params.observacion = "";
                } else {
                    var obs = JSON.parse(reserva.observacion);

                    if(obs.length===0){
                        params.observacion = "";
                    } else {
                        params.observacion = obs[obs.length-1].body;
                    }
                }

            }

            return false;
        }

        function obtenerDisponibles(){

            if(([2,3,5]).indexOf(params.modelo_id)>=0){
                aux.times1 = listSrv.getTimes2();
                aux.times2 = listSrv.getTimes2();

                if(params.hini.substr(3,2) === "30"){
                    params.hini = params.hini.substr(0,2) + ":00:00";
                }

                if(params.hfin.substr(3,2) === "30"){
                    params.hfin = params.hfin.substr(0,2) + ":00:00";
                }

            } else {
                aux.times1 = listSrv.getTimes();
                aux.times2 = listSrv.getTimes();
            }

            if( params.hini === "" || params.hfin === "" ||  params.local_id<=0 || params.modelo_id<=0){
                return false;
            }

            aux.searching_spaces = true;
            aux.oficinas = [];
            aux.space_image = 'preload.jpg';
            aux.time_list = [];
            aux.unavailable_space = false;
            params.oficina_id = 0;
            params.cochera_id = 0;
            params.cupon = "";
            

            mySrv.getAvailableV1({
                local_id: params.local_id,
                modelo_id: params.modelo_id,
                fecha: $filter('date')(params.fecha,'yyyy-MM-dd','America/Lima'),
                hini: params.hini,
                hfin: params.hfin,
                reserva_id: params.reserva_id
            }).then(function(r){
                vm.aux.oficinas = r.data;
            }).catch(function(){
                toastr.error('Hubo un error al obtener las oficinas disponibles');
            }).finally(function(){
                vm.aux.searching_spaces = false;
            });

            // Get cocheras
            getCocheras();

            // Obtiene Precios de auditorio y sala de capacitacion
            getPrices();
        }

        function openCalendar(){
            aux.open1 = !aux.open1;
        }

        function selectCoffeebreak(){
            params.selected_cb.id = aux.selected_coffeebreak.id;
            params.selected_cb.precio = aux.selected_coffeebreak.precio;
        }

        function selectOffice(off){
            params.oficina_id = off.oficina_id;
            aux.space_image = off.imagen;
            getDisponibilidad();
        }

        function validateCupon(){

            if(aux.blockCupon){
                vm.params.cupon = '';
                vm.aux.blockCupon = false;
                vm.aux.selected_cupon.precio = 0;
            } else {
                aux.searching_cupon = true;
                CuponSrv.validate(vm.params.cupon).then(function(r){
                    vm.aux.blockCupon = true;
                    vm.aux.selected_cupon.precio = (r.data.monto * 1);
                }).catch(function(e){
                    toastr.error(e.data.error, 'Error');
                }).finally(function(){ vm.aux.searching_cupon = false; });
            }
        }

        function getPrices(){
            // Obtener precios de auditorios/salas/terraza
            if(params.local_id>0 && params.modelo_id>0 && params.modelo_id!==1 && aux.selected_company !== null &&  aux.selected_company.plan_id !== undefined  && aux.selected_company.plan_id > 0){ 
                mySrv.getPrice(params.local_id, params.modelo_id, aux.selected_company.plan_id).then(function(r){
                    vm.aux.prices = r.data;
                });
            }
        }

        function blockSaveButton(){
            return vm.aux.saving || vm.params.empresa_id < 1 || vm.params.oficina_id < 1 || vm.params.modelo_id < 1;
        }
	}
});