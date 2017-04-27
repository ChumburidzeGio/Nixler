angular.module('products').controller('SellCtrl', [
    '$http', 'anchorSmoothScroll', '$scope', function ($http, anchorSmoothScroll, $scope) {

        var vm = this;
        vm.media = [];

        vm.selectMedia = function (event) {
            var files = event.target.files;
            if (files.length === 0) return;
            for (var i = 0; i < files.length; i++) {
                vm.uploadMedia(files[i]);
            }
        };

        vm.createFunction = function (tag) {
            return $http.post('/products/tags/create', {
                tag: tag
            }).then(function(response){
                return response.data;
            });
        };

        vm.remoteConfig = {
            url: "/products/tags/search",
            transformResponse: function (data) {
                console.log(data, angular.fromJson(data));
                return angular.fromJson(data);
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