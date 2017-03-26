angular.module('order', [])

.controller('OrderCtrl', [
	'$http', '$scope', '$timeout', function ($http, $scope, $timeout) {

		var vm = this;
		vm.addresses = window.addresses;
		vm.variants = window.variants;
		vm.product_price = window.price;
		vm.address = vm.addresses[0];
		vm.variant = vm.variants[0];
		vm.quantity = 1;

		vm.product_price_total = function() {
			return parseFloat(vm.product_price) * vm.quantity;
		};

		vm.price = function() {
			return parseFloat(vm.address.shipping.price) + vm.product_price_total();
		};

		vm.more = function(){
			if(vm.quantity < 99) vm.quantity += 1;
		}

		vm.less = function(){
			if(vm.quantity > 1) vm.quantity -= 1;
		}

		vm.canSubmit = function(){
			return (vm.address && vm.address.shipping && vm.addresses.length);
		}

}]);