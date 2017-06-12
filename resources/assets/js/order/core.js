angular.module('order', [])

.controller('OrderCtrl', [
	'$http', '$scope', '$timeout', '$filter', function ($http, $scope, $timeout, $filter) {

		var vm = this;
		
		vm.cities = $filter('orderBy')(window.cities, 'label');

		vm.pprice = window.price;

		angular.forEach(vm.cities, function(k, i){
			if(k.id == window.city_id) {
				vm.city = k;
			}
		});

		vm.price = function(){
			return vm.city ? (vm.pprice + parseFloat(vm.city.shipping_price)).toFixed(2) : undefined;
		};
}]);