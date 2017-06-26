@extends('layouts.app')

@section('body_class', '_bgw')

@section('content')

<script>window.stream = {!! $collection->products->toJson() !!};</script>

<div style="margin-top: -15px;" ng-controller="ProfileCtrl as vm" class="_clear">
  <div class="col-xs-12 _p0">

    <div class="_bgcrm _bb1 _clear">

       <div class="_pb15 container _pt15 _mt15 _mb5">
           <div class="_clear">

            <img src="{{ $collection->user->avatar('profile') }}" 
                class="_brds50 _dib _left _bs012 _mr15 hidden-xs hidden-sm" 
                height="90px" width="90px" alt="{{ $collection->name }}">

            <div class="_left _pl5 _mt5">
                <h1 class="_et2 _fs24 _c2 _lh1 _fw400 _m0 _mb5">{{ $collection->name }}</h1>
                <p class="_clear _fs12 _cbt8 _cg _mb5"> 
                   {{ $collection->user->name }} · Last updated 5 days ago
               </p>
               <p class="_clear _fs14 _cbt8 _c3"> 
                   {{ $collection->headline }}
               </p>
           </div>
       </div>

       <!--div class="_clear _mt15 _tbs _ml10">

       <a class="_tb _crp _anim1 _fs14 _ls5 _fw600 _cbt6 _left _p0 _pr15" 
        ng-class="{'_c4':vm.liked}"
        id="like">
        <i class="material-icons _fs20 _va5 _mr5">favorite</i> 
</a>

<span class="_tb _crp _anim1 _fs13 _ls5 _fw600 _cbt6 _left _p0" ng-click="vm.share()" id="share">
    <i class="material-icons _fs20 _va5 _mr5">share</i> 
</span>
</div-->


</div>

</div>

<div class="_mt15 _pt5 container">
    @include('products.index')
</div>

</div>
</div>
@endsection