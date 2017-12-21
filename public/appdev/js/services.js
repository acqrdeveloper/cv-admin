define(['app'], function(app){
	app
	.factory('filterCompany', ['$http', function($http){
		return function(value){
			return $http.get('/empresas/search?view=minimal&empresa_nombre='+value).
						then(function(response){
							return response.data;
						});
		};
	}])
	.factory('listYears', function(){
		var years = [];
		for(var i = (new Date()).getFullYear(); i>=2015; i--){
			years.push(i);
		}
		return years;
	})
	.factory('listAllYears', function(){
		var years = ['Todos'];
		for(var i = (new Date()).getFullYear(); i>=2015; i--){
			years.push(i);
		}
		return years;
	})
	.factory('listTipoNota', function(){
		var tipopago = [
			{value:"-", name:"Tipo Nota"},
			{value:"CREDITO", name:"CREDITO"},
			{value:"DEBITO", name:"DEBITO"}
		];
		return tipopago;
	})
	.factory('listtipopago', function(){
		var tipopago = [
			{value:"-", name:"Tipo Pago"},
			{value:"EFECTIVO", name:"Efectivo"},
			{value:"DEPOSITO", name:"Deposito"},
			{value:"GARANTIA", name:"Garantia"},
			{value:"FACTURA", name:"Factura"},
		];
		return tipopago;
	})
	.factory('listestadofactura', function(){
		var estadofactura = [
			{value:"-", name:"Estado"},
			{value:"PENDIENTE", name:"Pendiente"},
			{value:"PAGADA", name:"Pagada"},
			{value:"ANULADA", name:"Anulada"}
		];
		return estadofactura;
	})
	.factory('listEstadoEmpresa', function(){
		var estados = [
            {value: '-', name: 'Estado Cliente'},
            {value: 'A', name: 'Activo'},
            {value: 'I', name: 'Inactivo'},
            {value: 'P', name: 'Pendiente'},
            {value: 'E', name: 'Eliminado'},
		];
		return estados;
	})
	.factory('listciclo', function(){
		var ciclo = [
			{value:"-", name:"Ciclo"},
			{value:"QUINCENAL", name:"Quincenal"},
			{value:"MENSUAL", name:"Mensual"}
		];
		return ciclo;
	})
	.factory('listreporte', function(){
		var reporte = [
			//{value:"-", name:"Correspondencia"},
			//{value:"-", name:"Llamadas"},
			{value:"empresaactual", name:"Empresas Situacion Actual"},
			{value:"auditorio", name:"Auditorios"},
			{value:"payment", name:"Pagos"},
			{value:"invoicepayed", name:"Comprobantes Pagados"},
			{value:"ownmissing", name:"Saldos y Excedentes"},
			{value:"monthpay", name:"Cuadres"},
			{value:"guarantee", name:"Garantias"},
			{value:"paymentintime", name:"Pagos en Fecha"},
			{value:"sunatcomprobante", name:"Sunat Comprobantes"},
			{value:"cuadremensual", name:"Cuadre Mensual"},
			{value:"localvisitantes", name:"Visitantes"},
			{value:"coffeebreak", name:"Coffeebreak"}
		];
		return reporte;
	})
	.factory('listMonths', function(){
		return ['Todos','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
	})
	.factory('dataservice', dataservice);

	dataservice.$inject = ['$http'];

	function dataservice($http){

		var service = {
			getCentral: getCentral,
			searchCompanies: searchCompanies,
			searchCorrespondencia: searchCorrespondencia
		};

		return service;

		function getCentral(id){
			return $http({
				url: '/central/' + id
			});
		}

		function searchCompanies(params){
			return $http({
				url: '/empresas/search',
				method: 'GET',
				params: params
			}).then(success).catch(fail);

			function success(response){
				return response.data;
			}

			function fail(response){
				console.log(response);
			}
		}

		function searchCorrespondencia(params){
			return $http({
				url: '/correspondencia/search/' + params.anio + '/' + params.mes + '/' + params.estado,
				method: 'GET',
				params: params
			});
		}
	}
});