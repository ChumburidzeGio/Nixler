(function(window){

	window.nxt = function(name, options) 
	{
		if(typeof fbq === "function")
		{
			fbq('track', name, options);
		}
	};

}(window));