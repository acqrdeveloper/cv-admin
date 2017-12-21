define(['app'], function(app){
	app.factory('OficinaSrv', service);
	service.$inject = ['$http'];

	function service($http){
		return {
			ban: function(params){
				return $http({
					url: '/oficina/anulacion',
					method: 'POST',
					data: params
				});
			},
			getBan: function(oficina_id){
				return $http({
					url: '/oficina/anulacion',
					method: 'GET',
					params: {'oficina_id': oficina_id}
				});
			},
			deleteBan: function(r){
				var p = angular.copy(r);
				delete p.dia_nombre;
				return $http({
					url: '/oficina/anulacion',
					method: 'DELETE',
					params: p
				});
			},
			search: function search(params){
				return $http({
					url: '/oficina/search',
					params: params
				});
			},
			send: function send(params){
				var u = '/oficina'; var m = 'POST';
				if(params.id !== undefined){
					u = u + '/' + params.id;
					m = 'PUT';
					delete params.id;
				}

				return $http({
					url: u,
					method: m,
					data: params
				});
			},
			updateStatus: function updateStatus(params){
				return $http({
					url: '/oficina/' + params.id + '/status',
					method: 'PUT',
					data: {'estado': params.estado}
				});
			}
		};
	}
});
