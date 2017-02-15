@extends('layouts.general')

@section('styles')
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
@endsection

@section('app')
<body>

  <div class="_posf _bggd _cwt9" style="height: 100vh;max-width:300px;min-width: 250px">
    <div class="_pt5 _pb15 _posa _w100 _hf _oa _fw600 _ffroboto">
      <a class="_lim _fs16 _mb15">
        <i class="material-icons _va5 _fs24 _mr10">store</i> Nixler Dashboard
      </a>
      <a class="_lim _fs13 _hvrd">
        <i class="material-icons _va5 _fs18 _mr10">trending_up</i> Dashboard
      </a>
      <a class="_lim _fs13 _hvrd">
        <i class="material-icons _va5 _fs18 _mr10">people</i> People
      </a>
      <a class="_lim _fs13 _hvrd">
        <i class="material-icons _va5 _fs18 _mr10">email</i> Subscribers
      </a>
      <a class="_lim _fs13 _hvrd">
        <i class="material-icons _va5 _fs18 _mr10">settings</i> Settings
      </a>
    </div>

    <div class="_lim _clear _ab _posa _pb0">
      <img src="{{ auth()->user()->photo('resize:40x40')}}" height="40" width="40" class="_z013 _brds3 _dib _left"> 
      <div class="_pl15 _pr15 _pb10 _oh">
        <a class="_lh1 _mb0 _telipsis _w100 _clear _pr10 _fs14 _fw600">
        Giorgi Chumburidze
        </a> 
        <span class="_clear _fs12  _telipsis _w100 _oh _pr10 _oh">
          Logout
        </span>
      </div>
    </div>

  </div>

  <div style="margin-left:250px" class="_clear">
    <div class="_p15 _bb1 _bgw"> @yield('header') </div>
    <div class="container-fluid"> <div class="_pl5 _pr5">@yield('wrapper')</div> </div>
  </div>


</body>
@endsection