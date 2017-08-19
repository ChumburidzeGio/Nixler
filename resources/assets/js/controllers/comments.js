angular.module('nx').controller('CommentsCtrl', ['$http', '$scope', function ($http, $scope) {

	var vm = this;

	vm.comments = window.nx.product.comments;

	vm.target = window.nx.product.id;

	vm.sending = false;

	vm.page = 1;

	vm.selectMedia = function (event) 
	{
		var files = event.target.files;

		if (!files[0]) return;

		vm.comment_media = files[0];

		if(vm.comment_media.size > 2000000)
		{
			vm.comment_media = null;

			return false;
		}

		var reader = new FileReader();

		reader.onload = function(event) 
		{
			vm.comment_media_b = event.target.result;

			$scope.$apply();
		};

		reader.readAsDataURL(vm.comment_media);
	};

	vm.commentPush = function()
	{
		if(vm.sending) 
		{
			return false;
		}

		vm.sending = true;

		var fd = new FormData();

		if(vm.comment_media) 
		{
			fd.append('file', vm.comment_media);
		};

		fd.append('prev', vm.comments.length ? vm.comments[0].id : null);

		fd.append('target', vm.target);

		fd.append('text', vm.comment_text);

		$http({
			method  : 'POST',
			url     : '/comments',
			data    : fd,
			headers : {
				'Content-Type': undefined
			}
		}).then(function(response)
		{
			vm.comments.unshift(response.data);

			vm.comment_text = '';

			$scope.$parent.vm.comments_count += 1;

			vm.sending = false;

			vm.removeAttachment();
		}, 
		function()
		{
			vm.sending = false;
		});
	}

	vm.isMore = function()
	{
		return !!($scope.$parent.vm.comments_count > vm.comments.length);
	}

	vm.load = function()
	{
		vm.page += 1;

		$http.get('/comments?page=' + vm.page + '&target=' + vm.target).then(function(response)
		{
			angular.forEach(response.data, function(i,k)
			{
				vm.comments.push(i);
			});
		});
	}

	vm.delete = function(comment)
	{
		$http.delete('/comments/' + comment.id + '?target=' + vm.target).then(function(response)
		{
			angular.forEach(response.data, function(i,k)
			{
				$scope.$parent.vm.comments_count -= 1;

				angular.forEach(vm.comments, function(item,key)
				{
					if(item.id == comment.id)
					{
						vm.comments.splice(key,1);
					}
				});
			});
		});
	};

	vm.removeAttachment = function()
	{
		vm.comment_media_b = false;
		vm.comment_media = false;
	};

}]);