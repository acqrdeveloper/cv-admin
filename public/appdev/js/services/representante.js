define(['app'], function(app){

	app.factory('RepresentanteSrv', service);

	service.$inject = ['$http'];

	function service($http){

		return {
			delete: deleteAgent,
			send: saveAgent,
			setLogin: setLogin
		};


		function deleteAgent(empresa_id, agente_id){
			return $http({
				url: "/empresa/" + empresa_id + "/representante/" +  agente_id, 
				method: "DELETE"
			});
		}

		function saveAgent(empresa_id, params){
			var method = "POST", curl = "/empresa/" + empresa_id + "/representante";

			if(params.id !== undefined && params.id > 0){
				curl = curl + "/" + params.id;
				method = "PUT";
				delete params.id;
				delete params.empresa_id;
				delete params.fecha;
			}

			return $http({
				url: curl,
				method: method,
				data: params
			});
		}

		function setLogin(empresa_id, agent_id){
			return $http({
				url: '/empresa/setLogin',
				method: 'PUT',
				data: {empresa_id: empresa_id, id: agent_id}
			});
		}
	}
});