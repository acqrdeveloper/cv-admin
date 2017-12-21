define(['app'], function(app){

	app.factory('EmpresaSrv', service);
	service.$inject = ['$http'];
	function service($http){
		return {
			changeInterview: changeInterview,
			create: create,
			postponeInterview: postponeInterview,
			search: search,
			searchInSunatByName: searchInSunatByName,
			searchInSunatByRuc: searchInSunatByRuc,
			updateState: updateState
		};

		function create(params){
			return $http({
				url: "/empresa",
				method: "POST",
				data: params
			});
		}

		function changeInterview(empresa_id, tipo){
			return $http({
				url: '/empresa/' + empresa_id + '/entrevista',
				method: 'PUT',
				data: {estado:tipo}
			});
		}

		function postponeInterview(empresa_id, params){
			return $http({
				url: '/empresa/' + empresa_id + '/entrevista',
				method: 'PUT',
				data: params
			});
		}

		function search(params){
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

		function searchInSunatByRuc(ruc){			
			return $http.get('http://service.facturame.pe/consultaruc/' +  ruc, {
				headers: {
					'Accept':'application/json',
					'X-CSRF-TOKEN': undefined,
					'X-Requested-With': undefined
				}
			});
		}

		function searchInSunatByName(name){
			return $http.get('http://service.facturame.pe/consultarsocial/' +  name, {
				headers: {
					'Accept':'application/json',
					'X-CSRF-TOKEN': undefined,
					'X-Requested-With': undefined
				}
			});
		}

		function updateState(empresa_id, params){
			return $http({
				url: '/empresa/' + empresa_id + '/estado',
				method: 'PUT',
				data: params
			});
		}
	}
});