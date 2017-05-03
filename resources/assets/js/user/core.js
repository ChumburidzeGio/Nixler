angular.module('user', [])

.controller('ProfileCtrl', function () {

		var vm = this;
		
		vm.deletePhoto = function(id){
			document.getElementById(id+'-delete-form').submit();
		};

});