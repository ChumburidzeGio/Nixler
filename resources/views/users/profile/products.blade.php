@extends('users.profile.layout')

@section('user_content')
<div class="row">

	@if(count($data))

		@each('products.short-card', $data, 'product')

	@else

		<div class="_tac _pt15 _mt70 _c3">
			<h5 class="_fw400">@lang('There is no products to show.')</h5>
		</div>

	@endif

</div>
@endsection