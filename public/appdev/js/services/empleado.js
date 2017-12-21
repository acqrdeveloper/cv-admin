define(['app'], function(app){

	app.factory('EmpleadoSrv', service);

	service.$inject = ['$http'];

	function service($http){

		return {
			delete: deleteEmployee,
			send: SaveEmployee
		};


		function deleteEmployee(empresa_id,empleado_id){
			return $http({
				url: "/empresa/" + empresa_id + "/empleado/" + empleado_id, 
				method: "DELETE"
			});
		}

		function SaveEmployee(empresa_id, params){
			var method = "POST", curl = "/empresa/" + empresa_id + "/empleado";

			if(params.id !== undefined && params.id > 0){
				curl = curl + "/" + params.id;
				method = "PUT";
				delete params.id;
				delete params.estado;
				delete params.empresa_id;
				delete params.fecha;
				delete params.opcion_central_id;
			}

			return $http({
				url: curl,
				method: method,
				data: params
			});
		}
	}
});