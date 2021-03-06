@if($product->in_stock && !(auth()->check() && $product->currency !== auth()->user()->currency))
<div class="_db _pt10 _brds2">

    @if($product->buy_link)

    <a class="_btn _bga _c2 _w100 _lh33px" href="{{ $product->buy_link }}" target="_blank">
        @lang('Buy now on :service', ['service' => ucfirst(str_replace('www.', '', parse_url($product->buy_link, PHP_URL_HOST)))]) <i class="material-icons _fs18 _va4 _ml4">open_in_new</i>
    </a>

    @else

    <form method="GET" action="{{ route('order', ['id' => $product->id]) }}" class="row _ov">
        <div id="variant" class="col-xs-8 _mb10" ng-if="vm.variants.length">
            <select selector
            model="vm.variant"
            class="_bg5 _brds3"
            placeholder="Variant"
            label-attr="name"
            options="vm.variants"
            require="true">
        </select>
        <input type="hidden" name="variant" ng-value="vm.variant.id">
    </div>

    <div id="quantity" class="_mb10" ng-class="{'col-xs-4':vm.variants.length, 'col-xs-3':!vm.variants.length}">
        <select selector
        model="vm.quantity"
        class="_bg5 _brds3"
        options="vm.quantities"
        require="true"></select> 
        <input type="hidden" name="quantity" ng-value="vm.quantity">
    </div>

    <div id="submit" class="_mb10" ng-class="{'col-xs-12':vm.variants.length, 'col-xs-9':!vm.variants.length}">
        <div class="_clear">
            <button class="_btn _bga _cb _w100 _ttu _lh33px">
                @lang('Buy now')
                <i class="material-icons _fs18 _va4 _ml4">chevron_right</i>
            </button>
        </div>
    </div>

</form>
@endif

@if($product->owner->id !== auth()->id())
<a class="_btn _bgwd1 _cb _w100 _mt5 _lh33px" href="{{ route('find-thread', ['id' => $product->owner->id]) }}"> 
    <i class="material-icons _fs18 _va4 _mr5">message</i> @lang('Contact seller') </a>
    <span class="_fs11 _clear _tac _cgr">{{ $product->owner->response_time }}</span>
    @endif

</div>
@endif