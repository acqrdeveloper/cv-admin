define([], function(window){
	var obj = {
		getRolesByEntity: function(entity){
			var roles = JSON.parse( sessionStorage.getItem("roles") );
			return roles[entity];
		},
		check: function(idx){
			return JSON.parse( sessionStorage.getItem("roles") ).indexOf(idx*1) >= 0;
		}
	};
	return obj; 
});