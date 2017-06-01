@extends('layouts.app')

@section('body_class', '_bgw')

@section('content')

<div class="container" ng-controller="ShowCtrl as vm" ng-init="vm.id='{{ $product->id }}'">

    <div class="col-md-12 _p0">
        <div class="row">

            <div class="col-md-12">
                <div class="row _brds3 _clear _mb15">

                    <div class="col-md-11">
                        @if($product->photo('full', 1))
                        <div class="_clear _posr _bcg _tac" id="product-gallery">
                                <img src="{{ $product->photo('full') }}" class="_clear _ma" 
                                ng-init="vm.mediaBase='{{ url('media') }}/'" 
                                ng-src="@{{ vm.media.mainPath() }}"
                                ng-click="vm.media.next()">

                            <ul class="_pl5 _pr15 _pt10 _a8 _posa">
                                @foreach($product->media as $key => $photo)
                                <img src="{{ $photo->photo('thumb_s') }}" 
                                height="50px" 
                                width="50px" 
                                class="_mr10 _mb10 _z013 _brds2 _crp _clear" 
                                ng-class="{'_b1 _bca _bw2':(vm.mainPhoto == {{ $key }})}"
                                ng-init="vm.media.add({{ $key }},{{ $photo->id }})"
                                ng-click="vm.mainPhoto={{ $key }}">    
                                @endforeach
                            </ul>

                        </div>
                        @endif


        <div class="_pl15 _pr10 _pb10 _posr _pt10 row">
            <div class="col-md-8">
            
            @include('products.show-description')

<div class="_clear _mb10 _pt10">


    <span class="_fs12 _ttu _mb10 _clear">
       <span ng-init="vm.comments_count={{ $product->comments->total() }}" ng-bind="vm.comments_count">{{ $product->comments->total() }}</span> @lang('Comments')
   </span>
   @include('comments.index', ['comments' => $product->comments, 'id' => $product->id])
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
<div class="_bb1 _p15 _fs18 _pb10 _cb">__('Share')</div>

        <a class="_crp _cb _pl15 _pr15 _li _hvrd _fs15" 
            href="http://www.facebook.com/sharer/sharer.php?u=@{{ vm.url }}" target="_blank">
            <img class="_mr15 _va3 _brds1 _ml5" src="/img/facebook.svg" height="18px">
            __('Share on :network', ['network' => 'Facebook'])
        </a>

        <a class="_crp _cb _pl15 _pr15 _li _hvrd _fs15" 
            href="https://twitter.com/intent/tweet?url=@{{ vm.url }}&text=@{{ text }}" 
            target="_blank">

            <img class="_mr15 _va3 _brds1 _ml5" src="/img/twitter.svg" height="18px">
            __('Share on :network', ['network' => 'Twitter'])

        </a>

        <a class="_crp _cb _pl15 _pr15 _li _hvrd _fs15" 
            href="http://vk.com/share.php?url=@{{ vm.url }}" 
            target="_blank">

            <img class="_mr15 _va3 _brds1 _ml5" src="/img/vk.svg" height="18px">
            __('Share on :network', ['network' => 'VK'])
        </a>

        <a class="_crp _cb _pl15 _pr15 _li _hvrd _fs15" 
            href="https://plus.google.com/share?url=@{{ vm.url }}" 
            target="_blank">

            <img class="_mr15 _va3 _brds1 _ml5" src="/img/gplus.svg" height="18px">
            __('Share on :network', ['network' => 'Google+'])
        </a>
</script>
@endsection
