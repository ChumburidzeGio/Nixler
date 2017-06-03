@extends('layouts.app')

@section('content')

<div class="container" ng-controller="SellCtrl as vm">

<script>
window.product = <?php echo json_encode([
	'description' => old('description', $product->description),
	'media' => old('media', $product->media),
	'variants' => old("variants", $product->variants),
	'id' => $product->id,
	'price' => old('price', $product->price),
	'in_stock' => old('in_stock', $product->in_stock),
	'tags' => old('tags', $product->tags),
	'category' => old('category', $product->category_id),
	'categories' => $categories
]); ?>;
</script>

	<div class="row">

		<div class="col-sm-8 col-xs-12">
			
			@if(session('status'))
			<div class="_card _z013 _mb15 _bgw _p15 _fs15 _cb"> 
				{{ session('status') }}
				@if(session('buttons'))
				<div class="_clear _mt15">
					<a class="_btn _bga _cb _mr5" href="{{ url('new-product') }}">@lang('Add new')</a>
					<a class="_btn _cb _bg0 _pr5 _b1 _bcg _hvrl" href="{{ $product->url() }}">
						@lang('Go to product page') <i class="material-icons _ml5 _va7">chevron_right</i>
					</a>
				</div>
				@endif
			</div>
			@endif

			<form class="_mb2 _fg @cannot('create', $product) _posr @endcannot" name="product" action="{{ $product->link() }}" method="POST">
				{{ csrf_field() }}

				@cannot('create', $product)
				<div class="_posa _af _bgwt6 _zi999"></div>
				<div class="_bgw _p15 _fs15 _cb _posa _a0 _zi9999 _w70 _b1"> 
					@lang('Please for first add shipping information before you will able to publish new product')
					<div class="_clear _mt10">
						<a class="_btn _bga _cb _pr5 _hvrl" href="{{ route('shipping.settings', ['ref' => 'product-edit']) }}" id="shipping_settings_route">
							@lang('Go to shipping settings') <i class="material-icons _ml5 _va7">chevron_right</i>
						</a>
					</div>
				</div>
				@endcannot

				<div class="_z013 _bgw _mb10 _brds2">

					<div class="_p15 _bb1 _posr">
						<h1 class="_fs18 _ci _lh1 _telipsis _m0">
							@if($product->slug)
							@lang('Editing product')
							@else 
							@lang('Adding new product')
							@endif
						</h1>
						@if($product->slug)
						<a href="{{ $product->url() }}" class="_a3 _mr15" target="_blank">
							<i class="material-icons _fs18 _va4">open_in_new</i> @lang('View product')
						</a>
						@endif
					</div>

					<div class="_p15">


						<div class="row">

							<div class="col-sm-7 _mb15">
								<small class="_clear _pb5">@lang('Product name')</small>

								<input id="title" type="text" required name="title" minlength="2" maxlength="90" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" value="{{ old('title', $product->title) }}"> 

								@if ($errors->has('title'))
								<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('title') }}</span>
								@endif
							</div>

							<div class="col-sm-5 _mb15">
								<small class="_clear _pb5">@lang('Category')</small>

								<div id="category">
									<select selector
									model="vm.category"
									class="_b1 _bcg _brds3"
									require="true"
									value-attr="id"
									options="vm.categories"
									group-attr="zone"></select>
							</div>

							<input type="hidden" name="category" ng-value="vm.category">

							@if ($errors->has('category'))
							<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('category') }}</span>
							@endif
						</div>

					</div>




					<small class="_clear _pb5">
						@lang('Product description')
					</small>

					<textarea type="text" class="_b1 _bcg _fe _brds3 _mih70" msd-elastic ng-model="vm.description" rows="8" id="description" name="description"></textarea>

					@if ($errors->has('decription'))
					<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('decription') }}</span>
					@endif

					<div class="row _mt15">
						<div class="col-sm-3 _mb15">
							<small class="_clear _pb5">@lang('Price')</small>

							<input value="{{ $product->currency }} @{{ vm.price }}" class="_b1 _bcg _fe _brds3 _fes" readonly ng-if="vm.variants.count()" type="text">

							<input placeholder="" class="_b1 _bcg _fe _brds3 _fes" id="price" type="text" ng-currency min="1" ng-required="true" currency-symbol="{{ $product->currency }} " ng-model="vm.price" name="price" ng-if="!vm.variants.count()">

							@if ($errors->has('price'))
							<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('price') }}</span>
							@endif
						</div>
						<div class="col-sm-2 _mb15">
							<small class="_clear _pb5">@lang('In stock')</small>

							<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.in_stock" placeholder="" ui-numeric-input min="0" max="500" max-length="3" id="in_stock" name="in_stock" ng-readonly="vm.variants.count()">

							@if ($errors->has('in_stock'))
							<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('in_stock') }}</span>
							@endif
						</div>
						<div class="col-sm-7 _mb15">
							<small class="_clear _mb5">@lang('Tags')</small>

							<tags-input ng-model="vm.tags" placeholder="@lang('Tags for product')" replace-spaces-with-dashes="0" key-property="" add-on-paste="true"></tags-input>

							<input type="hidden" name="tags" ng-value="vm.tags | json">

							@if ($errors->has('tags'))
							<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('tags') }}</span>
							@endif
						</div>


						<div>
						<div class="col-xs-12 _mb15" ng-if="!vm.variants.count()">
							<span class="_clear _fs14 _mb5">
								@lang('Product Variants')
							</span>
							<span class="_clear _fs13 _c4 _crp" ng-click="vm.variants.add()" id="add-variants">
								<i class="material-icons _hvra _dib _mr5 _va4 _fs18">add</i> @lang('Add Variant')
							</span>
						</div>

						<div class="_bgwt8 _p10 _clear _brds3 _pb5 _ml5 _mr5" ng-if="vm.variants.count()">
							<span class="_clear _mb5 _fs13 _mb10">
								@lang('Product Variants')
								<span class="_right _crp" ng-click="vm.variants.add()" id="add-variant">
									<i class="material-icons _cb _hvra _dib _mr5 _va4 _fs18">add</i> @lang('Add more')
								</span>
							</span>
							<div class="row">

								<div class="col-sm-7 _mb5">
									<small class="_clear _mb5">@lang('Name')</small>
								</div>

								<div class="col-sm-2 _mb5">
									<small class="_clear _mb5">@lang('Price')</small>
								</div>

								<div class="col-sm-2 _mb5">
									<small class="_clear _mb5">@lang('In stock')</small>
								</div>

								<div class="col-sm-1 _mb5 _oh _tac">
									<small class="_clear _mb5">@lang('Delete')</small>
								</div>

							</div>
							<div class="row" id="variants">
							<div ng-repeat="variant in vm.variants.items">

								<div class="col-sm-7 _mb15">
									<input type="text" ng-required="true" minlength="1" maxlength="40" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" placeholder="@lang('Green - XL')" ng-model="variant.name"> 
								</div>

								<div class="col-sm-2 _mb15">
									<input class="_b1 _bcg _fe _brds3 _fes" type="text" 
									ng-currency min="1" ng-required="true" currency-symbol="{{ $product->currency }} " 
									ng-model="variant.price" placeholder="0.00">
								</div>

								<div class="col-sm-2 _mb15">
									<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="variant.in_stock" ui-numeric-input min="0" max="500" max-length="3" placeholder="0">
								</div>

								<div class="col-sm-1 _mb15 _oh _tac">
									<i class="material-icons _cb _hvra _dib _mt5 _crp" ng-click="vm.variants.remove(variant)">close</i>
								</div>

							</div>
							</div>
						</div>

							<input type="hidden" name="variants" ng-value="vm.variants.items | json">
						</div>

					@can('sellExternally', $product)
					<div class="col-sm-12 _mb15">
						<small class="_clear _pb5">@lang('Link to buy product')</small>

						<input id="buy_link" type="text" name="buy_link" minlength="2" maxlength="90" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" value="{{ old('buy_link', $product->buy_link) }}" placeholder="https://www.example.com/product-x.html"> 

						@if ($errors->has('buy_link'))
						<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('buy_link') }}</span>
						@endif
					</div>
					@endcan

				</div>


				<div class="_tal _scr1 _p0 _pt10">

					<div sv-root sv-part="vm.media">	
						<label for="picker-input">
							<div class="_brick _posr _z013 _bg5 _crp _c4">
								<i class="material-icons _a0 _fs28">add_a_photo</i>
							</div>
						</label>
						<input type="file" id="picker-input" on-file-change="vm.selectMedia" name="files[]" multiple="" style="visibility: hidden;position:absolute;width:0;">

						<div class="_brick _posr _z013 _bg2" ng-repeat="file in vm.media" sv-element="opts" class="well">
							<small class="_ab _posa _bgg _trans5" style="height: 0;" ng-style="{'height': file.uploading + '%'}">
							</small>
							<div loader ng-if="file.uploading" class="_af _posa"></div>
							<i class="material-icons _a2 _cr1 _c2 _tz1 _crp" ng-click="vm.deleteMedia(file)" ng-if="!file.uploading">close</i>
							<img ng-src="@{{ file.thumb }}" style="height: 80px;width: 80px;">
						</div>
					</div>

					<input type="hidden" name="media" ng-value="vm.media | json">

				</div>
			</div>



			<div class="_p15 _bt1 _clear _tar">

				@if(!$product->just_created)
				<a class="_btn _bg5 _cg _hvra _left" ng-click="vm.delete()" confirm-click="@lang('Do you really want to delete product?')">
					@lang('Delete')
				</a>
				@endif 

				@if($product->is_active)
				<!--a class="_btn _bg5 _cg _hvra _ml10 _left" onclick="event.preventDefault();
				document.getElementById('status-change-form').submit();" href="/"> 
				{{ $product->is_active ? __('Hide') : __('Show') }} 
			</a-->
			@endif

		@if($product->just_created || $product->isInactive)
		<!--button class="_btn _bg5 _cb _hvra _ml10" 
			type="submit" name="action" value="schedule"> 
			<i class="material-icons _mr5 _va5 _fs20">schedule</i> @lang('Schedule')
		</button-->
		@endif

		@if($product->is_inactive)
		@can('create', $product)
		<button class="_btn _bga _cb _hvra _ml10" type="submit" name="action" value="publish" id="publish"> 
			<i class="material-icons _mr5 _va5 _fs20">store</i> @lang('Publish')
		</button>
		@endif
		@else
		<button class="_btn _bga _cb _hvra _ml10" type="submit" name="action" value="publish" id="publish"> 
			<i class="material-icons _mr5 _va5 _fs20">update</i> @lang('Update')
		</button>
		@endif
	</div>



