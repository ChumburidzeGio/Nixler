angular.module('nx').controller('ShowCtrl', ['$http', 'ngDialog', '$timeout', function ($http, ngDialog, $timeout) {

    var vm = this;
    vm.product = window.nx.product;
    vm.quantities = vm.product.quantities;
    vm.variants = vm.product.variants;

    var mainPhotoId = 0;

    vm.media = {
        loading: false,
        initialize: function()
        {
            mainPhotoId = 0;
        },
        next: function()
        {
            mainPhotoId = vm.product.media[(mainPhotoId + 1)] ? (mainPhotoId + 1) : 0;
        },
        active: function()
        {
            return vm.product.media[mainPhotoId].full;
        },
        isActive: function(id)
        {
            return mainPhotoId == id;
        },
        isLoading: function()
        {
            return vm.media.loading;
        },
        select: function(id)
        {
            mainPhotoId = id;
        },
        all: function()
        {
            return vm.product.media;
        },
        onLoading: function()
        {
            $timeout(function(){
                vm.media.loading = true;
            });
        },
        onLoaded: function()
        {
            $timeout(function(){
                vm.media.loading = false;
            });
        }
    };

    vm.media.initialize();

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

                vm.copy = function() {

                  var copyElement = document.createElement("span");
                  copyElement.appendChild(document.createTextNode(vm.link));
                  angular.element(document.body.append(copyElement));

                  // select the text
                  var range = document.createRange();
                  range.selectNode(copyElement);
                  window.getSelection().removeAllRanges();
                  window.getSelection().addRange(range);

                  // copy & cleanup
                  document.execCommand('copy');
                  window.getSelection().removeAllRanges();
                  copyElement.remove();

              }

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

    nxt('ViewContent', {
        value: vm.product.price,
        content_ids: vm.product.id
    });
    
}]);