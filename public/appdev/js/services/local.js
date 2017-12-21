define(['app'], function(app){
	app.factory('LocalSrv', service);
	service.$inject = ['$http'];

	function service($http){
		return {
			search: function search(params){
				return $http({
					url: '/local/search'
				});
			},
			send: function send(params){
				var u = '/local'; var m = 'POST';
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
			}
		};
	}
});
