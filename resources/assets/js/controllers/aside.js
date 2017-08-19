angular.module('nx').controller('AsideCtrl', ['$rootScope', function ($rootScope) 
{
    var vm = this;

    vm.init = function()
    {
        vm.aside_opened = false;
    }

    vm.init();

    $rootScope.$on('aside_open', function (event) 
    {
        vm.aside_opened = true;
    });

    $rootScope.$on('aside_close', function (event, alert) 
    {
        vm.aside_opened = false;
    });

    vm.close = function()
    {
        $rootScope.$emit('aside_close');
    }
}]);
