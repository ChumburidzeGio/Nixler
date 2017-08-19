angular.module('nx').directive("ngFormCommit", [function(){
    return {
        require:"form",
        link: function($scope, $el, $attr, $form) {
            $form.commit = function() {
                $el[0].submit();
            };
        }
    };
}]);