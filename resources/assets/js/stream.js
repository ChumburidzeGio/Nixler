angular.module('stream', [])

.controller('StreamCtrl', [
	'$http', '$scope', '$timeout', function ($http, $scope, $timeout) {

		var vm = this;
		vm.stream = window.stream;
		vm.filters = {
			price: {}
		};

		vm.isMore = function(){
			return vm.stream.nextPageUrl;
		};

		vm.load = function(){

			if(!vm.stream.nextPageUrl){
				return false;
			}

			$http.post(vm.stream.nextPageUrl).then(function(response){
				angular.forEach(response.data.items, function(i,k){
					vm.stream.items.push(i);
				});

				vm.stream.nextPageUrl = response.data.nextPageUrl;
			});
		};

		var calcPriceRange = function(prices) {
			return {
				min: Math.min(...Object.keys(prices)),
				max: Math.max(...Object.keys(prices)),
				avg: calculateAvg(prices),
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

			range.push({value: 0});

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

			if(window.facets) {

				vm.grices = window.facets;

				vm.filters.price = calcPriceRange(vm.grices);

				vm.priceSliderOptions = {
					ceil: vm.filters.price.max,
					floor: vm.filters.price.min,
					step: 1,
					precision: 1,
					showTicksValues: true,
					stepsArray: pricesToRange(vm.grices, vm.filters.price.min, vm.filters.price.max)
				};
				
			}

		});

	}]);