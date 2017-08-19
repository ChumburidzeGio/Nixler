angular.module('nx').controller('NavCtrl', ['$scope', function ($scope) 
{
        var vm = this;
        
        vm.openAside = function()
        {
            $scope.$emit('aside_open');
        }
 }]);