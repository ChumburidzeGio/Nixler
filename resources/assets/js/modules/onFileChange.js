angular.module('nx').directive('onFileChange', function onFileChange() {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            var onChangeHandler = scope.$eval(attrs.onFileChange);
            element.bind('change', onChangeHandler);
        }
    };
});