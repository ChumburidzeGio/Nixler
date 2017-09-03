angular.module('nx').controller('OrderCtrl', ['$http', '$timeout', '$filter', function ($http, $timeout, $filter) 
{
		var vm = this;
		
		vm.payment_method = 'crd';

		vm.payload = null;

		vm.pm = function(type)
		{
			return vm.payment_method == type;
		}

		vm.spm = function(type)
		{
			vm.payment_method = type;
		}

		vm.cities = $filter('orderBy')(window.nx.cities, 'label');

		vm.pprice = window.nx.price;

		angular.forEach(vm.cities, function(k, i)
		{
			if(k.id == window.nx.city_id) 
			{
				vm.city = k;
			}
		});

		vm.price = function()
		{
			return vm.city ? parseFloat(vm.pprice + parseFloat(vm.city.shipping_price)).toFixed(2) : undefined;
		};

		nxt('InitiateCheckout');

}]);