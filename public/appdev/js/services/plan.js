define(['app'], function(app){
	app.factory('PlanSrv', service);
	service.$inject = ['$http'];

	function service($http){
		return {
			search: function(params){
				return $http({
					url: '/plan/search',
					params: params
				});
			},
			send: function(params){
				var curl='/plan'; var m = 'POST';
				if(params.id !== undefined){
					curl = curl + '/' + params.id;
					m = 'PUT';
				}
				return $http({
					url: curl,
					method: m,
					data: params
				});
			},
			updateStatus: function(params){
				return $http({
					url: '/plan/' + params.id + '/estado',
					method: 'PUT',
					data: {'estado':params.estado}
				});
			}
		};
	}
});
