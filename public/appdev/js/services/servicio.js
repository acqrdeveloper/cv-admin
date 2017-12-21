define(['app'], function(app){
	app.factory('EmpServicioSrv', service);
	service.$inject = ['$http'];
	function service(http){
		return {
			addResource: function(params){
				return http({
					url: '/servicio/' + params.empresa_id + '/' + params.anio + '/' + params.mes,
					method: 'POST',
					data: params
				});
			},
			addService: function(params){
				return http({
					url: '/servicio/empresa/' + params.empresa_id,
					method: 'POST',
					data: params
				});
			},
			changeCiclo: function(params){
				return http({
					url: '/empresa/' + params.empresa_id + '/ciclo',
					method: 'PUT',
					data: params 
				});
			},
			changeComprobante: function(params){
				return http({
					url: '/empresa/' + params.empresa_id + '/comprobante',
					method: 'PUT',
					data: params 
				});
			},
			editContract: function(params){

				var urll = '/empresa/' + params.empresa_id + '/contrato';

				if(params.renew !== undefined && params.renew === 1){
					urll = '/empresa/' + params.empresa_id + '/renovarcontrato';
					params.plan_id = params.plan_dst;
					delete params.plan_src;
					delete params.plan_dst;
				}

				return http({
					url: urll,
					method: 'PUT',
					data: params
				});

			},
			deleteService: function(params){
				return http({
					url: '/servicio/empresa/' + params.empresa_id + '/' + params.servicio_id,
					method: 'DELETE'
				});
			},
			getService: function(params){
				return http({
					url: '/servicio/' + params.empresa_id + '/' + params.anio + '/' + params.mes
				});
			},
			getServices: function(params){
				return http({
					url: '/servicio/empresa/' + params.empresa_id
				});
			},
			scheduleDelete: function(params){
				return http({
					url: '/empresa/' + params.empresa_id + '/schedule',
					method: 'DELETE',
					params: params 
				});
			}
		};
	}
});