
/**
 * Next, we will create a fresh Angular application instance and attach it to
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

 require('./vendor/angular-timeago');
 require('ng-dialog');
 require('angularjs-scroll-glue');
 require('angular-numeric-input');
 require('angular-tooltips');
 require('angularjs-slider');

 require('./utils/core');
 require('./product/core');
 require('./comments/core');
 require('./messages/core');
 require('./nav/nav.ctrl');
 require('./order/core');
 require('./settings/core');
 require('./user/core');
 require('./stream');
 require('./collections');

 
 var app = angular.module('nx', [
 	'utils',
    'products', 
    'nav', 
    'ng-currency', 
    'monospaced.elastic', 
    'ngTagsInput', 
    'confirm-click', 
    'angular-sortable-view', 
    'selector',
    'rzModule',
    'comments', 
    'ngSanitize', 
    'yaru22.angular-timeago', 
    'ngDialog',
    'messages',
    'luegg.directives',
    'ui.numericInput',
    'order',
    '720kb.tooltips',
    'stream',
    'settings',
    'user',
    'collections'
]);

require('./utils/preloader');

 app.factory('httpRequestInterceptor', function () {
 	return {
 		request: function (config) {
 			config.headers['X-Token'] = window.nx.csrfToken;
 			return config;
 		}
 	};
 });


 app.config(['$httpProvider', '$compileProvider', function ($httpProvider, $compileProvider) {

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

app.filter('boolean', function() {
    return function(input) {
        return input ? 1 : 0;
    }
});

 app.filter('to_trusted', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
}]);


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