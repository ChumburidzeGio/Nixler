angular.module('nx').directive('loader', function () {
    return {
        scope: {
            inclass: '@',
            inwidth: '@'
        },
        link: function (scope, element, attrs) {
            if (scope.inwidth == undefined) scope.inwidth = 2;
        },
        template: '<div class="_loader _a0" style=" margin: 0 auto;" ng-class="inclass"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="{{inwidth}}" stroke-miterlimit="10"></circle></svg></div>'
    };
});