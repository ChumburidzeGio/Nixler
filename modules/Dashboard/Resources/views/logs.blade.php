@extends('layouts.app')

@section('content')
<div class="container">
  <div class="col-md-10 col-md-offset-1 col-xs-12 _p0">

    <div class="_bgw _b1 _brds3 _clear _mb15">

      @foreach($logs as $log)
      <div class="_lim _clear">
        <div class="_pr10 _pb5 _oh">
          <span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs14 _fw600" title="{{ $log['text'] }}">
            {{ $log['text'] }}
          </span> 
          <span class="_cbt8 _clear _fs12  _telipsis _w100 _oh _pr10 _oh" title="{{ $log['in_file'] }}">
            {{ $log['ago'] }}{{ $log['in_file'] }}
          </span>
        </div>
      </div>
      @endforeach

    </div>


  </div>

</div>
@endsection