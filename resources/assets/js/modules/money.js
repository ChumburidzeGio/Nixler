angular.module('nx').filter('money', ['$rootScope', '$filter', '$sce', function ($rootScope, $filter, $sce) {

	return function (amount, currency, rtl) {

		var amount = Math.round(amount),
		currency = currency || window.nx.currencySymbol,
		signAmount = amount < 0 ? '-' : '',
		rtl = rtl || false;

		return (window.nx.currency == 'USD') ? currency + ' ' + amount : amount + ' ' + currency;
	};
}]);