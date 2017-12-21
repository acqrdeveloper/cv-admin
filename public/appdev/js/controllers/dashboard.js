define(['app', 'roles', 'angular'], function(app, roles, angular){
	return app.controller('DashboardCtrl', ['$scope','$http','$uibModal','toastr','listYears','listMonths','listciclo','filterCompany', function($scope,$http,$uibModal,toastr,listYears,listMonths,listciclo,filterCompany){

        $scope.roles = roles;

        var now = new Date();

        $scope.validateLoading = false;
        $scope.empresaLoading  = false;
        $scope.estadoLoading   = false;
        $scope.preferenciaestado = "";
        $scope.asesorempresa   = "";

        $scope.anios  = listYears;
        $scope.mes    = listMonths;
        $scope.ciclo  = listciclo;
        $scope.plan   = angular.copy(window.iVariables.planes);
        $scope.asesor = angular.copy(window.iVariables.asesores);
        $scope.usuarioR = angular.copy(window.iVariables.user);
        $scope.asesor.unshift({"id":"-","nombre":"Todos los Asesores"});
		$scope.plan.unshift({"id":"-","nombre":"Todos los Planes"});

        $scope.params1 = {
            anio: now.getFullYear(),
            mes: now.getMonth()+1,
            ciclo:{value:"-", name:"Ciclo"},
            plan:{id:"-", name:"Todos los Planes"},
            asesor:{"id":"-","nombre":"Todos los Asesores"}
        };

        $scope.params2 = {
            anio: now.getFullYear(),
            mes: now.getMonth()+1,
            ciclo:{value:"-", name:"Ciclo"}
        };

        var getByValue = function( arr, index, value ){
        	result = null;
        	arr.forEach(function(v,pos){
        		if(v[index]){
        			if( v[index] === value ){
        				result = pos;
        			}
        		}
        	});
        	return result;
		};

        var empresaDataInfo = function(json){
			if (json.load) {
				$scope.empresadata = json.rows;
				valplan         = {};
				valplanid 		= {};
				empresaplan     = [];
				valusuario      = {};
				empresausuario  = [];
				empresa_plan    = [];
				empresa_usuario = [];
				json.rows.forEach(function(a){
					if( !valplan[a.plan] ){
						valplan[a.plan] = 1;
						valplanid[a.plan] = a.plan_id;
						empresaplan.push( a.plan );
					}else{
						valplan[a.plan] = valplan[a.plan] + 1;
					}

					if( !valusuario[a.asesor] ){
						valusuario[a.asesor] = 1;
						empresausuario.push( a.asesor );
					}else{
						valusuario[a.asesor] = valusuario[a.asesor] + 1;
					}
				});
				empresaplan.forEach(function(a){
					empresa_plan.push({"id":a, "value":valplan[a], "did":valplanid[a] });
				});

				empresausuario.forEach(function(a){
					empresa_usuario.push({"id":a, "value":valusuario[a]});
				});
				$scope.empresa_plan = empresa_plan;
				$scope.empresa_usuario = empresa_usuario;
			}
        };

        var estadoDataInfo = function(json){
			if (json.load) {
				$scope.estadodata = json.rows;
				estados = {"A": "Activa", "S": "Suspendida", "E": "Eliminado", "P": "Pendiente"};
				estadosnum = {};
				estado_empresa = [];
				estadolist   = [];//["A","P","S","E"];

				json.rows.forEach(function(a){
					if(!estadosnum[a.preferencia_estado]){
						estadosnum[a.preferencia_estado] = 1;
						estadolist.push(a.preferencia_estado);
					}else{
						estadosnum[a.preferencia_estado] = estadosnum[a.preferencia_estado] + 1;
					}
				});

				estadolist.forEach(function(a){
					estado_empresa.push({"id":estados[a], "value":estadosnum[a], "estado": a});
				});
				$scope.estado_empresa = estado_empresa;
			}
        };

        //metodos $scope
        $scope.methods = {
        	filterEmpresaByAsesor:function(asesor){
				pos = getByValue( $scope.asesor, "nombre", asesor );
				if( pos >= 0 ){
					$scope.params1.asesor = $scope.asesor[pos];
					$scope.methods.getDataEmpresa();
				}
			},
			filterEmpresaByPlan:function(plan){
				pos = getByValue( $scope.plan, "id", plan );
				if( pos >= 0 ){
					$scope.params1.plan = $scope.plan[pos];
					$scope.methods.getDataEmpresa();
				}
			},
            export: function( tipo ) {
                var endpoint = ""; var p = {};
            	if( tipo == "empresa" ){
            		endpoint = "dashboard_empresasregistradas";
	                p = angular.copy($scope.params1);
	                p.asesor = p.asesor.nombre;
	                p.plan_id   = p.plan.id;
	                p.ciclo  = p.ciclo.value;
            	}else if( tipo == "estado" ){
            		endpoint = "dashboard_empresahistorial";
	                p    = angular.copy($scope.params2);
	                p.ciclo  = p.ciclo.value;
            	}

                window.open('/export/'+ endpoint +'?json=' + JSON.stringify(p),'');
            },
        	setEstado:function(est){
        		$scope.preferenciaestado = est;
        	},
            getDataInitial: function() {
                $scope.validateLoading = true;
                var param    = angular.copy($scope.params1);
                param.asesor = param.asesor.nombre;
                param.plan_id   = param.plan.id;
                param.ciclo  = param.ciclo.value;
                $http({
                    url: "/dashboard/initial",//+param.reporte
                    method: 'GET',
                    params: param
                }).then(function(r) {
                    if (r.data.load) {
                    	$scope.validateLoading = false;
						empresaDataInfo( r.data.data.empresa );
						estadoDataInfo( r.data.data.historial );
                    }else{
                    	$scope.validateLoading = false;
                    }
                }, function(){
                    toastr.error('No se pudo obtener los datos.');
                });
            },
            getDataEmpresa: function() {
                $scope.empresaLoading = true;
                var param    = angular.copy($scope.params1);
                param.asesor = param.asesor.nombre;
                param.plan_id   = param.plan.id;
                param.ciclo  = param.ciclo.value;
                $http({
                    url: "/dashboard/empresa",//+param.reporte
                    method: 'GET',
                    params: param
                }).then(function(r) {
                    if (r.data.load) {
                    	empresaDataInfo( r.data );
                    	$scope.empresaLoading = false;
                    }else{
                    	$scope.empresaLoading = false;
                    }
                }, function(){
                    toastr.error('No se pudo obtener los datos.');
                });
            },
            getDataEstado: function() {
                $scope.estadoLoading = true;
                var param    = angular.copy($scope.params2);
                param.ciclo  = param.ciclo.value;
                $http({
                    url: "/dashboard/empresaestado",//+param.reporte
                    method: 'GET',
                    params: param
                }).then(function(r) {
                    if (r.data.load) {
                    	estadoDataInfo( r.data );
                    	$scope.estadoLoading = false;
                    }else{
                    	$scope.estadoLoading = false;
                    }
                }, function(){
                    toastr.error('No se pudo obtener los datos.');
                });
            }
        };
        $scope.methods.getDataInitial();
    }]);
});