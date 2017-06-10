@extends('users.settings.layout')

@section('layout')

	<div class="_z013 _bgw _brds2 _mb15 _clear">

		<div class="_p15 _bb1 _posr">
			<h1 class="_fs18 _ci _lh1 _telipsis _m0">
				@lang('My Products')
			</h1>
		</div>

		<div class="_clear">
			
			<span class="_clear _pl15 _pr15 _mt5 _mb10 _c2">

				<div class="row">
					<div class="{{ auth()->user()->getMeta('has_sku') ? 'col-xs-4' : 'col-xs-6' }}">Product</div>
					<div class="col-xs-2">Price</div>
					@if(auth()->user()->getMeta('has_sku'))
					<div class="col-xs-2">SKU</div>
					@endif
					<div class="col-xs-1">Likes</div>
					<div class="col-xs-1">Sales</div>
					<div class="col-xs-1">Stock</div>
					<div class="col-xs-1">Public</div>
				</div>

			</span>

			@foreach($products as $product)
			<a class="_lim _clear _pl15 _pr15 _hvrl _bt1 _bcg" href="{{ route('product.edit', ['id' => $product->id]) }}">

				<div class="row">
					<div class="{{ auth()->user()->getMeta('has_sku') ? 'col-xs-4' : 'col-xs-6' }}">
						<img src="{{ $product->photo('similar') }}" class="_z013 _brds2 _dib _left" height="30" width="30" alt="{{ $product->title }}">

						<div class="_pl15 _pr15 _oh _pt5">
							<span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs15">
								{{ $product->title }}
							</span>
						</div>
					</div>

					<span class="_oh col-xs-2 _pt5">{{ $product->price_formated }}</span>
					@if(auth()->user()->getMeta('has_sku'))
						<span class="_oh col-xs-2 _pt5">{{ $product->sku }}</span>
					@endif
					<span class="_oh col-xs-1 _pt5">{{ $product->likes_count }}</span>
					<span class="_oh col-xs-1 _pt5">{{ $product->sales_count }}</span>
					<span class="_oh col-xs-1 _pt5">{{ $product->in_stock }}</span>
					<span class="_oh col-xs-1 _pt5">{{ $product->is_active ? 'Yes' : 'No' }}</span>
				</div>

			</a>
			@endforeach

		</div>

	</div>
@endsection