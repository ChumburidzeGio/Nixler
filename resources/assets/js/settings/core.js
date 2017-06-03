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

	}]);