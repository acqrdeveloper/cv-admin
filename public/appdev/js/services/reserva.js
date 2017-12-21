define(['app'], function(app){
	app.factory('ReservaSrv', service);
	service.$inject = ['$http'];
	function service($http){
		return {
			active: active,
			changeState: changeState,
			manage: manage,
			search: search,
			searchByDate: searchByDate,
			create: create,
			getAvailable: getAvailable,
			getAvailableV1: getAvailableV1,
			getDetails: getDetails,
			getCocheras: getCocheras,
			getPrice: getPrice,
			uploadList: uploadList,
			update: update
		};

		function changeState(id, estado){
			return $http({
				url: '/reserva/' + id + '/' + estado,
				method: 'PUT'
			});
		}

		function create(p){
			return $http({
				url: '/reserva',
				method: 'POST',
				data: p
			});
		}

		function getAvailable(p){
			return $http({
                url: '/oficina/disponibilidad',
                method: 'GET',
                params: p
            });
		}

		function getAvailableV1(p){
			return $http({
                url: '/oficina/disponibilidad.v1',
                method: 'GET',
                params: p
            });
		}

		function getPrice(local_id, modelo_id, plan_id){
			return $http({
				url: '/reserva/auditorio/' + local_id + '/' + modelo_id +'/' + plan_id,
				method: 'GET'
			});
		}

		function getCocheras(reserva_id, p){
			return $http({
                url: '/cochera/'+reserva_id+'/'+p.fecha+'/'+p.local_id+'/'+p.hini+'/'+p.hfin,
                method: 'GET'
            });
		}

		function getDetails(reserva_id){
			return $http({
				url: '/reserva/' + reserva_id + '/detalle',
				method: 'GET'
			});
		}

		function search(params){
			return $http({
				url: '/reserva/search',
				method: 'GET',
				params: params
			});
		}

		function searchByDate(date, params){
			return $http({
				url: '/reserva/search/' + date,
				method: 'GET',
				params: params
			});
		}

		function uploadList(reserva_id, list){
			return $http({
				url: '/asistencia/' + reserva_id + '/massive',
				method: 'POST',
				data: {estructura: list}
			});
		}

		function active(id){
			return $http({
				url: '/reserva/' + id + '/A',
				method: 'PUT'
			});
		}

		function manage(u, m, d){
			return $http({
				url: u,
				method: m,
				data: d
			});
		}

		function update(id, p){
			return $http({
				url: '/reserva/' + id,
				method: 'PUT',
				data: p
			});
		}
	}
});