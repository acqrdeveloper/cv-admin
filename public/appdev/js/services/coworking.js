define(['app'], function(app){
	app.factory('CoworkingSrv', service);
	service.$inject = ['$http'];
	function service($http){
		return {
			getByHQ: function(id){
				return $http({
					url: '/oficina/coworking/' + id
				});
			},
			update: function(oficina_id, empresa_id){
				return $http({
					url: '/oficina/coworking/' + oficina_id + '/' + empresa_id,
					method: 'PUT'
				});
			}
		};
	}
});