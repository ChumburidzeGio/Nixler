@extends('layouts.app')

@section('body_class', '_bgw')

@section('content')

<script>window.stream = {!! $collection->products->toJson() !!};</script>

<div style="margin-top: -15px;" ng-controller="ProfileCtrl as vm" class="_clear">
  <div class="col-xs-12 _p0">

    <div class="_bgcrm _bb1 _clear">

       <div class="_pb15 container _pt15 _mt15 _mb5">
           <div class="_clear">

            <img src="{{ $collection->owner->avatar('profile') }}" 
                class="_brds50 _dib _left _bs012 _mr15 hidden-xs hidden-sm" 
                height="90px" width="90px" alt="{{ $collection->name }}">

            <div class="_left _pl5 _mt5">
                <h1 class="_et2 _fs24 _c2 _lh1 _fw400 _m0 _mb5">{{ $collection->name }}</h1>
                <p class="_clear _fs12 _cbt8 _cg _mb5"> 
                   {{ $collection->owner->name }} · @lang('Last updated') {{ $collection->updated_at->diffForHumans() }}
               </p>
               <p class="_clear _fs14 _cbt8 _c3"> 
                   {{ $collection->description }}
               </p>
           </div>
       </div>

</div>

</div>

<div class="_mt15 _pt5 container">
    @include('products.index')
</div>

</div>
</div>
@endsection