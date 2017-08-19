angular.module('nx').directive("ngHeight", ['$window', function ($window) {
    return function(scope, element, attrs) {
        var nd = 240;
        if(attrs.max) nd = attrs.max;

        element.css('max-height',  $window.innerHeight - nd +'px');
        angular.element($window).bind("resize", function() {
            var ch = $window.innerHeight - nd;
            element.css('max-height', ch +'px');
            //if(ch < element[0].height) {
            //console.log(ch, $window.innerHeight, element[0]);
            //    element.css('width', '100%');
            //}
        });
    };
}]);