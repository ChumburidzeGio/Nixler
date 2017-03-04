
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 window.Angular = require('angular');
 require('ng-currency')
 require('angular-elastic')
 require('ng-tags-input')
 require('confirm-click')
 require('angular-sortable-view')
 require('angular-selector')

 require('./utils/core');
 require('./product/core');
 require('./nav/nav.ctrl');

 var app = angular.module('nx', ['utils','products', 'nav', 
 	'ng-currency', 'monospaced.elastic', 'ngTagsInput', 'confirm-click', 'angular-sortable-view', 'selector']);


 app.factory('httpRequestInterceptor', function () {
 	return {
 		request: function (config) {
 			config.headers['X-Token'] = window.Laravel.csrfToken;
 			return config;
 		}
 	};
 });


 app.config(['$httpProvider', function ($httpProvider) {

    XMLHttpRequest.prototype.setRequestHeader = (function (sup) {
        return function (header, value) {
            if ((header === "__XHR__") && angular.isFunction(value))
                value(this);
            else
                sup.apply(this, arguments);
        };
    })(XMLHttpRequest.prototype.setRequestHeader);

    $httpProvider.interceptors.push('httpRequestInterceptor');

}]);