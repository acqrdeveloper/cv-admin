require.config({
	waitSeconds: 200,
	paths: {
		angular: './node_modules/angular/angular.min',
		ngAnimate: './node_modules/angular-animate/angular-animate.min',
		ngRoute: './node_modules/angular-route/angular-route.min',
		uiRouter: './node_modules/angular-ui-router/release/angular-ui-router',
		uiBootstrap: './node_modules/angular-ui-bootstrap/dist/ui-bootstrap-tpls',
		'ui.select': './node_modules/ui-select/dist/select.min',
		toastr: './node_modules/angular-toastr/dist/angular-toastr.tpls',
		app: './appdev/js/app',
		roles: './appdev/js/roles',
		io: './node_modules/socket.io-client/dist/socket.io',
		moment: './node_modules/moment/moment'
	},
	shim: {
		angular: {
			exports: 'angular'
		},
		ngAnimate: {
			deps: ['angular']
		},
		ngRoute: {
			deps: ['angular']
		},
		uiRouter: {
			deps: ['angular']
		},
		uiBootstrap: {
			deps: ['angular']
		},
		'ui.select': {
			deps: ['angular']
		},
		toastr: {
			deps: ['angular']
		}
	}
});

require([
	'angular',
	'ngAnimate',
	'ngRoute',
	'uiRouter',
	'uiBootstrap',
	'ui.select',
	'toastr',
	'app',
	'appdev/js/route',
	'appdev/js/services',
	'appdev/js/filters',
	'appdev/js/services/central',
	'appdev/js/services/correspondencia',
	'appdev/js/services/coworking',
	'appdev/js/services/cupon',
	'appdev/js/services/empleado',
	'appdev/js/services/empresa',
	'appdev/js/services/factura',
	'appdev/js/services/list',
	'appdev/js/services/mensaje',
	'appdev/js/services/local',
	'appdev/js/services/oficina',
	'appdev/js/services/plan',
	'appdev/js/services/representante',
	'appdev/js/services/reserva',
	'appdev/js/services/servicio',
	'appdev/js/controllers/app',
    'appdev/js/controllers/abogado',
    'appdev/js/controllers/asistencia',
    'appdev/js/controllers/bandeja',
	'appdev/js/controllers/correspondencia',
	'appdev/js/controllers/cupon',
	'appdev/js/controllers/detalleempresa',
	'appdev/js/controllers/dashboard',
	'appdev/js/controllers/detalle-info',
	'appdev/js/controllers/detalle-llamada',
	'appdev/js/controllers/detalle-reserva',
	'appdev/js/controllers/empresa',
	//'appdev/js/controllers/empresa_central',
	'appdev/js/controllers/empresa_pbx',
	'appdev/js/controllers/empresa_coworking',
	'appdev/js/controllers/empresa_facturacion',
	'appdev/js/controllers/empresa_historial',
	'appdev/js/controllers/empresa_mensaje',
	'appdev/js/controllers/empresa_nota',
	'appdev/js/controllers/empresa_extras',
	'appdev/js/controllers/empresa_seguimiento',
	'appdev/js/controllers/empresa_servicio',
    'appdev/js/controllers/factura',
	'appdev/js/controllers/feedback',
    'appdev/js/controllers/local',
	'appdev/js/controllers/main-correspondencia',
	'appdev/js/controllers/nueva-empresa',
    'appdev/js/controllers/mensaje',
    'appdev/js/controllers/nueva-reserva',
    'appdev/js/controllers/oficina',
    'appdev/js/controllers/plan',
	'appdev/js/controllers/recado',
	'appdev/js/controllers/reserva',
	'appdev/js/controllers/reporte',
    'appdev/js/controllers/seguimiento',
    'appdev/js/controllers/usuario'
], function(angular){
	angular.element(document).ready(function(){
		angular.bootstrap(document, ['CentrosApp']);
	});
});