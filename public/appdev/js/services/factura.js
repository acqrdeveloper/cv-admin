define(['app'], function(app){
	app.factory('FacturaSrv', service);
	service.$inject = ['$http'];
	function service($http){
		return {
			'Invoice': {
				getById: function(invoice_id){
					return $http({
		                url: "/factura/getone/"+invoice_id,
		                method: 'GET'
		            });
				}
			},
			'Note': {
				create: function(empresa_id, invoice_id, params){
					var p = angular.copy(params);
					p.empresa_id = empresa_id;
					return $http({
	                    url: '/factura/' + invoice_id + '/nota',
	                    method: 'POST',
	                    data: p
	                });
				}
			},
			'Payment': {
				pay: function(empresa_id, invoice_id, p){
					var params = angular.copy(p);
					params.empresa_id = empresa_id;
					return $http({
						url: '/factura/' + invoice_id + '/pagar',
						method: 'POST',
						data: params
					});
				},
				payWithGuarantee: function(empresa_id, invoice_id, p){
					var params = angular.copy(p);
					params.empresa_id = empresa_id;
					return $http({
						url: '/factura/' + invoice_id + '/pagar_garantia',
						method: 'POST',
						data: params
					});	
				}
			},
			'Guarantee': {
				get: function(empresa_id){
					return $http.get('/factura/garantia/' + empresa_id);
				}
			}
		};
	}
});