@extends('layouts.app')

@section('content')

<div class="container">

<div class="col-md-10 col-md-offset-1 _p0">
    <div class="row">

        <div class="col-md-8">
            <div class="_bgw _b1 _bs011 _brds3 _clear _mb15">
                <img src="{{ $product->photo('resize:600x800') }}" class="_clear _w100">
                <div class="_pl15 _pr10 _pt10 _pb10 _bb1">
                    <span class="_cbl _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs16">
                        Braided 8-Pin USB Cable Charger from
                    </span>
                    <span class="_cb _clear _fs13  _telipsis _w100 _oh _pr10">$345</span>
                    <span class="_cg _clear _fs12  _telipsis _w100 _oh _pr10">D&M Store</span>
                </div>

                <div class="row _bb1 _clear _tac">
                    <div class="col-sm-6 col-xs-8">
                        <select class="col-xs-12  _fe _bg0 _b1 _bcg _ml15 _fs13 _brds3 _mr5 _mt10 _mb10">
                          <option>PRE ORDER:Ships April 2014</option>
                        </select>
                    </div>
                    <div class="col-sm-2 col-xs-3">
                        <select class="col-xs-12 _fe _bg0 _b1 _bcg _ml10 _fs13 _brds3 _mt10 _mb10">
                          <option>1</option>
                        </select>
                    </div>
                    <div class="col-sm-4"><div class="_btn _bga _cb _ttu _pt5 _pb3 _mt10 _mb10">Add to cart</div></div>
                </div>

                <ul class="_pl15 _pr15 _pt10">
                    @foreach($photos as $photo)
                    <img src="{{ $photo }}" height="100px" width="100px" class="_mr10 _mb10 _z013 _brds2 _crp">
                    @endforeach
                </ul>

                <p class="_p15 _pt0 _pb10 _fs13 _fw100 _cbt8 _bb1 _mb0">

                The Pacific belt is handcrafted with the finest wool. Double sided and reinforced with leather for added durability. Nicely complemented with 100% full grain leather tabs and a solid brass buckle. Refreshing lightweight comfort, with a traditional buckle closure. A perfect fit with both jeans, chinos and weekday suits.
                </p>

                <div class="_tbs _ov" style="padding:2px 4px 3px 4px;">

                    <span class="_tb _crp _anim1 _fs13 _ls5 _fw600 _cbt6">
                        <i class="material-icons _fs15 _va3 _mr5">favorite</i> 
                        Like
                    </span>

                    <span class="_tb _crp _anim1 _fs13 _ls5 _fw600 _cbt6">
                        <i class="material-icons _fs15 _va3 _mr5">share</i> 
                        Share
                    </span>

                    <small class="_tb _right _crp">
                        22 Comments
                    </small>
                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="_bgw _b1 _brds3 _clear">

                <img src="{{ $user->photo('resize:140x800') }}" class="_clear _w100">
                <div class=" _pb5 _pl15">
                     <img src="{{ $user->photo('resize:80x80') }}" class="_brds50 _dib _ma _mb5 _b1 _bcg _bw2 _clear _mt-50" height="80" width="80">
                     <a href="{{ url('/login') }}" class="_lh1 _et2 _fs18 _clear">{{ $user->name }}</a>
                     <small class="_clear">14k Followers</small>
                     <div class="_clear _mt10">
                        <div class="_btn _bgw _cg _mt5 _b1 _bcg">Follow</div>
                        <div class="_btn _bgw _cg _mt5 _b1 _bcg">Message</div>
                     </div>
                     
                     <div class="_pr15 _pt5 _pb0 _bt1 _tal _mt10 _mb0">
                        <span class="_fs12">
                            More items from this seller
                        </span>

                        @each('product.short-li', $photos, 'product')

                    </div>

                </div>

            </div>

        </div>
    </div>

    <h5 class="_mt15 _clear _pt5">Other interesting products</h5>
    <div class="row _mt15 _pt5">
        @each('product.short-card', $photos, 'product')
    </div>


</div>
</div>




@endsection
