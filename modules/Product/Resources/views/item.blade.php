@extends('layouts.app')

@section('content')

<div class="container" ng-controller="ShowCtrl as vm" ng-init="vm.id='{{$product->id}}'">

<div class="col-md-12 _p0">
    <div class="row">

        <div class="col-md-8">
            <div class="_bgw _b1 _bs011 _brds3 _clear _mb15">
                @if($product->photo('full', 1))
                <div class="_clear _posr _bb1 _bcg" style="height: 500px">
                    <img src="{{ $product->photo('full') }}" class="_clear _hf _ma" 
                    ng-init="vm.mediaBase='{{ url('media')}}/'" 
                    ng-src="@{{vm.media.mainPath()}}"
                    ng-click="vm.media.next()">

                    <ul class="_pl15 _pr15 _pt10 _a8 _posa">
                        @foreach($product->media as $key => $photo)
                        <img src="{{ $photo->photo('thumb_s') }}" 
                            height="50px" 
                            width="50px" 
                            class="_mr10 _mb10 _z013 _brds2 _crp _clear" 
                            ng-class="{'_b1 _bca _bw2':(vm.mainPhoto == {{$key}})}"
                            ng-init="vm.media.add({{$key}},{{$photo->id}})"
                            ng-click="vm.mainPhoto={{$key}}">    
                        @endforeach
                    </ul>

                </div>
                @endif

                @can('update', $product)
                <div class="_tbs _ov _tar _bg5" style="padding:2px 4px 3px 4px;">

                    <small class="_tb _crp _left">
                    You can manage this product
                    </small>
                    
                    <a class="_tb _crp _anim1 _fs13 _ls5 _c4" href="{{ route('product.edit', ['id' => $product->id]) }}">
                        Edit or Delete
                    </a>
                </div>
                @endcan

                <div class="_pl15 _pr10 _pt10 _pb10 _posr _bb1">
                    <span class="_c2 _lh1 _mb0 _telipsis _w80 _clear _pr10 _fs20 _pr15 _mr15">
                        {{ $product->title }}
                    </span>

                    <span class="_cgr _clear _fs15  _telipsis _w100 _oh _pr10">
                       {{ $product->currency }} {{ $product->price }}
                    </span>

                    <span class="_cg _clear _fs14  _telipsis _w100 _oh _pr10">

                       @if($product->category)
                       Published in <a class="_c4" href="{{ route('feed', ['cat' => $product->category->id]) }}">{{ $product->category->name }}</a>
                       @endif

                       @if($product->tags->count())· @endif 
                       @foreach($product->tags as $tag)
                         <span class="_c3">#{{ $tag->name }}</span>
                       @endforeach

                       @if($product->category || $product->tags->count())<br>@endif
                       @if($product->variants->count())Available in  @endif 
                       @foreach($product->variants as $kk => $tag)
                         <span class="_c3">{{ $tag->name }}</span> 
                         @if(($kk+1) < $product->variants->count()) · @endif
                       @endforeach
                    </span>

                    @if($product->in_stock && !(auth()->check() && $product->currency !== auth()->user()->currency))
                    <div class="_a3 _posa _mr15 _buybtn">
                        <a class="_btn _bga _cb" href="{{ $product->buy_link or route('order', ['product_id' => $product->id]) }}" {{ $product->buy_link ? 'target="_blank"' : '' }}>
                            BUY NOW  {{ $product->buy_link ?  'on'.ucfirst(str_replace('www.', '', parse_url($product->buy_link, PHP_URL_HOST))) : '' }}
                        </a>
                        <span class="_fs11 _clear _tac">{{ $product->in_stock }} in stock</span>
                    </div>
                    @endif
                </div>

                <div class="panel-body _pb15 _pl15 _pr5 _mb10 _bb1">
                @if(auth()->check() && $product->addresses->count())
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
                @else
                    @foreach($product->owner->shippingPrices as $sp)

                        @if($sp->type == 'country')
                            Around whole {{ $sp->location->name }}
                        @else
                            In {{ $sp->location->name }}
                        @endif

                        delivery in {{ $sp->window_from }}-{{ $sp->window_to }} days for 
                        @if($sp->price == '0.00')
                            <span class="_cgr _ttu">free</span>
                        @else
                        {{ $sp->currency }} {{ $sp->price }}
                        @endif

                    @endforeach
                @endif
                </div>

                @if(!$product->in_stock)
                <div class="_c3 _bg5 _p5 _pl15">
                    Product is out of stock, please contact owner or check comments for more information.
                </div>
                @endif


                @if(auth()->check() && $product->currency !== auth()->user()->currency)
                <div class="_c3 _bg5 _p5 _pl15">
                    You can't buy product from this market because of difference in currency.
                </div>
                @endif

                <div class="_p15 _pt10 _pb5 _fs14 _cbt8 _mb0 _clear">
                    <span class="_clear">{!! $product->description_parsed !!}</span>
                </div>

                <div class="_tbs _ov _tar _bb1 _bt1" style="padding:2px 4px 3px 4px;" 
                    ng-init="vm.liked={{ $product->isLiked() ? 1 : 0 }}">

                    <span class="_tb _crp _anim1 _fs13 _ls5 _fw600 _cbt6 _left" 
                        ng-class="{'_c4':vm.liked}"
                        ng-click="vm.like()">
                        <i class="material-icons _fs15 _va3 _mr5">favorite</i> 
                        Like 
                        <span ng-show="vm.likes_count" class="ng-cloak">
                            (<span ng-bind="vm.likes_count" ng-init="vm.likes_count={{ $product->likes_count }}">
                                {{ $product->likes_count }}
                            </span>)
                        </span>
                    </span>

                    <span class="_tb _crp _anim1 _fs13 _ls5 _fw600 _cbt6 _left" ng-click="vm.share()">
                        <i class="material-icons _fs15 _va3 _mr5">share</i> 
                        Share
                    </span>

                    <small class="_tb _crp">
                    <span ng-init="vm.comments_count={{ $product->comments->total() }}" ng-bind="vm.comments_count">{{ $product->comments->total() }}</span> Comments
                    </small>
                </div>


                <div class="_clear _p5">
                @include('comment::index', ['comments' => $product->comments, 'id' => $product->id])
                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="_bgw _b1 _brds3 _clear">

                <a href="{{ $product->owner->link() }}">
                    <img src="{{ $product->owner->cover('product') }}" class="_clear _w100" height="80px">
                </a>
                <div class=" _pb5 _pl15">
                     <a href="{{ $product->owner->link() }}">
                        <img src="{{ $product->owner->avatar('product') }}" class="_brds3 _dib _ma _mb5 _b1 _bcg _bw2 _clear _mt-50" height="80" width="80">
                        <span class="_lh1 _et2 _fs18 _clear _c4">{{ $product->owner->name }}</span>
                     </a>
                     <small class="_clear _mb5">{{ $product->owner->followers()->count() }} Followers</small>
                     @if($product->owner->id !== auth()->id())
                     <div class="_clear _mt10 _pr10 _mb5">
                        <!--div class="_btn _bgw _cg _mt5 _b1 _bcg">Follow</div-->
                        <a class="_btn _bg5 _cb _mt5 _w100" href="{{ route('find-thread', ['id' => $product->owner->id]) }}">
                            <i class="material-icons _mr5 _va5 _fs20">message</i> Message
                        </a>
                        <span class="_fs11 _clear _tac _cgr">{{ $product->owner->response_time }}</span>
                     </div>
                     @endif

                </div>

            </div>

            @if($product->similar->count())
            <div class="_bgw _b1 _brds3 _clear _mt15">

                     <div class="_pr15 _pl15 _tal _mb0 _p10">
                        <span class="_fs12 _ttu _mb5 _clear">
                            More products
                        </span>

                        @each('product::short-li', $product->similar, 'product')

                    </div>

            </div>
            @endif

        </div>
    </div>

</div>
</div>




@endsection
