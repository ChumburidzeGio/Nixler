angular.module('order', [])

.controller('OrderCtrl', [
	'$http', '$scope', '$timeout', '$filter', function ($http, $scope, $timeout, $filter) {

		var vm = this;
		
		vm.cities = $filter('orderBy')(window.cities, 'label');

		vm.pprice = window.price;

		vm.price = function(){
			return vm.city ? (vm.pprice + parseFloat(vm.city.shipping_price)).toFixed(2) : undefined;
		};

		/*
		vm.addresses = window.addresses;
		vm.cities = $filter('orderBy')(window.cities, 'name');
		vm.variants = window.variants;
		vm.phones = window.phones;
		vm.product_price = window.price;
		vm.variant = vm.variants[0];
		vm.phone = vm.phones[0];
		vm.quantity = 1;
		vm.max_quantity = window.max_quantity;

		vm.product_price_total = function() {
			return parseFloat(vm.product_price) * vm.quantity;
		};

		vm.price = function() {
			return parseFloat(vm.address.shipping.price) + vm.product_price_total();
		};

		vm.more = function(){
			if(vm.quantity < vm.max_quantity) vm.quantity += 1;
		}

		vm.less = function(){
			if(vm.quantity > 1) vm.quantity -= 1;
		}

		vm.setAddress = function(){

			angular.forEach(vm.addresses, function(item){
				if(item.shipping){
					vm.address = item;
				};
			});

			if(!vm.address){
				vm.address = vm.addresses[0];
			}
			
		}

		vm.setAddress();*/

}]);