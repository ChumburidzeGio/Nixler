angular.module('settings', [])

.controller('AccountSettingsCtrl', [
	'$http', '$scope', '$timeout', '$window', 'ngDialog', '$filter', function ($http, $scope, $timeout, $window, ngDialog, $filter) {

		var vm = this;
		vm.cities = $filter('orderBy')(window.cities, 'label');
		
		vm.deactivateAccount = function(){

			var confirmed = ngDialog.openConfirm({
				template:'deactivateAccountConfirm.html',
				showClose: 0,
				className: 'ngdialog-theme-plain'
			});

			confirmed.then(function() {
				$http.post('/settings/account/deactivate').then(function(){
					$window.location.href = '/';
				});
			});

		};

	}])

.controller('ShipSettingsCtrl', [
	'$filter', '$scope', function ($filter, $scope) {

		var vm = this;
		let settings = window.settings;
		vm.cities = $filter('orderBy')(window.cities, 'label');
		vm.delivery_full = settings.delivery_full;
		vm.has_return = settings.has_return;
		vm.has_sku = settings.has_sku;
		vm.policy = settings.policy;
		vm.location_id = settings.location_id;
		vm.price = settings.price;
		vm.window_from = settings.window_from;
		vm.window_to = settings.window_to;

	}]);