<span class="_c2 _lh1 _mb0 _fs18 _mt5 _clear">
    {{ $product->title }}
</span>

<span class="_cgr _clear _fs17 _pr10">
   {{ $product->price_formated }}

   <div class="_tbs _ov _right _cg _mt3" ng-init="vm.liked={{ $product->isLiked() ? 1 : 0 }}">


        <a class="_tb _crp _anim1 _fs13 _ls5 _fw600 _cbt6 _left _p0 _pr15" 
           ng-class="{'_c4':vm.liked}"
           {{ auth()->guest() ? 'href='.route('login') : 'ng-click=vm.like()' }}>
           <i class="material-icons _fs18 _va5 _mr5">favorite</i> 
           <span ng-show="vm.likes_count" class="ng-cloak">
            (<span ng-bind="vm.likes_count" ng-init="vm.likes_count={{ $product->likes_count }}">
            {{ $product->likes_count }}
        </span>)
        </span>
        </a>

        <span class="_tb _crp _anim1 _fs13 _ls5 _fw600 _cbt6 _left _p0" ng-click="vm.share()">
            <i class="material-icons _fs18 _va5 _mr5">share</i> 
        </span>
</div>

</span>


<span class="visible-sm visible-xs _mt10"> 
    @include('products.show-buy')
</span>

@can('update', $product)
<div class="_tbs _ov _tar _bg5 _brds3 _pl5 _mt15">

    <small class="_tb _crp _left">
        @lang('You can manage with this product')
    </small>

    <a class="_tb _crp _anim1 _fs13 _ls5 _c4" href="{{ route('product.edit', ['id' => $product->id]) }}">
        @lang('Edit or Delete')
    </a>
</div>
@endcan


{{-- @if(auth()->check() && $product->addresses->count())
<div class="panel-body _pb15 _pl15 _pr5 _mb10 _bb1">
    @foreach($product->addresses as $address)
    You can get this product on {{ $address['label'] }} for 
    @if(array_get($address, 'shipping.price') == '0.00')
    <span class="_cgr _ttu">free</span>
    @else
    {{ array_get($address, 'shipping.currency') }} {{ array_get($address, 'shipping.price') }}
    @endif
    in {{ array_get($address, 'shipping.window_from') }}-{{ array_get($address, 'shipping.window_to') }} days

    <i class="_clear">*only you can see it</i>
    @endforeach
</div>
@endif --}}

@if(!$product->in_stock)
<div class="_cw _p5 _pl15 _mt15 _brds3 _fs13 _bgbl">
    @lang('Product is out of stock, please contact owner or check comments for more information.')
</div>
@endif


@if(auth()->check() && $product->currency !== auth()->user()->currency)
<div class="_cw _p5 _pl15 _mt15 _brds3 _fs13 _bgbl">
    @lang('You can\'t buy product from this market because of difference in currency.')
</div>
@endif



<div class="_pt10 _pb5 _fs14 _cbt8 _mb0 _clear product-description _posr">

    <div class="_bt1 _pt15 _bb1">

        <div class="_media _clear _p3">
            <a href="{{ $product->owner->link() }}">
                <img class="_mr15 _left _brds50" src="{{ $product->owner->avatar('product') }}" height="45px" width="45px">
            </a>
            <div class="_clear">
                <a class="_c3 _fs18" href="{{ $product->owner->link() }}">
                    {{ $product->owner->name }}
                </a>
                <p class="_c4 _fs13 _pt0 _mt0">
                    <div class="_clear" show-more more="@lang('Read more')" less="@lang('Show less')" height="180">{!! $product->description_parsed !!}</div>
                </p>
            </div>
        </div>


        <div id="tags" class="_mt10 _mb15 _clear">
            @foreach($product->tags as $tag)
            <span class="_bgbt05 _brds3 _fs13 _c3 _mr10 _dib _p5 _pl10 _pr10 _mb10"> {{ $tag->name }} </span>
            @endforeach
        </div>

    </div>

</div>