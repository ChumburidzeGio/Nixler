@extends('layouts.app')

@section('content')

<div class="container" ng-controller="SellCtrl as vm">
	<div class="row">

		<div class="col-sm-8 col-xs-12">
			@cannot('create', $product)
			<div class="_card _z013 _mb15 _bgw _p15 _fs15 _cb"> 
				Please for first add shipping information before you will able to publish new product
				<div class="_clear _mt10">
					<a class="_btn _bga _cb _pr5 _hvrl" href="{{ route('shipping.settings') }}" id="shipping_settings_route">
						Go to shipping settings <i class="material-icons _ml5 _va7">chevron_right</i>
					</a>
				</div>
			</div>
			@endif

			@if(session('status'))
			<div class="_card _z013 _mb15 _bgw _p15 _fs15 _cb"> 
				{{ session('status') }}
				@if(session('buttons'))
				<div class="_clear _mt15">
					<a class="_btn _bga _cb _mr5" href="{{ url('new-product') }}">Add new</a>
					<a class="_btn _cb _bg0 _pr5 _b1 _bcg _hvrl" href="{{ $product->url() }}">
						Go to product page <i class="material-icons _ml5 _va7">chevron_right</i>
					</a>
				</div>
				@endif
			</div>
			@endif

			<form class="_mb2 _fg" name="product" action="{{ $product->link() }}" method="POST" ng-init="vm.id={{ $product->id }}">
				{{ csrf_field() }}

				<div class="_z013 _bgw _mb10 _brds2">

					<div class="_p15 _bb1 _posr">
						<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
							@if($product->slug)
								Editing product #{{ $product->id }}
							@else 
								Adding new product
							@endif
						</h1>
						@if($product->slug)
						<a href="{{ $product->url() }}" class="_a3 _mr15" target="_blank">
							<i class="material-icons _fs18 _va4">open_in_new</i> View product
						</a>
						@endif
					</div>

					<div class="_p15">


						<div class="row">

							<div class="col-sm-7 _mb15">
								<small class="_clear _pb5">Product name</small>

								<input id="title" type="text" required name="title" minlength="2" maxlength="90" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" value="{{ $product->title }}"> 

								@if ($errors->has('title'))
								<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('title') }}</span>
								@endif
							</div>

							<div class="col-sm-5 _mb15">
								<small class="_clear _pb5">Category</small>

								<div id="category">
								<select selector
									model="vm.category"
									value-attr="value"
									class="_b1 _bcg _brds3"
									group-attr="zone">
									@foreach($categories as $category)
									<optgroup label="{{ $category->name }}">
										@foreach($category->children as $subcat)
										<option value="{{ $subcat->id }}"
											{{ $product->category_id == $subcat->id ? 'selected' : ''}}>
											{{ $subcat->name }}
										</option>
										@endforeach
									</optgroup>
									@endforeach
									
								</select>
								</div>

								<input type="hidden" name="category" ng-value="vm.category">

								@if ($errors->has('category'))
								<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('category') }}</span>
								@endif
							</div>

						</div>




						<small class="_clear _pb5">
							Product description
						</small>

						<textarea type="text" class="_b1 _bcg _fe _brds3 _mih70" msd-elastic ng-model="vm.description" rows="8" ng-init="vm.description='{{ addslashes($product->description) }}'" id="description" name="description"></textarea>

						@if ($errors->has('decription'))
						<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('decription') }}</span>
						@endif

						<div class="row _mt15">
							<div class="col-sm-3 _mb15">
								<small class="_clear _pb5">Price</small>

								<input placeholder="" class="_b1 _bcg _fe _brds3 _fes" id="price" type="text" ng-currency min="1" ng-required="true" currency-symbol="{{ $product->currency }} " ng-model="vm.price" ng-init="vm.price={{ $product->price }}" name="price">

								@if ($errors->has('price'))
								<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('price') }}</span>
								@endif
							</div>
							<div class="col-sm-2 _mb15">
								<small class="_clear _pb5">In stock</small>

								<input class="_b1 _bcg _fe _brds3 _fes" type="text" ng-required="true" ng-model="vm.in_stock" placeholder="" ui-numeric-input min="0" max="500" max-length="3" ng-init="vm.in_stock={{ $product->in_stock }}" id="in_stock" name="in_stock">

								@if ($errors->has('in_stock'))
								<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('in_stock') }}</span>
								@endif
							</div>
							<div class="col-sm-7 _mb15">
								<small class="_clear">Product variants</small>

								<tags-input ng-model="vm.variants" placeholder="Green - S, Red - M ..." replace-spaces-with-dashes="0" key-property=""></tags-input>
								<input type="hidden" name="variants" ng-value="vm.variants | json" ng-init="vm.variants={{ $product->variants }}">

								@if ($errors->has('variants'))
								<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('variants') }}</span>
								@endif
							</div>



							<div class="col-sm-12 _mb15">
								<small class="_clear _mb5">Tags</small>

								<tags-input ng-model="vm.tags" placeholder="Tags for product" replace-spaces-with-dashes="0" key-property=""></tags-input>

								<input type="hidden" name="tags" ng-value="vm.tags | json" 
									ng-init="{{ $product->tags ? 'vm.tags='.$product->tags : '' }}">

								@if ($errors->has('tags'))
								<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('tags') }}</span>
								@endif
							</div>

							@can('sellExternally', $product)
							<div class="col-sm-12 _mb15">
								<small class="_clear _pb5">Link to buy product</small>

								<input id="buy_link" type="text" required name="buy_link" minlength="2" maxlength="90" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" value="{{ $product->buy_link }}" placeholder="https://www.example.com/product-x.html"> 

								@if ($errors->has('buy_link'))
								<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('buy_link') }}</span>
								@endif
							</div>
							@endcan

						</div>
						


						<div class="_tal _scr1 _p0 _pt10" ng-init='vm.media={{ $product->media }}'>

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
						<a class="_btn _bg5 _cg _hvra _left" ng-click="vm.delete()" confirm-click="Do you really want to delete product?">Delete</a>
						@endif 
						
						@if($product->is_active)
						<a class="_btn _bg5 _cg _hvra _ml10 _left" onclick="event.preventDefault();
						document.getElementById('status-change-form').submit();" href="/"> 
						{{ $product->is_active ? 'Hide' : 'Show' }} 
					</a>
					@endif

					@if($product->just_created)
					<button class="_btn _bg5 _cb _hvra _ml10" 
					type="submit" name="action" value="schedule"> 
					<i class="material-icons _mr5 _va5 _fs20">schedule</i> Schedule
				</button>
				@endif

				@if($product->is_inactive)
				@can('create', $product)
				<button class="_btn _bga _cb _hvra _ml10" type="submit" name="action" value="publish" id="publish"> 
					<i class="material-icons _mr5 _va5 _fs20">store</i> Publish
				</button>
				@endif
				@else
				<button class="_btn _bga _cb _hvra _ml10" type="submit" name="action" value="publish" id="publish"> 
					<i class="material-icons _mr5 _va5 _fs20">update</i> Update
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
			<i class="material-icons _mr10 _va5 _fs18">trending_up</i> Product Statistics
		</span>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">fingerprint</i> 
			Product ID: #{{ $product->id }}
		</a>


		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">shopping_basket</i>
			{{ $product->getMeta('sales', 0) }} Sales
		</a>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">favorite</i> 
			{{ $product->likes_count }} Likes
		</a>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">comment</i> 
			{{ $product->getMeta('comments', 0) }} Comments
		</a>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">access_time</i> 
			Added {{ $product->created_at->diffForHumans() }}
		</a>

		<a class="_hvr1 _fs13 _clear _lim">
			<i class="material-icons _mr10 _va5 _fs18">update</i> 
			Updated {{ $product->updated_at->diffForHumans() }}
		</a>

	</div>

	<div class="_card _z013 _bgw _oh _p0 _mt15"> 

		<span class="_fs13 _clear _li _bb1 _cb">
			Markdown support
		</span>
		<div class="_p10">
			Nixler uses Markdown for formatting. Here are the basics.
			<hr class="_mt5 _mb5">
			<span class="_cg _clear">Header</span>
			<code># Material & Care</code>
			<hr class="_mt5 _mb5">
			<span class="_cg _clear">Bold</span>
			<code>*100 day* return policy</code>
			<hr class="_mt5 _mb5">
			<span class="_cg _clear">Emphasis</span>
			<code>Whisk the eggs _vigorously_.</code>
			<hr class="_mt5 _mb5">
			<span class="_cg _clear">Highlight</span>
			<code>`Carefully` crack the eggs.</code>
		</div>
	</div>


</div>

</div>
</div>

@endsection