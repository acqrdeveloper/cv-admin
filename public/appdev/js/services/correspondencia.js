define(['app'], function(app){

	app.factory('CorrespondenciaSrv', service);

	service.$inject = ['$http'];

	function service($http){
		var s = {
			delete: deleteCorrespondencia,
			deliver: deliver,
			getHistory: getHistory,
			send: send
		};

		return s;

		function deleteCorrespondencia(id, obs){
			return $http({
				url: "/correspondencia/" + id, 
				method: "DELETE",
				data: {"observacion": obs}
			});
		}

		function deliver(params){
			return $http({
				url: "/correspondencia/deliver", 
				method: "PUT",
				data: params
			});
		}

		function getHistory(id){
			return $http.get("/correspondencia/" + id + "/history");
		}

		function send(params){
			
			var method = "POST", curl = "/correspondencia";

			if(params.id !== undefined && params.id > 0){
				curl = curl + "/" + params.id;
				method = "PUT";
				delete params.id;
				delete params.empresa_nombre;
				delete params.estado;
				delete params.empresa_id;
			}

			return $http({
				url: curl,
				method: method,
				data: params
			});
		}
	}
});