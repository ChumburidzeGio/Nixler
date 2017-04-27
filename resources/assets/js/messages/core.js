angular.module('messages', []).controller('ThreadsCtrl', [
	'$http', '$scope', function ($http, $scope) {

		var vm = this;
		vm.threads = window.threads;
		vm.page = 1;

	}])


.controller('ThreadCtrl', [
	'$http', '$scope', '$interval', function ($http, $scope, $interval) {

		var vm = this;
		vm.thread = window.thread;
		vm.sending = false;

		vm.message = function(){

			if(vm.sending) return false;

			vm.load(1);

			vm.sending = true;

			$http.post('/im/' + vm.thread.id, {
				message: vm.text
			}).then(function(response){
				vm.pushMessage(response.data);
				vm.text = '';
				vm.sending = false;
			});

		}

		vm.pushMessage = function(message, isNew = true){

	        if (!vm.thread.messages.inArray(function(e) { return e.id === message.id; })) {
	            vm.thread.messages.push(message);
	            if(isNew) vm.thread.messages_count += 1;
	        }

		}

		vm.isMore = function(){
			return !!(vm.thread.messages_count > vm.thread.messages.length);
		}

		vm.load = function(dir){

			if(vm.thread.messages.length > 0){
				var id = (dir == '-1' ? vm.thread.messages.slice('-1')[0].id : vm.thread.messages[0].id);
			} else {
				var id = 0;
				dir = 1;
			}

			$http.get('/im/'+vm.thread.id+'/load?dir=' + dir + '&id=' + id).then(function(response){
				angular.forEach(response.data, function(i,k){
					vm.pushMessage(i, false);
				});
			});
		}

		$interval(function(){
			vm.load(1);
		}, 30000);


	}])


.directive("ngHeight", ['$window', function ($window) {
		return function(scope, element, attrs) {
			var nd = 240;
			if(attrs.max) nd = attrs.max;

			element.css('max-height',  $window.innerHeight - nd +'px');
			angular.element($window).bind("resize", function() {
				var ch = $window.innerHeight - nd;
				element.css('max-height', ch +'px');
            //if(ch < element[0].height) {
            //console.log(ch, $window.innerHeight, element[0]);
            //    element.css('width', '100%');
            //}
        });
		};
	}]);




