angular.module('nx').filter('boolean', function() {
    return function(input) {
        return input ? 1 : 0;
    }
});