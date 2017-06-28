@extends('users.settings.layout')

@section('layout')
<div class="_crd _mb15">
    <h2 class="_crd-header">@lang('My sessions')</h2>
    <div class="_crd-content">

        <span class="_clear _pl15 _pr15 _mt5 _mb5 _c2">

            <div class="row">
                <div class="col-xs-2">@lang('IP Address')</div>
                <div class="col-xs-3">@lang('Location')</div>
                <div class="col-xs-4">@lang('User Agent')</div>
                <div class="col-xs-2">@lang('Time')</div>
            </div>

        </span>

        <div id="orders">
            @forelse($sessions as $session)
            <span class="_lim _clear _pl15 _pr15 _hvrl _bt1 _bcg{{ $session['is_current'] ? ' _bgwt8' : '' }}">

                <div class="row">
                    <span class="_oh col-xs-2 _telipsis">
                        @if($session['is_current'])
                        <span class="_p5 _bggr _brds50 _dib"></span> 
                        @endif
                        {{ $session['ip_address'] }}
                    </span>
                    <span class="_oh col-xs-3 _oh">{{ $session['location'] }}</span>
                    <span class="_oh col-xs-4 _oh">{{ $session['user_agent'] }}</span>
                    <span class="_oh col-xs-2">{{ $session['time'] }}</span>
                </div>

            </span>

            @empty
            <div class="_posr _clear _mih250 _tac">
                <div class="_a0 _posa">
                    <span class="_fs16 _c2">@lang('A list of sessons is empty.')</span><br>
                </div>
            </div>

            @endforelse
        </div>

    </div>
</div>
@endsection