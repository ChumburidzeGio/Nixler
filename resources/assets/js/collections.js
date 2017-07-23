angular.module('collections', [])

.controller('CollectionUpdateCtrl', ['$http', '$scope', function ($http, $scope) {

	var vm = this;
	
	vm.products = window.collection.items;

	vm.isPrivate = window.collection.isPrivate;

	vm.privacyOptions = window.collection.privacyOptions;
	
	vm.productIds = [];

	vm.results = [];

	vm.searchQuery = '';

	vm.searchProccessing = false;

	/**
	 * Search products by query
	 */
	 $scope.$watch(function(){return vm.searchQuery;}, function() {

	 	console.log('collections.searchWatcher');

	 	vm.filter();

	 	if(vm.searchProccessing) {
	 		return false;
	 	}

	 	if(vm.searchQuery.length < 2) {
	 		vm.results = [];
	 		return false;
	 	}

	 	vm.searchProccessing = true;

	 	$http.post('/cl/productSearch', {query: vm.searchQuery}).then(function(response){

	 		vm.results = response.data;

	 		vm.searchProccessing = false;

	 		vm.filter();

	 		console.log('collections.searchWatcher.success');

	 	}, function(){

	 		vm.results = [];

	 		vm.searchProccessing = false;

	 		console.log('collections.searchWatcher.error');

	 	});

	 });

	/**
	 * Add search result to collection
	 */
	 vm.add = function(item) {

	 	console.log('collections.add');

	 	var index = vm.results.indexOf(item);

	 	item.selected = true;

	 	vm.products.push(item);

	 	vm.results.splice(index, 1);

	 	vm.productIds.push(item.id);

	 };

	/**
	 * Soft remove item from collection if it's new or restore if soft removed
	 */
	 vm.remove = function(item) {

	 	console.log('collections.remove');

	 	var index = vm.products.indexOf(item);

	 	if(item.selected !== false) {

	 		var cindex = vm.productIds.indexOf(item.id);

	 		vm.productIds.splice(cindex, 1);

	 		return item.selected = false;

	 	}

	 	item.selected = true;

	 	vm.productIds.push(item.id);

	 	console.log('collections.remove.select');
	 };

	/**
	 * Clear search query and results
	 */
	 vm.clearSearch = function() {

	 	console.log('collections.clearSearch');

	 	vm.results = [];

	 	vm.searchQuery = '';

	 	vm.filter();

	 };

	/**
	 * Filter search results in the way to not show selected
	 * products and products to remove deseleted
	 */
	 vm.filter = function() {

	 	console.log('collections.filter');

	 	angular.forEach(vm.products, function(product, pi) {

	 		if(product.selected == undefined) {
	 			product.selected = true;
	 		}

	 		if(product.selected == false) {
	 			vm.products.splice(pi, 1);
	 		}

	 	});

	 	vm.productIds = [];

	 	angular.forEach(vm.products, function(product, pi) {

	 		if(product.selected){
	 			vm.productIds.push(product.id);
	 		}

	 		angular.forEach(vm.results, function(result, ri) {

	 			if(product.id == result.id) {
	 				vm.results.splice(ri, 1);
	 			}

	 		});

	 	});

	 };

	/**
	 * Delete collection
	 */
    vm.delete = function () {
        document.getElementById('delete-form').submit();
    }

}]);