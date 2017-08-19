angular.module('nx').factory('httpRequestInterceptor', function () {
  return {
        request: function (config) {
            config.headers['X-Token'] = window.nx.csrfToken;
            return config;
        }
    };
});

angular.module('nx').config(['$httpProvider', '$compileProvider', function ($httpProvider, $compileProvider) {

    XMLHttpRequest.prototype.setRequestHeader = (function (sup) {
        return function (header, value) {
            if ((header === "__XHR__") && angular.isFunction(value))
                value(this);
            else
                sup.apply(this, arguments);
        };
    })(XMLHttpRequest.prototype.setRequestHeader);

    $httpProvider.interceptors.push('httpRequestInterceptor');

    $compileProvider.debugInfoEnabled(false);
    //$compileProvider.commentDirectivesEnabled(false);
    //$compileProvider.cssClassDirectivesEnabled(false);

}]);