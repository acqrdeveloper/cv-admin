/**
 * Created by Gonzalo A. Del Portal Ch. on 08/05/17.
 * Modify by Kevin W. Baylon H. on 04/07/17
 */
define(['app', 'roles', 'angular'], function(app, roles, angular) {
    return app.controller('EmpresaFacturacionCtrl', ['$scope','$filter','$http','$timeout','$stateParams','$uibModal','FacturaSrv', 'ListSrv','toastr','listAllYears','listMonths','listestadofactura','listTipoNota','filterCompany', 'EmpServicioSrv', function($scope,$filter,$http,$timeout,$stateParams,$uibModal,mySrv,ListSrv,toastr,listAllYears,listMonths,listestadofactura,listTipoNota,filterCompany,serviceSrv){
        $scope.yearspay = listAllYears;
        $scope.months = listMonths;
        $scope.estadofactura = listestadofactura;
        //globales
        $scope.data = {};
        $scope.showForm = false;
        $scope.totalItems            = 0;
        $scope.validateLoading       = false; //si cargo
        $scope.validateFailedLoading = false; //si no cargo
        $scope.roles = roles;

        $scope.params = {
            anio: (new Date()).getFullYear(),
            mes: 0,
            estado: {value:"PENDIENTE", name:"Tipo Pago"},
            discrepancia: ''
        };

        $scope.services = [];

        function mailComprobanteCtrl(instance, items){
            var ctrl = this;

            var correo = empresa.basic.representantes[0].correo;
            var cc = "";

            function close(){
                instance.dismiss('close');
            }

            function send(){
                $scope.validateLoading = true;
                $http({url: '/email/facturasend/' + items.factura_id, method:'GET', params: {cc: ctrl.cc}}).then(function(r){
                    toastr.success(r.data.message);
                    close();
                }).catch(function(e){
                    toastr.error(e.data.message);
                }).finally(function(){
                    $scope.validateLoading = false;
                });
            }

            angular.extend(ctrl, {
                correo: correo,
                cc: cc,
                close: close,
                send: send
            });
        }

        var modalController = function($uibModalInstance, items){
            var $ctrl = this;
            var comprobante = null;
            var factura_id = 0;

            $ctrl.params = {
                anular: 'no',
                fecha_emision: new Date(),
                observacion: '',
                monto: 0,
                tipo: '-',
                discrepancia: '',
            };

            if(items !== undefined){
                if(items.data !== undefined){
                    if(items.data.comprobante !== undefined){
                        comprobante = items.data.comprobante;
                    }
                    if(items.data.factura_id !== undefined){
                        factura_id = items.data.factura_id;
                    }
                    if(items.data.fromPay !== undefined){
                        $ctrl.params.monto = items.data.fromPay.note_amount;
                        $ctrl.debitOpcion = true;
                        $ctrl.params.tipo = "CREDITO";
                    }

                }
            }
            $ctrl.comprobante = comprobante;
            $ctrl.factura_id = factura_id;
            $ctrl.edit = false;
            $ctrl.auxs = {
                calendarOpts: {
                    minDate: new Date(),
                    showWeeks: false
                },
                conceptos: [{
                    id:'-',
                    nombre: 'Seleccione'
                }],
                isOpenCalendar: false,
                sending: false
            };

            if(items !== undefined){
                if(items.data !== undefined){
                    $ctrl.data = items.data;
                }
            }

            $ctrl.close = function(){
                $uibModalInstance.dismiss('cancel');
            };

            $ctrl.getConceptos = function(){
                var conceptos = angular.copy(window.iVariables.notaconcepto);
                $ctrl.auxs.conceptos = [{
                    id:'',
                    nombre: 'Seleccione'
                }];
                angular.forEach(angular.copy(window.iVariables.notaconcepto), function(concepto){
                    if(concepto.tipo == $ctrl.params.tipo){
                         $ctrl.auxs.conceptos.push(concepto);
                    }
                });
            };

            $ctrl.notaCreditoAnularFactura = function(){
                if( $ctrl.params.anular === 'si' )
                    $ctrl.params.monto = 0;
            };

            $ctrl.openCalendar = function(){
                $ctrl.auxs.isOpenCalendar = true;
            };

            $ctrl.sendNota = function(){
                $ctrl.auxs.sending = true;
                mySrv.Note.create(empresa.id, factura_id, $ctrl.params).then(function(response){
                    if( response.data.load ){
                        toastr.success(response.data.message, 'Éxito');
                        $ctrl.close();
                        $scope.methods.getData();
                    }else{
                        toastr.error(response.data.message, 'Error al enviar nota');
                    }
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error al enviar nota');
                }).finally(function(){
                    $ctrl.auxs.sending = false;
                });
            };

            $ctrl.sendNotaMail = function(factura_id){
                $ctrl.auxs.sending = true;
                $http({
                    url: '/email/notasend/' + factura_id,
                    method: 'GET'
                }).then(function(response){
                    if( response.data.load )
                        toastr.success("Email Enviado Exitosamente", 'Éxito');    
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error al enviar Email');
                }).finally(function(){
                    $ctrl.auxs.sending = false;
                });
            };

            $ctrl.sendNotaRequire = function(){
                switch($ctrl.params.tipo){
                    case 'CREDITO':
                        return ($ctrl.params.anular==='si' || ($ctrl.params.monto>0 && $ctrl.params.monto<=(comprobante.monto*1)));
                    case 'DEBITO':
                        return ($ctrl.params.monto>0);
                    default:
                        return false;
                }
            };

            return $ctrl;
        };

        var payController = function(instance, items){
            var ctrl = this;
            var facturaItems = [];

            if(items !== undefined && items.invoice !== undefined){
                ctrl.invoice = items.invoice;
                console.log(ctrl.invoice);
            }

            ctrl.bancos = window.iVariables.entidad_bancaria;
            ctrl.ctas = window.iVariables.cuenta_bancaria;
            ctrl.ifDetraccion = "0";
            ctrl.isOpenCalendar = false;
            ctrl.params = defaultParams();
            ctrl.warranties = [];

            ctrl.addFacturaItem = function addFacturaItem(item){
                var idx = -1;
                for(var i=0;i<facturaItems.length;i++){
                    if(facturaItems[i].id === item.id){
                        idx = i;
                    }
                }

                if (idx > -1)
                    facturaItems.splice(idx, 1);
                else
                    facturaItems.push({'id': item.id, 'precio': (item.precio*1)});
            };

            ctrl.calculate = function calculate(){
                if(ctrl.params.tipo === 'DEPOSITO'){
                    if(ctrl.params.monto<=700){
                        ctrl.ifDetraccion = "0";
                    }

                    if(ctrl.ifDetraccion === "1"){
                        ctrl.params.detraccionD = Math.round((ctrl.params.monto * 0.1) * 100) / 100;
                        ctrl.params.detraccionE = Math.round((ctrl.params.monto - ctrl.params.detraccionD) * 100) / 100;
                    } else {
                        ctrl.params.detraccionD = 0;
                        ctrl.params.detraccionE = 0;
                    }

                    if(ctrl.params.id_pos>0){
                        var montoC = ctrl.params.monto;
                        if(ctrl.ifDetraccion === "1"){
                            montoC = ctrl.params.detraccionE;
                        }
                        ctrl.params.des_com_pos = montoC * window.iVariables.pos[ctrl.params.id_pos];
                        ctrl.params.dif_dep_pos = montoC - ctrl.params.des_com_pos;
                    } else {
                        ctrl.params.des_com_pos = 0;
                        ctrl.params.dif_dep_pos = 0;  
                    }
                } else {
                    ctrl.ifDetraccion = "0";
                    ctrl.params.id_pos = 0;
                    ctrl.params.detraccionD = 0;
                    ctrl.params.detraccionE = 0;
                    ctrl.params.des_com_pos = 0;
                    ctrl.params.dif_dep_pos = 0;
                    ctrl.params.dif_dep_pos = 0;
                    ctrl.params.detalle = "";
                    ctrl.params.deposito_fecha = new Date();
                    ctrl.deposito_banco = ctrl.bancos[0];
                    ctrl.deposito_cuenta = ctrl.ctas[0].id;

                }
            };

            ctrl.close = function close(){ instance.dismiss('close'); };

            ctrl.enableGuaranteeButton = function enableGuaranteeButton(){ return (facturaItems.length<=0); };

            ctrl.getGuarantees = function getGuarantees(){
                if(ctrl.params.tipo === 'GARANTIA'){
                    mySrv.Guarantee.get(empresa.id).then(function(r){
                        ctrl.warranties = r.data.rows;
                    });
                }
            };

            ctrl.openCalendar = function openCalendar(){ ctrl.isOpenCalendar = !ctrl.isOpenCalendar; };

            ctrl.payIt = function payIt(){
                ctrl.sending = true;
                var param = angular.copy(ctrl.params);
                param.deposito_fecha = $filter('date')(param.deposito_fecha, 'yyyy-MM-dd', 'America/Lima');
                mySrv.Payment.pay(parseInt(empresa.id), ctrl.invoice.id, param).then(function(response){
                    if( response.data.load ){
                        toastr.success("Pago Realizado Exitosamente", 'Éxito');
                        $scope.methods.getData();
                        ctrl.close();
                    }else{
                        toastr.error(response.data.message, 'Error');
                    }
                }).catch(function(error){
                    toastr.error('Error al realizar el pago');
                    console.log(response);
                }).finally(function(){
                    ctrl.sending = false;
                });
            };

            ctrl.payWithCreditNote = function payWithCreditNote(){
                if(confirm("¿Estás seguro de emitir la nota de crédito?")){
                    ctrl.sending = true;
                    mySrv.Note.create(empresa.id, ctrl.invoice.id, {
                        anular: '',
                        monto: $filter('sumByKey')(ctrl.warranties, 'precio'),
                        tipo: 'CREDITO',
                        factura_item: $filter('getColumn')(ctrl.warranties, 'id'),
                        observacion: ctrl.params.observacion
                    }).then(function(r){
                        if(r.data.load){
                            toastr.success('Pago con nota de crédito realizado.');
                            $scope.methods.getData();
                            ctrl.close();
                        } else {
                            toastr.error(r.data.message);
                        }

                    }).catch(function(e){
                        console.log(e);
                        toastr.error(e.data.message);
                    }).finally(function(){
                        ctrl.sending = false;
                    });
                } else {
                    toastr.error("Pago con nota de crédito cancelado");
                }
            };

            ctrl.payWithGuarantee = function payWithGuarantee(){
                ctrl.sending = true;
                var params = {
                    observacion: ctrl.params.observacion,
                    factura_item: facturaItems
                };
                mySrv.Payment.payWithGuarantee(parseInt(empresa.id), ctrl.invoice.id, params).then(function(r){
                    toastr.success("Pago realizado");
                    $scope.methods.getData();
                    ctrl.close();
                }).catch(function(e){
                    toastr.success(e.data.message);
                    console.log(e);
                }).finally(function(){
                    ctrl.sending = false;
                });
            };

            ctrl.validateForm = function validateForm(){
                if(ctrl.params.monto<=0){
                    return true;
                } else if(ctrl.params.tipo === 'DEPOSITO' && (ctrl.params.detalle === '' || ctrl.params.detalle.length <= 0)){
                    return true;
                }
                return false;
            };

            function defaultParams(){
                return {
                    deposito_banco: ctrl.bancos[0],
                    deposito_cuenta: ctrl.ctas[0].id,
                    deposito_fecha: new Date(),
                    detalle: '',
                    id_pos: 0,
                    tipo: 'DEPOSITO',
                    monto: Math.round((ctrl.invoice.total - ctrl.invoice.pago) * 100) / 100,
                    observacion: ''
                };
            }
        };

        var paymentCtrl = function(instance, items){
            var ctrl = this;
            var invoice = items.invoice;
            var roles   = items.roles;
            console.log( items );
            var payments = [];

            function close(){
                instance.dismiss('close');
            }

            function deletePayment(payment){
                ctrl.deleting = true;
                $http.delete('/pago/' + payment.id).then(function(r){
                    if(r.data.load){
                        toastr.success('Pago eliminado');
                        init();
                        $scope.methods.getData();
                    } else {
                        toastr.error(r.data.message);
                    }
                }).catch(function(e){
                    toastr.error(e.data.message);
                }).finally(function(){
                    ctrl.deleting = false;
                });
            }

            function init(){
                ctrl.getting = true;
                $http({
                    url: "/factura/payment_detail/"+invoice.id,
                    method: 'GET'
                }).then(function(r) {
                    ctrl.payments = r.data.data.pago;
                }).catch(function(){
                    toastr.error('No se pudo obtener el historial de pagos.');
                }).finally(function(){
                    ctrl.getting = false;
                });
            }

            angular.extend(ctrl, {
                'close': close,
                'delete': deletePayment,
                'invoice': invoice,
                'payments': payments,
                'roles':roles
            });

            init();
        };

        var invoiceController = function(instance, items){
            var ctrl = this;
            ctrl.invoice = items.invoice;
            ctrl.close = function close(){
                instance.dismiss('close');
            };
            ctrl.addNumber = function addNumber(){
                $http({
                    url: '/empresa/' + ctrl.invoice.empresa_id + '/factura/' + ctrl.invoice.id + '/agregar_numero',
                    method: 'PUT',
                    data: {'numero':ctrl.params.numero} 
                }).then(function(r){
                    toastr.success(r.data.message);
                    ctrl.close();
                    $scope.methods.doGet();
                }).catch(function(e){
                    toastr.error(e.data.message);
                });
            };
            ctrl.changeInvoiceType = function (){
                ctrl.sending = true;
                $http.put('/factura/alterarcomprobante/' + empresa.id + '/' + ctrl.invoice.id + '/' + ctrl.params.comprobante).then(function(){
                    toastr.success('Datos actualizados');
                    $scope.methods.getData();
                    ctrl.close();
                }).catch(function(e){
                    toastr.error(e.data.message);
                }).finally(function(){
                    ctrl.sending = false;
                });
            };
        };

        //metodos $scope
        $scope.methods = {
            addInvoiceNumber: function addInvoiceNumber(invoice){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/factura/addInvoiceNumber.html',
                    controller: ['$uibModalInstance', 'items', invoiceController],
                    controllerAs: 'ctrl',
                    resolve: {
                        items: function(){
                            return {'invoice': angular.copy(invoice)};
                        }
                    }
                }).result.then(function(){}, function(){});
            },
            applyChange: function() {
                $scope.methods.getData();
                getFacturacionTemporal();
            },
            changeInvoiceType: function(invoice){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/factura/invoicetype.html',
                    controller: ['$uibModalInstance','items', invoiceController],
                    controllerAs: 'ctrl',
                    resolve: {
                        items: function(){
                            return {'invoice': angular.copy(invoice)};
                        }
                    }
                }).result.then(function(){}, function(){});
            },
            changePaginate: function() {
                $scope.data = [];
                $scope.validateFailedLoading = false;
                $scope.methods.doGet();
            },
            clickExportar: function() {
                var param = angular.copy($scope.params);
                param.estado = param.estado.value;
                param.empresa_id = empresa.id;
                var json = param;
                window.open('/export/factura?json=' + JSON.stringify(json),'');
            },
            doGet: function() {
                //$scope.params.pagina = 1;
                $scope.methods.getData();
            },
            editInvoice: function(invoice){
                $http({
                    url: '/factura/factura_item/' + invoice.id
                }).then(function(response){
                    $scope.form.params.items = response.data.item;
                    $scope.form.params.fecha_limite = new Date(invoice.fecha_emision + ' 00:00:00');
                    $scope.form.params.fecha_vencimiento = new Date(invoice.fecha_vencimiento + ' 00:00:00');
                    $scope.form.params.tipo = invoice.comprobante;
                    $scope.form.params.id = invoice.id;
                    $scope.form.params.sunat = 'off';

                    $scope.showForm = !$scope.showForm;
                });
            },
            init: function(){
                // Get all invoices
                if( !empresa.facturacion || empresa.facturacion === {} || empresa.facturacion === null ){
                    $scope.methods.getData();
                }

                // Get Temporal items
                getFacturacionTemporal();

                // Get Services
                getServices();
            },
            openFacturaMail: function(factura_id){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/factura/mail_comprobante.html',
                    controller: ['$uibModalInstance','items',mailComprobanteCtrl],
                    controllerAs: 'ctrl',
                    resolve: {
                        items: function(){
                            return {'factura_id':factura_id};
                        }
                    }
                }).result.then(function(){}, function(){});
            },
            pay: function(invoice){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/factura/pay.html',
                    controller: ['$uibModalInstance', 'items', payController],
                    controllerAs: 'ctrl',
                    resolve: {
                        items: function(){
                            return {'invoice': angular.copy(invoice)};
                        }
                    }
                }).result.then(function(){}, function(){});
            },
            showForm: function(){
                if($scope.showForm){
                    $scope.form.params = $scope.form.defaultParams();
                }
                $scope.showForm = !$scope.showForm;
            },
            sendSunat:function(factura_id){
                $scope.validateLoading = true;
                $http({
                    url: '/sunat/facturasend/' + factura_id,
                    method: 'GET'
                }).then(function(response){
                    if( response.data.load ){
                        toastr.success("Factura declarada Exitosamente", 'Éxito');
                        $scope.methods.getData();
                    }else{
                        toastr.error(response.data.message, 'Error');
                    }
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error al enviar a sunat');
                }).finally(function(){
                    $scope.validateLoading = false;
                });
            },
            setNota:function( factura_id ){
                openModalNote(factura_id);
            },
            setAnulado:function( factura_id ){
                $scope.validateLoading = true;
                $http({
                    url: "/factura/anula/"+empresa.id+"/"+factura_id,
                    method: 'GET'
                }).then(function(response) {
                    $scope.validateLoading = false;

                    if( response.data.load ){
                        toastr.success("Comprobante Anulado", 'Éxito');
                        $scope.methods.getData();
                    }else{
                        toastr.error(response.data.message, 'Error');
                    }

                }, function(){
                    toastr.error('No se pudo anular el Comprobante.');
                });
            },
            getPaymentDetail: function(invoice){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/factura/payments.html',
                    controller: ['$uibModalInstance', 'items', paymentCtrl],
                    controllerAs: 'ctrl',
                    resolve: {
                        items: function(){
                            return {'invoice': angular.copy(invoice), 'roles':roles};
                        }
                    }
                }).result.then(function(){}, function(){});
            },
            getComprobanteDetalle: function(factura_id){
                $scope.validateLoading = true;
                $http({
                    url: "/factura/factura_item/"+factura_id,
                    method: 'GET'
                }).then(function(r) {
                    $scope.validateLoading = false;
                    $uibModal.open({
                        animation: true,
                        templateUrl: '/templates/modals/empresa/detalle.html',
                        controller: ['$uibModalInstance', 'items', modalController],
                        controllerAs: '$ctrl',
                        size:'lg',
                        resolve: {
                            items: function(){
                                return {data: r.data};
                            }
                        }
                    }).result.then(function(){}, function(){});
                }, function(){
                    toastr.error('No se pudo obtener el Detalle del Comprobante.');
                });
            },
            getComprobanteHistorial: function(factura_id){
                $scope.validateLoading = true;
                $http({
                    url: "/factura/factura_historial/"+factura_id,
                    method: 'GET'
                }).then(function(r) {
                    $scope.validateLoading = false;
                    $uibModal.open({
                        animation: true,
                        templateUrl: '/templates/modals/empresa/historial.html',
                        controller: ['$uibModalInstance', 'items', modalController],
                        controllerAs: '$ctrl',
                        //size:'lg',
                        resolve: {
                            items: function(){
                                return {data: r.data};
                            }
                        }
                    }).result.then(function(){}, function(){});
                }, function(){
                    toastr.error('No se pudo obtener el Detalle del Comprobante.');
                });
            },
            getData: function() {
                $scope.validateLoading = true;
                $scope.searching = true;
                var param = angular.copy($scope.params);
                param.estado = param.estado.value;
                param.empresa_id = empresa.id;
                $http({
                    url: "/factura/search",
                    method: 'GET',
                    params: param
                }).then(function(r) {
                    if ( r.data.rows.length > 0 ) {
                        //$scope.data = r.data.rows;
                        empresa.facturacion = r.data.rows;
                        $scope.totalItems = r.data.total;
                        $scope.validateLoading = false;
                        $scope.validateFailedLoading = false;
                    } else {
                        //$scope.data = [];
                        empresa.facturacion = [];
                        $scope.totalItems = 0;
                        $scope.validateLoading = false;
                        $scope.validateFailedLoading = true;
                    }
              
                }, function(){
                    toastr.error('No se pudo obtener el historial de este recado.');
                }).finally(function(){
                    $scope.searching = false;
                });
            },
            showTemporal: function(){
                $uibModal.open({
                    animation: true,
                    templateUrl: '/templates/modals/factura/extra.html',
                    controller: ['$uibModalInstance', extraCtrl],
                    controllerAs: 'ctrl',
                }).result.then(function(){}, function(){});
            }
        };

        var now = new Date();
        var meses = ListSrv.months();
        meses[0] = 'Seleccione';

        function addPeriodo(){
            return " (Periodo " + $scope.form.itemMonth + "/" + $scope.form.itemYear + ")";
        }

        function getPlan(id){
            for(var i = 0; i<window.iVariables.planes.length; i++){
                var plan = window.iVariables.planes[i];
                if(plan.id === id){
                    return plan;
                }
            }
        }

        $scope.form = {
            conceptos: angular.copy(window.iVariables.conceptos),
            isOpenedCalendar1: false,
            isOpenedCalendar2: false,
            flagSunat: false,
            dateOptions: {
                minDate: now,
                showWeeks: false
            },
            dateOptions2: {
                minDate: (new Date()).setDate( (new Date()).getDate() + 13),
                showWeeks: false
            },
            itemMonth: now.getMonth() + 1,
            itemCB: angular.copy(window.iVariables.coffeebreak)[0],
            itemPlan: angular.copy(window.iVariables.planes)[0].id,
            itemType: 'P',
            itemYear: now.getFullYear(),
            monthList: meses,
            planList: angular.copy(window.iVariables.planes),
            sending: false,
            yearList: [now.getFullYear()-1,now.getFullYear(),now.getFullYear()+1],
            calcFromQuantity: function(evt, idx){
                $scope.form.params.items[idx].precio = event.currentTarget.value * 30;
            },
            createComprobante: function(){
                var param = angular.copy( $scope.form.params );
                var m = (param.id !== undefined)?'PUT':'POST';
                var u = (param.id !== undefined)?'/empresa/' + empresa.id + '/factura/' + param.id:'/empresa/' + empresa.id + '/factura';
                param.fecha_limite = $filter('date')(param.fecha_limite, 'yyyy-MM-dd', 'America/Lima');
                param.fecha_vencimiento = $filter('date')(param.fecha_vencimiento, 'yyyy-MM-dd', 'America/Lima');
                $scope.validateLoading = true;
                $http({
                    url: u,
                    method: m,
                    data: param
                }).then(function(response){
                    if( response.data.load ){
                        toastr.success( "Comprobante Generado", 'Éxito');
                        $scope.methods.showForm();
                        $scope.methods.getData();

                        $timeout(function(){
                            if(response.data.factura_temporal !== undefined)
                                $scope.itemExtras = response.data.factura_temporal;
                        }, 100);

                    }else{
                        toastr.error( response.data.message, 'Error al enviar Comprobante');
                    }
                }).catch(function(error){
                    toastr.error(error.data.message, 'Error al enviar Comprobante');
                }).finally(function(){
                    $scope.validateLoading = false;
                });
            },
            addItem: function(){
                var obj = {descripcion:'SERVICIO EXTRA', descripcion_sunat:"SERVICIO EXTRA", precio: 0, custom_id: 0, tipo: angular.copy($scope.form.itemType), mes: $scope.form.itemMonth, anio: $scope.form.itemYear};

                if(obj.tipo === 'P'){
                    obj.descripcion = empresa.basic.plan.nombre + addPeriodo();
                    obj.descripcion_sunat = "SERVICIO EN OFICINAS VIRTUALES" + addPeriodo();
                    obj.custom_id = empresa.basic.plan_id;
                    obj.precio = (empresa.basic.plan.precio*1);
                } else if(obj.tipo === 'G'){
                    obj.descripcion = "GARANTIA";
                    obj.descripcion_sunat = obj.descripcion;
                    //obj.custom_id = empresa.basic.plan_id;
                    obj.precio = (empresa.basic.plan.precio*1);
                } else if(obj.tipo === 'HO'){
                    obj.descripcion = "HORAS EXTRA OFICINA" + addPeriodo();
                    obj.descripcion_sunat = obj.descripcion;
                    obj.precio = 30;
                } else if(obj.tipo === 'HR'){
                    obj.descripcion = "HORAS EXTRA REUNION" + addPeriodo();
                    obj.descripcion_sunat = obj.descripcion;
                    obj.precio = 30;
                } else if(obj.tipo === 'C'){
                    obj.custom_id = $scope.form.itemPlan;
                    var p = getPlan(obj.custom_id);
                    obj.descripcion = "COMBO " + p.nombre + addPeriodo();
                    obj.precio = (p.precio*1);
                    obj.descripcion_sunat = obj.descripcion;
                }

                if($scope.services.length>0 && obj.tipo === 'P'){
                    angular.forEach($scope.services, function(service){
                        if(service.tipo == "P"){
                            obj.precio = ((service.servicio_extra_id <= 0)?1:service.servicio_extra_id) * obj.precio;                    
                        } else if(service.tipo == "D"){
                            obj.precio = (obj.precio*1) - (service.monto*1);
                            obj.precio = $filter('number')(obj.precio, 2);
                            obj.descripcion = obj.descripcion + " dscto. S/. " + $filter('number')(service.monto,2);
                        } else if(service.tipo == "E") {
                            $scope.form.params.items.push({
                                descripcion: service.concepto, 
                                descripcion_sunat:"SERVICIO EXTRA", 
                                precio: $filter('number')(service.monto,2), 
                                custom_id: 0, 
                                tipo: "E", 
                                mes: 0, 
                                anio: 0
                            });
                        }
                    });

                    $scope.form.params.items.push(obj);

                } else {
                    $scope.form.params.items.push(obj);
                }
            },
            changeFlagSunat: function(){
                $scope.form.flagSunat = !$scope.form.flagSunat;
            },
            changeToNegative: function(item){
                if(item.tipo === 'D' && item.precio > 0){
                    item.precio = Math.abs(item.precio) * -1;
                }
            },
            defaultParams: function(){

                var p = {
                    fecha_limite: (new Date()).setDate( (new Date()).getDate() + 13 ),
                    fecha_vencimiento: new Date(),
                    tipo: 'FACTURA',
                    meses: 2,
                    adelanto: 'off',
                    items: [
                        /*{
                            descripcion: angular.copy(empresa.basic.plan.nombre + addPeriodo()),
                            descripcion_sunat: angular.copy("SERVICIO EN OFICINAS VIRTUALES" + addPeriodo()),
                            tipo: "P",
                            precio: (empresa.basic.plan.precio*1),
                            custom_id: empresa.basic.plan.id*1,
                            anio: angular.copy($scope.form.itemYear),
                            mes: angular.copy($scope.form.itemMonth)
                        }*/
                    ],
                    sunat: 'off'
                };

                return p;
            },
            deleteItem: function(idx){
                $scope.form.params.items.splice(idx, 1);
            },
            disabledAddItem: function(){
                if ($scope.form.itemType === 'C' || $scope.form.itemType === 'G' || $scope.form.itemType === 'HO' || $scope.form.itemType === 'HR' || $scope.form.itemType === 'P'){
                    for(var i = 0; i<$scope.form.params.items.length; i++){
                        var item = $scope.form.params.items[i];
                        if(item.tipo === $scope.form.itemType && (item.tipo === 'G' || (item.mes !== undefined && item.mes === $scope.form.itemMonth && item.anio !== undefined && item.anio === $scope.form.itemYear)))
                            return true;
                    }
                } else {
                    return false;                    
                }

                return false;
            },
            getItemExtras: function(){

                var items = [];

                angular.forEach($scope.form.params.items, function(item){
                    if(item.tipo === 'T'){
                        items.push(item);
                    }
                });

                angular.forEach($scope.itemExtras, function(i){
                    if($filter('findInArray')(items, 'custom_id', i.id) === -1){
                        $scope.form.params.items.push({
                            descripcion: i.descripcion,
                            descripcion_sunat: "SERVICIOS EXTRAS",
                            tipo: "T",
                            precio: (i.precio*1),
                            custom_id: i.id,
                            anio: 0,
                            mes: 0
                        });
                    }
                });

                items = undefined;
            },
            openCalendar1: function(){
                $scope.form.isOpenedCalendar1 = true;
            },
            openCalendar2: function(){
                $scope.form.isOpenedCalendar2 = true;
            },
            selectConcepto: function(){
                if($scope.form.itemType == 'CB'){
                    $uibModal.open({
                        animation: true,
                        templateUrl: '/templates/modals/misc/coffeebreak.html',
                        controller: ['$uibModalInstance', cbCtrl],
                        controllerAs: 'ctrl'
                    }).result.then(function(){}, function(){});
                    $scope.form.itemType = 'P';
                }
                return false;
            },
            send: function(){
                console.log($scope.form.params);
            },
            showDescription: function(tipo){
                return (['P','G','C','HR','HO']).indexOf(tipo) >= 0;
            },
            showPeriodo: function(){
                switch($scope.form.itemType){
                    case 'P':
                    case 'HO':
                    case 'HR':
                    case 'C':
                        return true;
                    default:
                        return false;
                }
            },
            showPlan: function(){
                return $scope.form.itemType === 'C';
            },
            showPrice: function(tipo){
                return (['P','G']).indexOf(tipo) >= 0;
            }
        };

        $scope.form.params = $scope.form.defaultParams();


        // Add By Kevin
        // factura_id
        // comprobante
        // listTipoNota
        // modalController
        function openModalNote(factura_id, payModal){
            $scope.validateLoading = true;
            mySrv.Invoice.getById(factura_id).then(function(r) {
                $scope.validateLoading = false;
                    $uibModal.open({
                        animation: true,
                        templateUrl: '/templates/modals/empresa/notas.html',
                        controller: ['$uibModalInstance', 'items', modalController],
                        controllerAs: '$ctrl',
                        //size:'lg',
                        resolve: {
                            items: function(){
                                var retur = { data:{ factura_id:factura_id, comprobante: r.data.comprobante, listtiponota:listTipoNota } };
                                if(payModal !== undefined){
                                    retur.data.fromPay = {
                                        disable_debit: true,
                                        note_amount: payModal.monto
                                    };
                                }
                                return retur;
                            }
                        }
                    }).result.then(function(){}, function(){});
            }, function(){
                toastr.error('No se pudo obtener la factura para poder generar la Nota.');
            });
        }

        // Controller for coffeebreak
        function cbCtrl(instance){
            var ctrl = this;

            ctrl.aux = {
                coffeebreaks: angular.copy(window.iVariables.coffeebreak),
                selected_coffeebreak: angular.copy(window.iVariables.coffeebreak)[0],
                selected_reserva: {},
                reservas: []
            };

            ctrl.params = {
                cantidad: 20,
                reserva_id: 0,
                preciou: ctrl.aux.selected_coffeebreak.precio
            };

            ctrl.close = function(){
                instance.dismiss('close');
            };

            ctrl.addItem = function(){

                var obj = {
                    descripcion:'COFFEEBREAK ' + ctrl.aux.selected_coffeebreak.nombre  + ' x ' +  ctrl.params.cantidad + ' personas', 
                    descripcion_sunat:"SERVICIO EXTRA",
                    precio: ctrl.aux.selected_coffeebreak.precio * ctrl.params.cantidad, 
                    custom_id: ctrl.aux.selected_coffeebreak.id,
                    tipo: 'CB', 
                    mes: (new Date()).getMonth() + 1, 
                    anio: (new Date()).getFullYear(),
                    reserva_id: ctrl.aux.selected_reserva.id,
                    cantidad: ctrl.params.cantidad,
                    preciou: ctrl.aux.selected_coffeebreak.precio
                };

                $scope.form.params.items.push(obj);

                ctrl.close();
            };

            $http.get('/oficina/auditorio/' + empresa.id).then(function(r){
                ctrl.aux.reservas = r.data.rows;
            }).catch(function(){
                toastr.error('Hubo un error al cargar los eventos, intente más tarde');
                ctrl.close();
            });
        }

        function extraCtrl(instance){
            var ctrl = this;

            ctrl.saving = false;

            ctrl.close = function(){
                instance.dismiss('close');
            };

            ctrl.delete = function(id, idx){
                ctrl.saving = true;

                $http({url: '/facturatemporal/'  + id  + '/' + empresa.id, 'method':'DELETE'}).then(function(r){
                    toastr.success(r.data.message);
                    $timeout(function(){
                        ctrl.extraItems.splice(idx, 1);
                        $scope.itemExtras = angular.copy(ctrl.extraItems);
                    }, 100);
                }).catch(function(e){
                    toastr.error(e.data.error);
                }).finally(function(){
                    ctrl.saving = false;
                });
            };

            ctrl.save = function(idx){
                ctrl.saving = true;

                var item = ctrl.extraItems[idx];

                var p = { 
                   id: item.id,
                   descripcion: item.descripcion,
                   precio: item.precio
                };

                $http({
                    url: '/facturatemporal/' + empresa.id,
                    method: 'POST',
                    data: p
                }).then(function(r){
                    toastr.success(r.data.message);
                    $timeout(function(){
                        $scope.itemExtras = angular.copy(ctrl.extraItems);
                    }, 100);
                }).catch(function(e){
                    toastr.error(e.data.error);
                }).finally(function(){ ctrl.saving = false; });
            };

            ctrl.extraItems = angular.copy($scope.itemExtras);
        }

        function getFacturacionTemporal(){
            $http.get('/facturatemporal/' + empresa.id).then(function(r){
                $scope.itemExtras = r.data;
            }).catch(function(e){
                console.log(e);
            });
        }

        function getServices(){
            serviceSrv.getServices({empresa_id:empresa.id}).then(function(r){
                $scope.services = r.data;
            });
        }
    }]);
});