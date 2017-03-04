angular.module('products').controller('ShowCtrl', [
    '$http', '$scope', function ($http, $scope) {

        var vm = this;
        vm.media = {};
        vm.mainPhoto = 0;

        vm.media = {
        	add: function(id,mid){
	        	vm.media[id] = mid;
	        },
        	next: function(){
	        	vm.mainPhoto = vm.media[(vm.mainPhoto + 1)] ? (vm.mainPhoto + 1) : 0;
	        },
	        mainPath: function(){
	        	return vm.mediaBase+vm.media[vm.mainPhoto]+'/product/full.jpg'
	        }
        };

        vm.like = function(){
            $http.post('/products/'+vm.id+'/like').then(function(response){
                vm.liked = response.data.success;
            });
        }

 }]);