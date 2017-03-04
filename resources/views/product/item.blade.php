@extends('layouts.app')

@section('content')

<div class="container" ng-controller="ShowCtrl as vm" ng-init="vm.id='{{$product->id}}'">

<div class="col-md-10 col-md-offset-1 _p0">
    <div class="row">

        <div class="col-md-8">
            <div class="_bgw _b1 _bs011 _brds3 _clear _mb15">
                <div class="_clear _posr _bb1 _bcg" style="height: 500px">
                    <img src="{{ $product->photo('full') }}" class="_clear _hf _ma" 
                    ng-init="vm.mediaBase='{{ url('media')}}/'" 
                    ng-src="@{{vm.media.mainPath()}}"
                    ng-click="vm.media.next()">

                    <ul class="_pl15 _pr15 _pt10 _a8 _posa">
                        @foreach($media as $key => $photo)
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

                <div class="_pl15 _pr10 _pt10 _pb10 _bb1 _posr">
                    <span class="_c4 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs16">
                        {{ $product->title }}
                    </span>
                    <span class="_cb _clear _fs13  _telipsis _w100 _oh _pr10">
                       {{ $product->currency }} {{ $product->price }}
                    </span>
                    <div class="_a3 _posa _mr15">
                        <div class="_btn _bga _cb">
                            BUY NOW
                        </div>
                    </div>
                </div>

                <p class="_p15 _pt10 _pb10 _fs13 _fw100 _cbt8 _bb1 _mb0">
                <span class="_p3 _clear ">{{ $product->description }}</span>
                </p>

                <div class="_tbs _ov _tar" style="padding:2px 4px 3px 4px;" 
                    ng-init="vm.liked={{ $product->isLiked() ? 1 : 0 }}">

                    <span class="_tb _crp _anim1 _fs13 _ls5 _fw600 _cbt6 _left" 
                        ng-class="{'_c4':vm.liked}"
                        ng-click="vm.like()">
                        <i class="material-icons _fs15 _va3 _mr5">favorite</i> 
                        Like
                    </span>

                    <span class="_tb _crp _anim1 _fs13 _ls5 _fw600 _cbt6 _left">
                        <i class="material-icons _fs15 _va3 _mr5">share</i> 
                        Share
                    </span>

                    <small class="_tb _crp">
                    0 Comments
                    </small>
                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="_bgw _b1 _brds3 _clear">

                <img src="{{ $merchant->cover('product') }}" class="_clear _w100">
                <div class=" _pb5 _pl15">
                     <img src="{{ $merchant->avatar('product') }}" class="_brds50 _dib _ma _mb5 _b1 _bcg _bw2 _clear _mt-50" height="80" width="80">
                     <a href="{{ url('/login') }}" class="_lh1 _et2 _fs18 _clear">{{ $merchant->name }}</a>
                     <small class="_clear">{{ $merchant->followers()->count() }} Followers</small>
                     <!--div class="_clear _mt10">
                        <div class="_btn _bgw _cg _mt5 _b1 _bcg">Follow</div>
                        <div class="_btn _bgw _cg _mt5 _b1 _bcg">Message</div>
                     </div-->
                     
                     <div class="_pr15 _pt5 _pb0 _bt1 _tal _mt10 _mb0">
                        <span class="_fs12">
                            More items from this seller
                        </span>

                        {{--@each('product.short-li', $photos, 'product')--}}

                    </div>

                </div>

            </div>

        </div>
    </div>

    <h5 class="_mt15 _clear _pt5">Other interesting products</h5>
    <div class="row _mt15 _pt5">
       {{-- @each('product.short-card', $photos, 'product')--}}
    </div>


</div>
</div>




@endsection
