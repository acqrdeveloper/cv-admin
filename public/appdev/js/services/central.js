define(['app'], function(app){

	app.factory('CentralSrv', service);

	service.$inject = ['$http'];
	
	function service($http){

		return {
			activePbx: activePbx,
			deletePbx: deletePbx,
			deleteOption: deleteOption,
			getFreeNumbers: getFreeNumbers,
			getNumbers: getNumbers,
			save: save,
			saveOption: saveOption
		};

		function activePbx(params){
			return $http({
				url: '/pbx',
				method: 'PUT',
				params: params
			});
		}

		function deletePbx(params){
			return $http({
				url: '/pbx',
				method: 'DELETE',
				params: params
			});
		}

		function deleteOption(params){
			return $http({
				url: '/pbx/option',
				method: 'DELETE',
				params: params
			});
		}

		function getFreeNumbers(){
			return $http.get('/pbx/numbers');
		}

		function getNumbers(customer_id){
			return $http.get("/empresa/" + customer_id + "/pbx");
		}


		function save(params){
			return $http({
				url: "/pbx",
				method: "POST",
				transformRequest: angular.identity,
	            headers: {
	                'Content-Type': undefined
	            },
				data: params
			});
		}

		function saveOption(params){
			return $http({
				url: '/pbx/option',
				method: 'POST',
				data: angular.copy(params)
			});
		}
	}
});