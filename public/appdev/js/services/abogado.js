/**
 * Created by QuispeRoque on 02/05/17.
 */
define(['app', 'angular'], function (app, angular) {
    app.factory('AbogadoSrv', abogado_dataservice);
    abogado_dataservice.$inject = ['$http'];
    function abogado_dataservice($http) {

        return {
            getAllData: getAllData
        };

        function getAllData(params) {
            return $http({
                url: '/get-all-data-abogado',
                method: 'GET',
                params: (params !== undefined) ? params : null
            }).then(fnComplete).catch(fnFailed).finally(fnFinally);

            function fnComplete(r) {
                return r.data;
            }

            function fnFailed(error) {
                console.error('XHR Failed for abogado_dataservice.' + error.data);
                console.warn(error);
            }

            function fnFinally() {
            }
        }

    }
});