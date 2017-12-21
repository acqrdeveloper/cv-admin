define(['app'], function(app){

	app.controller('EmpCWCtrl', controller);

	controller.$inject = ['CoworkingSrv','toastr'];

	function controller(mySrv, toastr){
		var vm = this;
		var auxs = {
			empresa_id: empresa.id,
			locales: ([{'id':0,'nombre':'Seleccione Local'}]).concat(angular.copy(window.iVariables.locales))
		};

		var fn = {
			search: function(){
				mySrv.getByHQ( params.local_id ).then(function(r){
					var places = r.data; 
					var data = {};					
					var xmax =0, ymax =0;
					r.data.forEach(function(i){
						coord = i.nombre_o;
						letter = coord.substr(0,1);
						
						numberX = coord.substr(1)*1;
						numberY = ( ( letter.toUpperCase().charCodeAt(0)) - 64 );
						ymax    = ymax > numberY ? ymax : numberY;
						xmax    = xmax > numberX ? xmax : numberX;
						data[numberY+"-"+numberX] = i;
					});
					cowork = [];
					for(var y = 1; y <= ymax; y++ ){
						det = [];
						for(var x=1; x<= xmax; x++ ){
							if( data[y+"-"+x] === undefined ){
								data[y+"-"+x] = {};
							}
							det.push(data[y+"-"+x]);
						}
						cowork.push(det);
					}

					vm.auxs.places = cowork;
				}).catch(function(e){
					console.log(e);
					//toastr.error(e.data.error, 'Error al obtener los espacios');
				});
			},
			takeIt: function(place){
				var p = {};
				if(place.empresa_id > 0 && (place.empresa_id*1) !== (empresa.id*1)){
					toastr.error('No puedes escoger un asiento de otro cliente.');
					return false;
				} else if( (place.empresa_id*1) === (empresa.id*1)){
					p.empresa_id = 0;
				} else {
					p.empresa_id = (empresa.id*1);
				}

				var c = null;

				if(p.empresa_id > 0){
					c = confirm('¿Desea reservar el espacio ' + place.nombre_o + '?');
				} else {
					c = confirm('¿Desea liberar el espacio ' + place.nombre_o + '?');
				}

				if(c){
					mySrv.update(place.id, p.empresa_id).then(function(r){
						console.log(r.data);
						fn.search();
						toastr.success('Datos guardados');
					}).catch(function(e){
						console.log(e);
						toastr.error(e.data.message, 'Error al reservar asiento');
					});
				}
			}
		};
		var params = {
			local_id: 0
		};
		angular.extend(vm, {
			'auxs': auxs,
			'fn': fn,
			'params': params
		});
	}
});