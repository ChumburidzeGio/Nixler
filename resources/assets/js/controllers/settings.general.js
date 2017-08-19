angular.module('nx').controller('AccountSettingsCtrl', ['$http', '$window', 'ngDialog', '$filter', function ($http, $window, ngDialog, $filter) {

	var vm = this;
	vm.cities = $filter('orderBy')(window.cities, 'label');
	
	vm.deactivateAccount = function()
	{
		var confirmed = ngDialog.openConfirm({
			template:'deactivateAccountConfirm.html',
			showClose: 0,
			className: 'ngdialog-theme-plain'
		});

		confirmed.then(function() 
		{
			$http.post('/settings/account/deactivate').then(function()
			{
				$window.location.href = '/';
			});
		});
	};
	
}]);