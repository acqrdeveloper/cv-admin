define(['app'], function(app){
	app.filter('diffTime', function(){
		return function(time){
			if(time.length <= 0){
				return time;
			} else {
				var parts = time.split(":");
				var shora = '';
				var sminuto = '';

				// minuto
				if(parseInt(parts[1])>0){
					sminuto = parseInt(parts[1]) + ' minutos';
				}

				// horas
				if(parseInt(parts[0]) === 1){
					shora = parseInt(parts[0]) + ' hora';
				} else if(parseInt(parts[0]) > 1){
					shora = parseInt(parts[0]) + ' horas';
				}

				if(shora.length > 0 && sminuto.length > 0){
					shora = shora + ' y ';
				}

				return shora + sminuto;
			}
		};
	})
    .filter('findInArray', function(){
        return function(data, column, value){
            if (typeof(data) === 'undefined' || typeof(column) === 'undefined' || typeof(value) === 'undefined' ) {
                return -2;
            }
            var idx = -1;
            for(var i=0;i<data.length;i++){
                if(data[i][column] === value){
                    idx = i;
                    break;
                }
            }
            return idx;
        };
    })
	.filter('getColumn', function(){
		return function(data, key){
            if (typeof(data) === 'undefined' || typeof(key) === 'undefined') {
                return [];
            }
            if(data.length>0){
            	var r = [];
            	for(var i=0;i<data.length;i++){
            		r.push(data[i][key]);
            	}
            	return r;
            }
            return [];
		};
	})
	.filter('to_trusted', ['$sce', function($sce) {
        return function(text) {
            return $sce.trustAsHtml(text);
        };
    }])
    .filter('sumByKey', function() {
        return function(data, key) {
            if (typeof(data) === 'undefined' || typeof(key) === 'undefined') {
                return 0;
            }
            var sum = 0;
            if( data ){
                if( data.length > 0 ){
                    for (var i = data.length - 1; i >= 0; i--) {
                        sum += (data[i][key]*1);
                    }
                }
            }
            return sum;
        };
    })
    .filter('split', function() {
        return function(input, splitChar, splitIndex) {
            // do some bounds checking here to ensure it has that index
            return input.split(splitChar)[splitIndex];
        };
    })
    .filter('propsFilter', function() {
      return function(items, props) {
        var out = [];

        if (angular.isArray(items)) {
          var keys = Object.keys(props);

          items.forEach(function(item) {
            var itemMatches = false;

            for (var i = 0; i < keys.length; i++) {
              var prop = keys[i];
              var text = props[prop].toLowerCase();
              if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                itemMatches = true;
                break;
              }
            }

            if (itemMatches) {
              out.push(item);
            }
          });
        } else {
          // Let the output be the input untouched
          out = items;
        }

        return out;
      };
    });
});