</div>


</form>

<form id="delete-form" action="{{ $product->link() }}" method="POST" class="_d0">
	<input type="hidden" name="_method" value="DELETE">
	{{ csrf_field() }}
</form>

<form id="status-change-form" action="{{ $product->link('/status') }}" method="POST" class="_d0">
	{{ csrf_field() }}
</form>

</div>



<div class="col-md-4 col-xs-12">
	<div class="_card _z013 _bgw _oh _p0"> 

		<span class="_fs13 _clear _li _bb1 _cb">
			<i class="material-icons _mr10 _va5 _fs18">trending_up</i> @lang('Product Statistics')
		</span>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">fingerprint</i> 
			@lang('Product ID'): #{{ $product->id }}
		</a>


		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">shopping_basket</i>
			@lang(':amount Sales', ['amount' => $product->getMeta('sales', 0)])
		</a>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">favorite</i> 
			@lang(':amount Likes', ['amount' => $product->likes_count])
		</a>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">comment</i> 
			@lang(':amount Comments', ['amount' => $product->getMeta('comments', 0)])
		</a>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">access_time</i> 
			@lang('Added') <span ng-bind="'{{ $product->created_at }}' | timeAgo"></span>
		</a>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">update</i> 
			@lang('Updated') <span ng-bind="'{{ $product->updated_at }}' | timeAgo"></span>
		</a>

	</div>

	{{-- <div class="_card _z013 _bgw _oh _p0 _mt15"> 

		<span class="_fs13 _clear _li _bb1 _cb">
			@lang('Markdown support')
		</span>
		<div class="_p10">
			@lang('Nixler uses Markdown for formatting. Here are the basics.')
			<hr class="_mt5 _mb5">
			<span class="_cg _clear">@lang('Header')</span>
			<code># Material & Care</code>
			<hr class="_mt5 _mb5">
			<span class="_cg _clear">@lang('Bold')</span>
			<code>*100 day* return policy</code>
			<hr class="_mt5 _mb5">
			<span class="_cg _clear">@lang('Emphasis')</span>
			<code>Whisk the eggs _vigorously_.</code>
			<hr class="_mt5 _mb5">
			<span class="_cg _clear">@lang('Highlight')</span>
			<code>`Carefully` crack the eggs.</code>
		</div>
	</div> --}}


</div>

</div>
</div>

@endsection