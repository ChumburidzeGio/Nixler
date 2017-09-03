@extends('layouts.app')

@section('body_class', '_bgcrm')

@section('nav_class', '_mb0 ')

@section('content')
<div class="container">

    <div id="qa" class="_clear _c2 _tal _p10">
        <div class="_cbl _tac _fs24 _mt15">კითხვა-პასუხი</div>

        @foreach($qa as $section)

        @foreach($section as $q => $a)

        @if($q == '_title')
        <br>
        <div class="_cbl _fs20 _mt15">{{ $a }}</div>

        @else

        <div class="_c2 _fs16 _mt10 _fw600">{{ $q }}</div>
        <div class="_c3 _fs15">{{ $a }}</div>

        @endif

        @endforeach

        @endforeach
    </div>  

</div>
@endsection