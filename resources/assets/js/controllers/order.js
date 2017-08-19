angular.module('nx').controller('OrderCtrl', ['$http', '$timeout', '$filter', function ($http, $timeout, $filter) 
{
		var vm = this;
		
		vm.payment_method = 'cod';

		vm.payload = null;

		vm.pm = function(type)
		{
			return vm.payment_method == type;
		}

		vm.spm = function(type)
		{
			vm.payment_method = type;
		}

		vm.cities = $filter('orderBy')(window.nx.cities, 'label');

		vm.pprice = window.nx.price;

		angular.forEach(vm.cities, function(k, i)
		{
			if(k.id == window.nx.city_id) 
			{
				vm.city = k;
			}
		});

		vm.price = function()
		{
			return vm.city ? parseFloat(vm.pprice + parseFloat(vm.city.shipping_price)).toFixed(2) : undefined;
		};

		braintree.dropin.create({
			authorization: window.nx.payment.authcode,
			container: '#dropin-container'
		}, 
		function (createErr, instance) 
		{
			document.querySelector('#dropin-container .braintree-sheet__text').innerHTML = window.nx.payment.headerText;
			document.querySelector('#dropin-container [data-braintree-id="number-field-group"] .braintree-form__label').innerHTML = window.nx.payment.cardNumber;
			document.querySelector('#dropin-container [data-braintree-id="expiration-date-field-group"] .braintree-form__label').textContent = window.nx.payment.exDate;

			vm.submit = function(event, form) 
			{
				event.preventDefault();

				instance.requestPaymentMethod(function (err, payload) 
				{
					if(payload) 
					{
						$timeout(function()
						{
							vm.payload = payload.nonce;
							
							$timeout(function(){ form.commit(); }, 20);
							
						});

					}
				});

			};
		});

		if(typeof fbq === "function")
		{
			fbq('track', 'InitiateCheckout');
		}

}]);