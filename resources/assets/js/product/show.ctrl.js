angular.module('products').controller('ShowCtrl', [
    '$http', '$scope', 'ngDialog', '$timeout', function ($http, $scope, ngDialog, $timeout) {

        var vm = this;
        vm.media = {};
        vm.mainPhoto = 0;
        vm.quantities = window.quantities;
        vm.variants = window.variants;

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
            if(vm.liked) vm.likes_count++;
            else vm.likes_count--;
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

    vm.selectDefaults = function(){
      vm.quantity = 1;
      vm.variant = vm.variants.length ? vm.variants[0].id : 0;
    }

    $timeout(function(){
      vm.selectDefaults();
    }, 100);
    
}]);