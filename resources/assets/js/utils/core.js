angular.module('utils', []).service('anchorSmoothScroll', function(){

        this.scrollTo = function(eID) {

        // This scrolling function 
        // is from http://www.itnewb.com/tutorial/Creating-the-Smooth-Scroll-Effect-with-JavaScript
        
        var startY = currentYPosition();
        var stopY = elmYPosition(eID);
        var distance = stopY > startY ? stopY - startY : startY - stopY;
        if (distance < 100) {
            scrollTo(0, stopY); return;
        }
        var speed = Math.round(distance / 80);
        if (speed >= 20) speed = 20;
        var step = Math.round(distance / 25);
        var leapY = stopY > startY ? startY + step : startY - step;
        var timer = 0;
        if (stopY > startY) {
            for ( var i=startY; i<stopY; i+=step ) {
                setTimeout("window.scrollTo(0, "+leapY+")", timer * speed);
                leapY += step; if (leapY > stopY) leapY = stopY; timer++;
            } return;
        }
        for ( var i=startY; i>stopY; i-=step ) {
            setTimeout("window.scrollTo(0, "+leapY+")", timer * speed);
            leapY -= step; if (leapY < stopY) leapY = stopY; timer++;
        }
        
        function currentYPosition() {
            // Firefox, Chrome, Opera, Safari
            if (self.pageYOffset) return self.pageYOffset;
            // Internet Explorer 6 - standards mode
            if (document.documentElement && document.documentElement.scrollTop)
                return document.documentElement.scrollTop;
            // Internet Explorer 6, 7 and 8
            if (document.body.scrollTop) return document.body.scrollTop;
            return 0;
        }
        
        function elmYPosition(eID) {
            var elm = document.getElementById(eID);
            var y = elm.offsetTop;
            var node = elm;
            while (node.offsetParent && node.offsetParent != document.body) {
                node = node.offsetParent;
                y += node.offsetTop;
            } return y;
        }

    };
    
})

.directive('onFileChange', function onFileChange() {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            var onChangeHandler = scope.$eval(attrs.onFileChange);
            element.bind('change', onChangeHandler);
        }
    };
})

.directive('loader', function () {
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
})

.directive('showMore', function() {
  return {
        restrict: 'A',
        transclude: true,
        template: [
            '<div class="show-more-container"><ng-transclude></ng-transclude></div>',
            '<a href="#" class="show-more-expand _c4">{{ more }}</a>',
            '<a href="#" class="show-more-collapse _c4">{{ less }}</a>',
        ].join(''),
        scope: {
            more: '@',
            less: '@',
            height: '@'
        },
        link: function(scope, element, attrs, controller) {
            var maxHeight = scope.height;
            var initialized = null;
            var containerDom = element.children()[0];
            var $showMore = angular.element(element.children()[1]);
            var $showLess = angular.element(element.children()[2]);

            scope.$watch(function () {
                // Watch for any change in the innerHTML. The container may start off empty or small,
                // and then grow as data is added.
                return containerDom.innerHTML;
            }, function () {
                if (null !== initialized) {
                    // This collapse has already been initialized.
                    return;
                }

                if (containerDom.clientHeight <= maxHeight) {
                    // Don't initialize collapse unless the content container is too tall.
                    return;
                }

                $showMore.on('click', function () {
                    element.removeClass('show-more-collapsed');
                    element.addClass('show-more-expanded');
                    containerDom.style.height = null;
                });

                $showLess.on('click', function () {
                    element.removeClass('show-more-expanded');
                    element.addClass('show-more-collapsed');
                    containerDom.style.height = maxHeight + 'px';
                });

                initialized = true;
                $showLess.triggerHandler('click');
            });
        },
  };
})

.filter('money', ['$rootScope', '$filter', '$sce', function ($rootScope, $filter, $sce) {

        return function (amount, currency, rtl) {

            var amount = Math.round(amount),
                currency = currency || window.nx.currencySymbol,
                signAmount = amount < 0 ? '-' : '',
                rtl = rtl || false;

            return (window.nx.currency == 'USD') ? currency + ' ' + amount : amount + ' ' + currency;
        };
    }])

.directive('ngEnter', function() {
    return function(scope, element, attrs) {
        element.bind("keydown", function(e) {
            if(e.which === 13 && !e.shiftKey) {
                scope.$apply(function(){
                    scope.$eval(attrs.ngEnter, {'e': e});
                });
                e.preventDefault();
            }
        });
    };
});