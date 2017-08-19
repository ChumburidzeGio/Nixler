angular.module('nx').controller('ProfileCtrl', ['$http', function ($http) 
{
		var vm = this;

		vm.stream = window.stream;
		
		vm.deletePhoto = function(id)
		{
			document.getElementById(id+'-delete-form').submit();
		};

		vm.isMore = function()
		{
			return vm.stream.nextPageUrl;
		};

		vm.load = function()
		{
			if(!vm.stream.nextPageUrl)
			{
				return false;
			}

			$http.post(vm.stream.nextPageUrl).then(function(response)
			{
				angular.forEach(response.data.items, function(i,k)
				{
					vm.stream.items.push(i);
				});

				vm.stream.nextPageUrl = response.data.nextPageUrl;
			});
		};
}]);