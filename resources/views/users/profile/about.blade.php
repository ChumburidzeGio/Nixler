@extends('users.profile.layout')

@section('user_content')
<div class="row">

<div class="col-xs-12 _mt80">

@if($user->products_count)

	@if($user->merchant_terms)
		<span class="_fs15 _ttu _cb _mb10 _clear">@lang('Terms & Conditions')</span>

		<p class="_clear" show-more more="@lang('Read more')" less="@lang('Show less')" height="180">
			{!! nl2br($user->merchant_terms) !!}
		</p>

		@if($user->getMeta('delivery_full', 0))
		<span class="_cbl">
			<i class="material-icons _mr10 _fs20 _va5">check</i>
			@lang('Delivery accross the country')
		</span><br>
		@endif

		@if($user->getMeta('has_return', 0))
		<span class="_cbl">
			<i class="material-icons _mr10 _fs20 _va5">check</i>
			@lang('We accept return')
		</span>
		@endif

	@endif

	<span class="_fs15 _ttu _cb _mb10 _clear _mt30">@lang('Contact details')</span>

	<p class="_clear">

		@if($user->phone)
			<span class="_c3">Phone:</span> <a class="_cbl" href="tel:+{{ $user->phone }}">+{{ $user->phone }}</a><br> 
		@endif

		@if($user->getMeta('website'))
			<span class="_c3">Website:</span> 
				<a class="_cbl" href="{{ $user->getMeta('website') }}" target="_blank" rel="nofollow noopener">
					{{ parse_url($user->getMeta('website'), PHP_URL_HOST) }}
				</a>
			</span> 
		@endif

		@if($user->city) <span class="_c3">City:</span> <span class="_c3">{{ $user->city->name }}</span> @endif
	</p>
@endif

</div>

</div>
@endsection
