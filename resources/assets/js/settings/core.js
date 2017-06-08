angular.module('settings', [])

.controller('AccountSettingsCtrl', [
	'$http', '$scope', '$timeout', '$window', 'ngDialog', '$filter', function ($http, $scope, $timeout, $window, ngDialog, $filter) {

		var vm = this;
		vm.cities = $filter('orderBy')(window.cities, 'label');;
		
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
	'$http', '$scope', function ($http, $scope) {

		var vm = this;
		vm.cities = window.cities;
		vm.delivery_full = window.settings.delivery_full;
		vm.has_return = window.settings.has_return;
		vm.policy = window.settings.policy;
		vm.location_id = window.settings.location_id;
		vm.price = window.settings.price;
		vm.window_from = window.settings.window_from;
		vm.window_to = window.settings.window_to;

	}]);