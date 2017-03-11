angular.module('products').controller('ShowCtrl', [
    '$http', '$scope', 'ngDialog', function ($http, $scope, ngDialog) {

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

}]);