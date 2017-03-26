angular.module('address', [])

.controller('ShipSettingsCtrl', [
	'$http', '$scope', function ($http, $scope) {

		var vm = this;
		vm.cities = window.cities;

	}])

.controller('AddressSettingsCtrl', [
	'$http', '$scope', function ($http, $scope) {

		var vm = this;
		vm.cities = window.cities;
		vm.location_id = null;

		vm.delete = function(url){

			$http.delete(url).then(function(){
				window.location.reload(); 
			});
		}

	}]);