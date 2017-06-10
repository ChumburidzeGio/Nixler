@extends('layouts.app')

@section('body_class', '_bgcrm')

@section('nav_class', '_mb0 ')

@section('content')

<div class="_tac" id="about-header"><img src="/img/43345-O3TNL5.jpg"></div>

<div class="container">

    <div id="features" class="_clear _c2 _tac">

        <h1 class="_mt70 _lh13 _pl15 _pr15 _pb15 _fs24 _fw400 _cbl">@lang('Welcome to Nixler!')</h1>
        <h2 class="_mt15 _lh13 _pl15 _pr15 _pb5 _fs20 _fw400">
            @lang('Nixler is a shopping platform, where you can easily publish your product and sell online for free.')
        </h2>
        <h2 class="_mt0 _lh13 _pl15 _pr15 _pb15 _fs20 _fw400">
            @lang('Nixler aims to create free trading space where anyone will be able to buy and sell online simply and safely.')
        </h2>

    </div>


    <div id="contact" class="_pb15 row _pl10 _pr10 _mt70">

        <div class="col-sm-5 col-xs-12 _mb15 col-sm-offset-1">
            <a class="_lim _clear _pl0 _crp" href="mailto:info@nixler.pl">
                <i class="material-icons _fs40 _left _mt5 _mr10">email</i>
                <div class="_pl15 _pr15 _pb10 _oh">
                    <span class="_c2 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs18">
                        info@nixler.pl
                    </span>
                    <span class="_clear _fs13  _telipsis _w100 _oh _pr10 _oh">@lang('Drop line')</span>
                </div>
            </a>
        </div>

        <div class="col-sm-5 col-xs-12 _mb15 col-sm-offset-1 _mb15">
            <a class="_lim _clear _pl0 _crp" href="tel:+995591815010">
                <i class="material-icons _fs40 _left _mt5 _mr10">phone</i>
                <div class="_pl15 _pr15 _pb10 _oh">
                    <span class="_c2 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs18">
                        +995 591815010
                    </span>
                    <span class="_clear _fs13  _telipsis _w100 _oh _pr10 _oh">@lang('Call us')</span>
                </div>
            </a>
        </div>

        <div class="col-sm-5 col-xs-12 _mb15 col-sm-offset-1 _mb15">
            <a class="_lim _clear _pl0" href="{{ url('@nixler') }}">
                <img class="_va3 _left _mt5 _mr10 _brds3 _z013" src="/avatars/1/aside" height="40px" width="40px">
                <div class="_pl15 _pr15 _pb10 _oh">
                    <span class="_c2 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs18">
                        Nixler
                    </span>
                    <span class="_clear _fs13  _telipsis _w100 _oh _pr10 _oh">@lang('Follow us')</span>
                </div>
            </a>
        </div>

        <div class="col-sm-5 col-xs-12 _mb15 col-sm-offset-1 _mb15">
            <a class="_lim _clear _pl0" href="https://fb.me/nixler.georgia" target="_blank">
                <img class="_va3 _left _mt5 _mr10" src="/img/facebook.svg" height="40px">
                <div class="_pl15 _pr15 _pb10 _oh">
                    <span class="_c2 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs18">
                        fb.me/nixler.georgia
                    </span>
                    <span class="_clear _fs13  _telipsis _w100 _oh _pr10 _oh">@lang('Follow us')</span>
                </div>
            </a>
        </div>

    </div>

</div>

</body>
@endsection