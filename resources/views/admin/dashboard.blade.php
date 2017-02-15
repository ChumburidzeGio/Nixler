@extends('admin.layout')

@section('header')
Dashboard
@endsection

@section('wrapper')
<div class="row _pt15">

        <div class="col-xs-6">


          <div class="_bgw _b1 _brds3 _clear">
            <span class="_pl15 _mt10 _mb5 _cg _fs17 _clear">Latest users</span>

            <div class="_lim _clear">
              <img src="{{ auth()->user()->photo('resize:50x50')}}" height="50" width="50" class="_z013 _brds50 _dib _left"> 
              <div class="_pl15 _pr15 _pb10 _oh _pt5">
                <span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs14 _fw600">
                  Giorgi Chumburidze
                </span> 
                <span class="_cbt8 _clear _fs12  _telipsis _w100 _oh _pr10 _oh">
                  chumburidzegio@live.com · registered 2min ago
                </span>
              </div>
            </div>

            <a class="_clear _tac _p5 _hvrl _bt1">View All</a>
          </div>


        </div>

        <div class="col-xs-6">


          <div class="_bgw _b1 _brds3 _clear">
            <span class="_pl15 _mt10 _mb5 _cg _fs17 _clear">Latest products</span>

            <div class="_lim _clear">
              <img src="{{ auth()->user()->photo('resize:50x50')}}" height="50" width="50" class="_z013 _brds3 _dib _left"> 
              <div class="_pl15 _pr15 _pb10 _oh">
                <span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs14 _fw600">
                  Braided 8-Pin USB Cable Cheap
                </span> 
                <span class="_cbt8 _clear _fs12  _telipsis _w100 _oh _pr10 _oh">
                  $75 · D&M Store · 202 likes
                </span>
              </div>
            </div>

            <a class="_clear _tac _p5 _hvrl _bt1">View All</a>
          </div>


        </div>

        </div>
@endsection