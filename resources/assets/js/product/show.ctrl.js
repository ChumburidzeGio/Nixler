angular.module('products').controller('ShowCtrl', [
    '$http', '$scope', 'ngDialog', '$timeout', function ($http, $scope, ngDialog, $timeout) {
      
        var vm = this;
        vm.product = window.product;
        vm.media = {};
        vm.mainPhoto = 0;
        vm.quantities = vm.product.quantities;
        vm.variants = vm.product.variants;

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

        $http.post('/products/'+vm.product.id+'/like').then(function(response){

            vm.product.liked = response.data.success;

            if(vm.product.liked){ 
              vm.product.likes_count++;
          } else {
              vm.product.likes_count--;
          }

      });

    }

    vm.share = function(){

        ngDialog.open({

            template: '/tmp/share.html',
            controller: function() {

                var vm = this;
                vm.url = window.location.href;

            },
            controllerAs: 'vm',
            showClose: 1,
            className: 'ngdialog-theme-plain'
        });

    }

    $timeout(function(){
      vm.quantity = 1;
      vm.variant = vm.variants.length ? vm.variants[0] : 0;
  }, 100);
    
}]);