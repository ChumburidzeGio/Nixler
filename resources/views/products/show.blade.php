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
                        <div class="_clear _posr _bcg" style="height: 500px">
                            <img src="{{ $product->photo('full') }}" class="_clear _hf _ma" 
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
       <span ng-init="vm.comments_count={{ $product->comments->total() }}" ng-bind="vm.comments_count">{{ $product->comments->total() }}</span> Comments
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
            More products
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




@endsection
