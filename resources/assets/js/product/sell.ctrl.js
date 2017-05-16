angular.module('products').controller('SellCtrl', [
    '$http', 'anchorSmoothScroll', '$scope', function ($http, anchorSmoothScroll, $scope) {

        var vm = this;
        vm.media = [];


        vm.variants = {
            items: [],
            count: function(){
                return this.items.length;
            },
            add: function() {
                this.items.push({
                    name: '',
                    price: null,
                    in_stock: null
                });
            },
            remove: function(variant) {
                this.items.splice(this.items.indexOf(variant), 1);
            },
            price: function() {

                var prices = this.items.map(function(i){
                    return i.price;
                }).filter(function onlyUnique(value, index, self) { 
                    return self.indexOf(value) === index && value !== null;
                });

                var min = Math.min(...prices);
                var max = Math.max(...prices);
                
                var price = '0.00';

                if(min != 'Infinity' && max != '-Infinity'){
                    
                    if(!min + "".indexOf('.')) min = min + '.00';

                    if(!max + "".indexOf('.')) max = max + '.00';

                    price = (min == max) ? min : min + ' - ' + max;
                }

                return price;
            },
            in_stock: function() {

                return this.items.map(function(i){
                    return i.in_stock;
                }).filter(function (value) { 
                    return value !== null;
                }).reduce(function(a, b) { 
                    return a + b; 
                }, 0);

            }
        };

        $scope.$watch(function(){
            return vm.variants.items;
        }, function() {
           vm.price = vm.variants.price();
           vm.in_stock = vm.variants.in_stock();
        }, true);

        vm.selectMedia = function (event) {
            var files = event.target.files;
            if (files.length === 0) return;
            for (var i = 0; i < files.length; i++) {
                vm.uploadMedia(files[i]);
            }
        };

        vm.deleteMedia = function (media) {

            media.uploading = true;
            $http.post('/products/' + vm.id + '/photos/' + media.id).then(function(response){
                vm.media.splice(vm.media.indexOf(media), 1);
            }, function(){
                media.uploading = false;
            });
        }

        vm.delete = function () {
            document.getElementById('delete-form').submit();
        }


        vm.uploadMedia = function (file) {

            if(file.size > 2000000){
                //$scope.$emit('alert', 'Some text');
                return false;
            }

            var index = vm.media.push({ "uploading": true }) - 1;

            var item = vm.media[index];

            var fd = new FormData();
            fd.append('file', file);


            $http({
                method  : 'POST',
                url     : '/products/' + vm.id + '/photos',
                data    : fd,
                headers : {
                    'Content-Type': undefined,
                    "__XHR__": function() {
                        return function (xhr) {
                         xhr.upload.addEventListener("progress", function (event) {

                            $scope.$apply(function(){
                                item.uploading = (((event.loaded / 5 * 4) / event.total) * 100);
                            });

                        });
                     };
                 }
             }
         }).then(function(response){
            item.id = response.data.id;
            item.thumb = response.data.thumb;
            item.uploading = false;
            console.log(vm.media);
        }, function (response) {
            vm.media.splice(index, 1);
        });

     }
 }]);