angular.module('stream', [])

.controller('StreamCtrl', [
	'$http', '$scope', function ($http, $scope) {

		var vm = this;
		vm.stream = window.stream;

		vm.isMore = function(){
			return vm.stream.meta.pagination.links.next;
		};

		vm.load = function(){

			if(!vm.stream.meta.pagination.links.next){
				return false;
			}

			$http.post(vm.stream.meta.pagination.links.next).then(function(response){
				angular.forEach(response.data.data, function(i,k){
					vm.stream.data.push(i);
				});

				vm.stream.meta = response.data.meta;
			});
		}

	}]);