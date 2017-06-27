@extends('users.settings.layout')

@section('layout')
<div class="_crd _mb15">
    <h2 class="_crd-header">@lang('My sessions')</h2>
    <div class="_crd-content">

        <span class="_clear _pl15 _pr15 _mt5 _mb5 _c2">

            <div class="row">
                <div class="col-xs-3">@lang('Location')</div>
                <div class="col-xs-2 _oh">@lang('User Agent')</div>
                <div class="col-xs-2">@lang('IP Address')</div>
                <div class="col-xs-2">@lang('Time')</div>
                <div class="col-xs-3"></div>
            </div>

        </span>

        <div id="orders">
            @forelse($sessions as $session)
            <span class="_lim _clear _pl15 _pr15 _hvrl _bt1 _bcg">

                <div class="row">
                    <span class="_oh col-xs-3">{{ $session['location'] }}</span>
                    <span class="_oh col-xs-2">{{ $session['user_agent'] }}</span>
                    <span class="_oh col-xs-2">{{ $session['ip_address'] }}</span>
                    <span class="_oh col-xs-2">{{ $session['time'] }}</span>
                    <span class="_oh col-xs-3">{{ $session['is_current'] }}</span>
                </div>

            </span>

            @empty
            <div class="_posr _clear _mih250 _tac">
                <div class="_a0 _posa">
                    <span class="_fs16 _c2">@lang('A list of orders is empty.')</span><br>
                    @lang('Here will appear orders when you will buy something or when someone will buy your products.')<br>

                    <a class="_btn _bga _c2 _mt15" style="line-height: 29px;" href="{{ route('feed') }}">
                        @lang('Start shopping')
                        <i class="material-icons _fs20 _va6">chevron_right</i>
                    </a>

                </div>
            </div>

            @endforelse
        </div>

    </div>
</div>
@endsection