angular.module('nx').directive('preloadImage', function () {
    
    return {
        restrict: 'A',
        priority: 100,
        scope: {
            onloading: '=',
            onloaded: '='
        },
        link: function (scope, element, attrs) {

            scope.$watch(function()
            {
                return attrs.src;

            }, function(newImage, oldImage) 
            {
                if(newImage !== oldImage)
                {
                    scope.onloading();
                }

            });

            element.bind('load', function () 
            {
                scope.onloaded();
            });

        }
    };

});