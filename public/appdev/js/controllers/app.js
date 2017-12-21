define(['app','roles','io'], function(app, roles,io){

	app.controller('AppCtrl', controller);

	controller.$inject = ['$http','$uibModal','$timeout','$state'];

	function controller($http, modal, $timeout, $state){
		var vm = this;
		
		vm.roles = roles;

		vm.mycrm = window.iVariables.mycrm.length;
		vm.user = window.iVariables.user;
        vm.inbox = window.iVariables.inbox;
        vm.notifications = [];
        vm.count_notify = 0;
		vm.openAccounts = function(){
			modal.open({
				animation: true,
				templateUrl: '/templates/modals/banco/cuentas.html',
				controller: ['$uibModalInstance', function(instance){
					var $ctrl = this;
					$ctrl.close = function(){
						instance.dismiss('cancel');
					};
				}],
				controllerAs: '$ctrl'
			}).result.then(function(){}, function(){});
		};

		vm.decreaseNotifyCount = function(notify){
			vm.count_notify--;

			if((notify.read*1) === 0){
				console.log(notify);
				$http.put('/notificacion/' + notify.id).then(function(r){}).catch(function(e){});				
			}

		};

		$http.get('/notificacion').then(function(r){
			if(r.data.length>0){
				angular.forEach(r.data, function(notify){
					var obj = JSON.parse(notify.params);
					obj.id = notify.id;
					obj.read = notify.read;
					vm.notifications.push(obj);
					if((notify.read*1)===0){
						vm.count_notify++;
					}
				});
			}
		});

		if( (window.iVariables.user.rold_id * 1) !== 8){
	        var socket = io.connect(window.iVariables.ws);

	        socket.on('connect', function(){
	        	//setWS(socket.id);
	        	console.log("connected to websocket");
	        });

	        socket.on('emitSystem', function(data){
	        	$timeout(function(){
		        	vm.inbox++;
		        	vm.count_notify++;
		        	vm.notifications.unshift(data);
	        	}, 100);

	        	try {
	        		launchNotification(data.from, data.message, false, function(){
	        			$timeout(function(){
	        				vm.count_notify--;
	        				$state.go(data.module);
	        			}, 100);
	        		});
	        	} catch (e) {
	        	}
	        });
		}

        function launchNotification(titleText, bodyText, onVisible, onClick){

	        onVisible = onVisible | false;

	        var hidden, visibilityChange;
	        if (typeof document.hidden !== 'undefined') {
	            // Opera 12.10, Firefox >=18, Chrome >=31, IE11
	            hidden = 'hidden';
	            visibilityChangeEvent = 'visibilitychange';
	        } else if (typeof document.mozHidden !== 'undefined') {
	            // Older firefox
	            hidden = 'mozHidden';
	            visibilityChangeEvent = 'mozvisibilitychange';
	        } else if (typeof document.msHidden !== 'undefined') {
	            // IE10
	            hidden = 'msHidden';
	            visibilityChangeEvent = 'msvisibilitychange';
	        } else if (typeof document.webkitHidden !== 'undefined') {
	            // Chrome <31 and Android browser (4.4+ !)
	            hidden = 'webkitHidden';
	            visibilityChangeEvent = 'webkitvisibilitychange';
	        }

	        if(onVisible && !document[hidden]){
	            return false;
	        }

	        var notification = window.Notification || window.mozNotification || window.webkitNotification;

	        if ('undefined' === typeof notification)
	            alert('Web notification not supported');
	        else
	            notification.requestPermission(function(permission){});

	        if ('undefined' === typeof notification)
	            return false;       //Not supported....
	    
	        var noty = new notification(
	                titleText, {
	                    body: bodyText,
	                    dir: 'auto', // or ltr, rtl
	                    lang: 'EN', //lang used within the notification.
	                    tag: 'notificationPopup', //An element ID to get/set the content
	                    icon: '/images/icon.png' //The URL of an image to be used as an icon
	                }
	        );

	        noty.onclick = function(){
	            console.log('notification.Click');
	            onClick();
	            noty.close();
	        };

	        noty.onerror = function () {
	          console.log('notification.Error');
	        };
	        noty.onshow = function () {
	          console.log('notification.Show');
	        };
	        noty.onclose = function () {
	          console.log('notification.Close');
	        };

	        return true;
        }
	}

});