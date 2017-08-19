@extends('layouts.app')

@section('body_class', '_bgw')

@section('content')

    <div class="container" ng-controller="ShowCtrl as vm" itemscope itemtype="http://schema.org/Product">

        <div class="col-md-12 _p0">
            <div class="row">

                <div class="col-md-12">
                    <div class="row _brds3 _clear _mb15">

                        <div class="col-md-11">

                            @if($product->photo('full', 1))
                            <div class="_clear _posr _bcg _tac" id="product-gallery">

                                    <div class="_a0 _posa _spnr-md ng-cloak" ng-if="vm.media.isLoading()"></div>

                                    <img src="{{ $product->photo('full') }}" itemprop="image" class="_clear _ma" 
                                    preload-image
                                    ng-src="@{{ vm.media.active() }}"
                                    ng-click="vm.media.next()"
                                    alt="{{ $product->title }}"
                                    fallback-src="http://nixler.app/media/2116/media/thumb_s.jpg"
                                    onloading="vm.media.onLoading"
                                    onloaded="vm.media.onLoaded">

                                <ul class="_pl5 _pr15 _pt10 _a8 _posa">

                                    <img ng-repeat="(id, photo) in vm.media.all()" 
                                    ng-src="@{{ photo.thumb }}"
                                    data="@{{ id }}" 
                                    height="50px" 
                                    width="50px" 
                                    class="_mr10 _mb10 _z013 _brds2 _crp _clear" 
                                    ng-class="{'_b1 _bca _bw2':vm.media.isActive(id)}"
                                    ng-click="vm.media.select(id)">    

                                </ul>

                            </div>
                            @endif

                            <div class="_pl5 _pr10 _pb10 _posr _pt10 row">
                                <div class="col-md-8">
                                    @include('products.show-description')
                                    <div class="_clear _mb10 _pt10">
                                        <span class="_fs12 _ttu _mb10 _clear">
                                           <span ng-bind="vm.product.comments_count">{{ $product->comments_count }}</span> @lang('Comments')
                                       </span>
                                       @include('comments.index')
                                   </div>

                               </div>

                               <div class="col-md-4">

                                <div class="visible-md visible-lg"> 
                                    @include('products.show-buy')
                                </div>


                                @if($product->similar->count())

                                <span class="_fs12 _ttu _mb5 _clear _mt15">
                                    @lang('More products')
                                </span>

                                <div class="_clear">
                                    @each('products.similar', $product->similar, 'product')
                                </div>
                                @endif

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/ng-template" id="/tmp/share.html">

    <div class="_bb1 _p15 _fs18 _pb10 _cb">@lang('Share')</div>

    <div class="_clear _pb5">  

        <a class="_crp _cb _pl15 _pr15 _li _hvrd _fs15" href="http://www.facebook.com/sharer/sharer.php?u={{ $product->url() }}" target="_blank">
            <img class="_mr15 _va3 _brds1 _ml5" src="/img/facebook.svg" height="18px">
            @lang('Share on :network', ['network' => 'Facebook'])
        </a>

        <a class="_crp _cb _pl15 _pr15 _li _hvrd _fs15" href="https://twitter.com/intent/tweet?url={{ $product->url() }}" target="_blank">
            <img class="_mr15 _va3 _brds1 _ml5" src="/img/twitter.svg" height="18px">
            @lang('Share on :network', ['network' => 'Twitter'])
        </a>

        <a class="_crp _cb _pl15 _pr15 _li _hvrd _fs15" href="http://vk.com/share.php?url={{ $product->url() }}" target="_blank">
            <img class="_mr15 _va3 _brds1 _ml5" src="/img/vk.svg" height="18px">
            @lang('Share on :network', ['network' => 'VK'])
        </a>

        <a class="_crp _cb _pl15 _pr15 _li _hvrd _fs15" href="https://plus.google.com/share?url={{ $product->url() }}" target="_blank">
            <img class="_mr15 _va3 _brds1 _ml5" src="/img/gplus.svg" height="18px">
            @lang('Share on :network', ['network' => 'Google+'])
        </a>

        <a class="_crp _cb _pl15 _pr15 _li _hvrd _fs15" ng-init="vm.link='{{ $product->url() }}'" ng-click="vm.copy()">
            <i class="_mr15 _va7 _brds1 _ml5 material-icons _fs22">content_copy</i>
            @lang('Copy link')
        </a>

    </div>

</script>
@endsection
