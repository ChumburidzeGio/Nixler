@extends('layouts.dashboard')

@section('layout')
<div class="_crd _mb15">
    <h2 class="_crd-header">@lang('Recent users')</h2>
    <div class="_crd-content">

        <span class="_clear _pl15 _pr15 _mt5 _mb5 _c2">

            <div class="row">
                <div class="col-xs-4">@lang('User')</div>
                <div class="col-xs-3">@lang('Email')</div>
                <div class="col-xs-3">@lang('Registered')</div>
            </div>

        </span>

        <div id="orders">
            @forelse($users as $user)
            <a class="_lim _clear _pl15 _pr15 _hvrl _bt1 _bcg{{ $user->id == auth()->id() ? ' _bgwt8' : '' }}" href="{{ $user->link() }}">

                <div class="row">
                    <span class="_oh col-xs-4 _oh">
                        <img src="{{ $user->avatar('nav') }}" class="_z013 _brds2 _dib _left" height="30" width="30">

                        <div class="_pl15 _pr15 _oh _pt5">
                            <span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs15">
                                {{ $user->name }}
                            </span>
                        </div>
                    </span>
                    <span class="_oh col-xs-3 _oh _pt5">{{ $user->email }}</span>
                    <span class="_oh col-xs-3 _oh _pt5">{{ $user->created_at->diffForHumans() }}</span>
                </div>

            </a>

            @empty
            <div class="_posr _clear _mih250 _tac">
                <div class="_a0 _posa">
                    <span class="_fs16 _c2">@lang('A list of users is empty.')</span><br>
                </div>
            </div>

            @endforelse
        </div>

    </div>
</div>

{{ $users->links() }}
@endsection