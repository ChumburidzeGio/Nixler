angular.module('settings', [])

.controller('AccountSettingsCtrl', [
	'$http', '$scope', '$timeout', '$window', 'ngDialog', function ($http, $scope, $timeout, $window, ngDialog) {

		var vm = this;
		vm.cities = window.cities;
		
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