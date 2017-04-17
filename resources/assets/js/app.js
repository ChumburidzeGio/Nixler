
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 window.Angular = require('angular');
 require('angular-elastic');
 require('ng-tags-input');
 require('confirm-click');
 require('ng-currency');
 require('angular-sortable-view');
 require('angular-selector');
 require('angular-sanitize');
 require('angular-timeago');
 require('ng-dialog');
 require('angularjs-scroll-glue');
 require('angular-numeric-input');
 require('angular-tooltips');

 require('./utils/core');
 require('./product/core');
 require('./comments/core');
 require('./messages/core');
 require('./nav/nav.ctrl');
 require('./address/core');
 require('./order/core');
 require('./stream/core');

 var app = angular.module('nx', [
 	'utils',
    'products', 
 	'address', 
 	'nav', 
 	'ng-currency', 
 	'monospaced.elastic', 
 	'ngTagsInput', 
 	'confirm-click', 
 	'angular-sortable-view', 
 	'selector', 
 	'comments', 
 	'ngSanitize', 
 	'yaru22.angular-timeago', 
 	'ngDialog',
 	'messages',
 	'luegg.directives',
    'ui.numericInput',
    'order',
    '720kb.tooltips',
    'stream'
   ]);

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


app.filter('boolean', function() {
    return function(input) {
        return input ? 1 : 0;
    }
});

if (!Array.prototype.last){
    Array.prototype.last = function(){
        return this[this.length - 1];
    };
};

if (!Array.prototype.inArray){
    Array.prototype.inArray = function(comparer) { 
        for(var i=0; i < this.length; i++) { 
            if(comparer(this[i])) return true; 
        }
        return false; 
    }; 
};
