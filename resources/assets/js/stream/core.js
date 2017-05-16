angular.module('stream', [])

.controller('StreamCtrl', [
	'$http', '$scope', '$timeout', function ($http, $scope, $timeout) {

		var vm = this;
		vm.stream = window.stream;
		vm.filters = {
			price: {}
		};

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
		};

		var calcPriceRange = function(prices) {
			return {
				min: Math.min(...Object.keys(prices)),
				max: Math.max(...Object.keys(prices)),
				avg: calculateAvg(prices)
			};
		};

		var calculateAvg = function(prices) {

			var sum = 0, total = 0;

			angular.forEach(prices, function(amount, price){
				sum += price * amount;
				total += amount;
			});

			return Math.round(sum / total);
		};

		var pricesToRange = function(prices, min, max) {

			var range = [], step = 1, 
				maxAmountPercent = 50 / Math.max(...Object.values(prices));


			for (var i = min; i <= max; i++) {

				step = Math.round(i / 100);

				if(prices[i] !== undefined) {
					range.push({value: i, legend: (prices[i] * maxAmountPercent)});
				} else if((i % step) == 0) {
					range.push({value: i});
				}
			}

			return range;

		}

		$timeout(function(){

			vm.grices = {
				55: 23,
				78: 50,
				5200: 14,
				1200: 12,
				580: 25,
				125: 50,
				300: 26,
				450: 1,
				854: 6,
				200: 2,
				510: 1,
				311: 1,
				412: 3,
				53: 6,
				2552: 2,
				686: 1,
				123: 1,
				745: 3,
				4567: 3,
				9785: 3,
			};

			vm.filters.price = calcPriceRange(vm.grices);

			vm.priceSliderOptions = {
				ceil: vm.filters.price.max,
				floor: vm.filters.price.min,
				step: 1,
				precision: 1,
				showTicksValues: true,
				stepsArray: pricesToRange(vm.grices, vm.filters.price.min, vm.filters.price.max)
			};

		});

	}]);