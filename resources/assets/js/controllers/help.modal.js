angular.module('nx').controller('HelpModal', ['$scope', function ($scope) {

	var vm = this;

	vm.opened = false;

	vm.toggle = function()
	{
		vm.opened = !vm.opened;
	}

	vm.isOpen = function()
	{
		return vm.opened;
	}

}]);