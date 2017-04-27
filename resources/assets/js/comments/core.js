angular.module('comments', []).controller('CommentsCtrl', [
	'$http', '$scope', function ($http, $scope) {

		var vm = this;
		vm.comments = window.comments;
		vm.target = window.comments_target;
		vm.sending = false;
		vm.page = 1;

		vm.commentPush = function(){

			if(vm.sending) return false;

			vm.sending = true;

			$http.post('/comments', {
				prev: vm.comments.length ? vm.comments[0].id : null,
				target: vm.target,
				text: vm.comment_text
			}).then(function(response){
				vm.comments.unshift(response.data);
				vm.comment_text = '';
				$scope.$parent.vm.comments_count += 1;
				vm.sending = false;
			}, function(){
				vm.sending = false;
			});
		}

		vm.isMore = function(){
			return !!($scope.$parent.vm.comments_count > vm.comments.length);
		}

		vm.load = function(){

			vm.page += 1;

			$http.get('/comments?page=' + vm.page + '&target=' + vm.target).then(function(response){
				angular.forEach(response.data, function(i,k){
					vm.comments.push(i);
				});
			});
		}

		vm.delete = function(comment){

			$http.delete('/comments/' + comment.id + '?target=' + vm.target).then(function(response){
				angular.forEach(response.data, function(i,k){
					$scope.$parent.vm.comments_count -= 1;
					angular.forEach(vm.comments, function(item,key){
	                    if(item.id == comment.id){
	                        vm.comments.splice(key,1);
	                    }
	                });
				});
			});
		}

	}]);