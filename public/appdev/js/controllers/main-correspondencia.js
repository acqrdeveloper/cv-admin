define(['app'], function(app){
	app.controller('mainCorrespondencia', controller);

	controller.$inject = ['$state','$scope'];

	function controller($state,$scope){
		var vm = this;
		
		$scope.$on('$stateChangeSuccess', function(){
			var current = $state.current.url.split("/");

			switch(current[1]){
				case 'recado':
				case 'correspondencia':
					vm.tab = current[1];
					break;
				default:
					$state.go('recado-correspondencia.recado');
			}
		});
	}
});