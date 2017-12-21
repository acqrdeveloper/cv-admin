define(['angular', 'app', 'roles'], function (angular, app, roles) {
    return app.config(['$urlRouterProvider', '$locationProvider', '$stateProvider', function ($urlRouterProvider, $locationProvider, $stateProvider) {
        $stateProvider
            .state('app', {
                abstract: true,
                templateUrl: '/templates/full.html'
            })
            .state('configuracion', {
                url: '/configuracion',
                templateUrl: '/templates/configuracion.html',
                parent: 'app'
            })
            .state('cupon', {
                url: '/cupon',
                templateUrl: '/templates/cupon.html',
                parent: 'app'
            }) 
            .state('dashboard', {
                url: '/',
                templateUrl: '/templates/dashboard.html',
                controller: 'DashboardCtrl',
                parent: 'app'
            })
            .state('nueva_empresa', {
                url: '/nueva_empresa',
                templateUrl: '/templates/nueva_empresa.html',
                controller: 'NuevaEmpresaCtrl',
                controllerAs: "vm",
                parent: 'app'
            })
            .state('asistencia', {
                url: '/asistencia',
                templateUrl: '/templates/asistencia.html',
                controller: 'AsistenciaCtrl',
                controllerAs: "vm",
                parent: 'app'
            })
            .state('empresas', {
                url: '/empresas',
                templateUrl: '/templates/empresa.html',
                controller: 'EmpresaCtrl',
                controllerAs: "vm",
                parent: 'app'
            })
            .state('empresa', {
                url: '/empresa/:empresaID',
                templateUrl: "/templates/empresa_tabs.html",
                parent: 'app'
            })
            .state('empresa.info', {
                url: '/info',
                templateUrl: "/templates/empresa_info.html",
            })
            .state('empresa.central', {
                url: '/central',
                templateUrl: "/templates/empresa_central.html",
            })
            .state('empresa.servicio', {
                url: '/servicio',
                templateUrl: "/templates/empresa_servicio.html"
            })
            .state('empresa.coworking', {
                url: '/coworking',
                templateUrl: "/templates/empresa_coworking.html"
            })
            .state('empresa.facturacion', {
                url: '/facturacion',
                templateUrl: "/templates/empresa_facturacion.html",
                controller: "EmpresaFacturacionCtrl"
            })
            .state('empresa.nota', {
                url: '/nota',
                templateUrl: "/templates/empresa_nota.html",
                controller: "EmpresaNotaCtrl"
            })
            .state('empresa.seguimiento', {
                url: '/seguimiento',
                templateUrl: "/templates/empresa_seguimiento.html",
                controller: "EmpresaSeguimientoCtrl"
            })
            .state('empresa.reservas', {
                url: '/reservas',
                templateUrl: "/templates/empresa_reservas.html"
            })
            .state('empresa.mensajes', {
                url: '/mensajes',
                templateUrl: "/templates/empresa_mensajes.html"
            })
            .state('empresa.llamadas', {
                url: '/llamadas',
                templateUrl: "/templates/empresa_llamadas.html"
            })
            .state('empresa.historial', {
                url: '/historial',
                templateUrl: "/templates/empresa_historial.html",
                controller: 'EmpresaHistorialCtrl',
                controllerAs: "vm"
            })
            .state('empresa.extras', {
                url: '/extras',
                templateUrl: "/templates/empresa_extras.html",
                controller: 'EmpresaExtrasCtrl',
                controllerAs: "vm"
            })
            .state('recado-correspondencia', {
                url: '/recado-correspondencia',
                templateUrl: "/templates/correspondencia_recado.html",
                parent: 'app'
            })
            .state('recado-correspondencia.recado', {
                url: '/recado',
                templateUrl: '/templates/recado.html'
            })
            .state('recado-correspondencia.correspondencia', {
                params: {
                    empresa_id: null,
                    empresa_nombre: null
                },
                url: '/correspondencia',
                templateUrl: '/templates/correspondencia.html'
            })
            .state('reserva', {
                url: '/reserva',
                templateUrl: '/templates/reserva.html',
                parent: 'app'
            })
            .state('reserva_nueva', {
                url: '/reservar',
                templateUrl: '/templates/nueva-reserva.html',
                controller: 'NuevaReservaCtrl',
                controllerAs: 'vm',
                parent: 'app'
            })
            .state('reserva_editar', {
                url: '/reserva/:reservaID',
                params: {
                    reserva: null
                },
                templateUrl: '/templates/nueva-reserva.html',
                parent: 'app'
            })
            .state('bandeja', {
                url: '/bandeja',
                templateUrl: '/templates/bandeja.html',
                controller: 'BandejaCtrl',
                controllerAs: "vm",
                parent: 'app'
            })
            .state('seguimiento', {
                url: '/seguimiento',
                templateUrl: '/templates/seguimiento.html',
                controller: 'SeguimientoCtrl',
                controllerAs: "vm",
                parent: 'app'
            })
            .state('feedback', {
                url: '/feedback',
                templateUrl: '/templates/feedback.html',
                controller: 'FeedbackCtrl',
                parent: 'app'
            })
            .state('factura', {
                url: '/factura',
                templateUrl: '/templates/factura.html',
                controller: 'FacturaCtrl',
                parent: 'app'
            })
            .state('reporte', {
                url: '/reporte',
                templateUrl: '/templates/reporte.html',
                controller: 'ReporteCtrl',
                parent: 'app'
            })
            .state('mensaje', {
                url: '/mensaje',
                templateUrl: '/templates/mensaje.html',
                controller: 'MensajeCtrl',
                parent: 'app'
            })
            .state('abogado', {
                url: '/abogado',
                templateUrl: '/templates/abogado.html',
                parent: 'app'
            })
            .state('usuario', {
                url: '/usuario',
                templateUrl: '/templates/usuario.html',
                parent: 'app'
            });

        $locationProvider.html5Mode({
            enabled: true,
            requireBase: false
        });

        //$urlRouterProvider.otherwise('/');
    }]).run(['$rootScope', '$state', '$http', '$timeout', function ($rootScope, $state, $http, $timeout) {
        $rootScope.$state = $state;
        $http.defaults.headers.common['X-CSRF-TOKEN'] = document.getElementsByTagName("meta")[0].content;
        $http.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    }]);
});
