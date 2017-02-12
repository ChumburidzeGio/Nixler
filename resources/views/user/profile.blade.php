@extends('layouts.app')

@section('content')

<div class="container">
<div class="col-md-10 col-md-offset-1 col-xs-12 _p0">

    <div class="_bgw _b1 _brds3 _clear">

        <img src="{{ $user->photo('resize:150x800') }}" class="_clear _w100">
        <div class="_tac _pb15 _bb1">
             <img src="{{ $user->photo('resize:100x100') }}" class="_brds2 _dib _ma _mb10 _b1 _bcg _bw2 _clear _mt-50" height="100" width="100">
             <a href="{{ url('/login') }}" class="_lh1 _et2 _fs24 _clear">{{ $user->name }}</a>
             <small class="_clear">Member since: {{ $user->created_at->format('F jS, Y') }}</small>
             <div class="_clear">
                <div class="_btn _bgi _cw _mt5">Follow</div>
                <div class="_btn _bgw _cg _mt5 _b1 _bcg">Message</div>
             </div>
             
        </div>

        <div class="_tbs  _tal _pt4 _fw600 _pl5">
            <a class="_fs12 _tb _bb1 _bw2 _ci _bci" href="/">Liked <span class="_fw300 _ml5">32</span></a>
            <a class="_fs12 _tb" href="/">Selling <span class="_fw300 _ml5">0</span></a>
            <a class="_fs12 _tb" href="/">Followers <span class="_fw300 _ml5">43</span></a>
            <a class="_fs12 _tb" href="/">Following <span class="_fw300 _ml5">54</span></a>
            <a class="_fs12 _tb" href="/">Photos <span class="_fw300 _ml5">14</span></a>
            <a class="_tb _right _fw300" href="/">Share</a>
            <a class="_tb _right _fw300" href="/">Report</a>
        </div>

    </div>


    <div class="row _mt15 _pt5">
        @each('product.short-card', $products, 'product')
    </div>



</div>
</div>




@endsection
