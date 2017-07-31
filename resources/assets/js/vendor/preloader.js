/**
 * License: MIT
 */
(function(angular) {
    'use strict';

    angular.module('angular-preload-image', []);

    angular.module('angular-preload-image').factory('preLoader', function () {
        return function (url, successCallback, errorCallback) {
            angular.element(new Image()).bind('load', function () {
                successCallback();
            }).bind('error', function () {
                errorCallback();
            }).attr('src', url);
        }
    });

    angular.module('angular-preload-image').directive('preloadImage', ['preLoader', function (preLoader) {
        return {
            restrict: 'A',
            terminal: true,
            priority: 100,
            link: function (scope, element, attrs) {
                scope.default = attrs.defaultImage || "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3wEWEygNWiLqlwAAABl0RVh0Q29tbWVudABDcmVhdGVkIHdpdGggR0lNUFeBDhcAAAAMSURBVAjXY/j//z8ABf4C/tzMWecAAAAASUVORK5CYII=";
                attrs.$observe('ngSrc', function () {
                    var url = attrs.ngSrc;
                    attrs.$set('src', scope.default);
                    preLoader(url, function () {
                        attrs.$set('src', url);
                    }, function () {
                        if (attrs.fallbackImage != undefined) {
                            attrs.$set('src', attrs.fallbackImage);
                        }
                    });
                })
    
            }
        };
    }]);
    
})(angular);