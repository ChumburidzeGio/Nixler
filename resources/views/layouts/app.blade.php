@extends('layouts.general')

@section('app')
<body class="@yield('body_class')">

    
    <div id="app" class="_pb15 _db" ng-app="nx">
        <nav class="_clear _b0 _bgw _bb1 _cb _ma _tac _mb15" ng-controller="NavCtrl as vm">

        @impersonating
        <a href="{{ route('impersonate.leave') }}" class="_clear _tac _bg3 _p5 _cw _thvrw _crp _fs13">Leave impersonation</a>
        @endImpersonating
        
        <div class=" _pl15 _pr15">
            <span class="_logo _left _fs18 _cinherit _ml0 _mr0">
                <i class="material-icons _fs20 _mr15 _crp _va4 _cinherit" ng-click="vm.openAside()" id="menu">menu</i> 
                <a href="{{ url('/') }}" class="_cinherit _thvrb">{{ config('app.name')}}</a>
            </span>

            <div class="_dib _right _tbs">
                @if(auth()->check())

                <a href="{{ route('threads') }}" class="_tb _posr">
                    <i class="material-icons _cinherit _fs20 _mt10">message</i> 
                    @if(auth()->user()->getMeta('has_messages'))
                    <span class="_p5 _brds50 _bgr _a2 _posa _mt15 _mr5"></span>
                    @endif
                </a>

                <a class="_tb" href="{{ auth()->user()->link() }}">
                    <img src="{{ auth()->user()->avatar('nav') }}" height="25px" width="25px" class="_va2 _brds3">
                </a>
                @else 
                
                <a href="{{ route('register') }}" class="_tb _posr _pt15 _thvri" id="register">
                    Sign up
                </a>
                <a href="{{ route('login') }}" class="_tb _posr _pt15 _thvri" id="login">
                    Log in
                </a>

                @endif
            </div>

            <div class="_dib _oh _posr _brds3" style="width: 100%; max-width: 500px;margin-top: 7px;">
                <form class="_fg _db _w100" action="{{ route('feed') }}">
                    <input class="_fe _b1 _bw2 _bcgt _bgwt9 _fcsbuy _fcsbw" placeholder="Search for products and accounts" style="padding-left: 45px; height: 34px;" name="query" minlength="3" required="" value="{{ request()->input('query') }}" id="search">
                    @if(request()->has('cat'))<input name="cat" type="hidden" value="{{ request()->input('cat') }}">@endif
                    <i class="material-icons _a8 _fs20 _ml15" style="margin-top: 8px;">search</i>
                </form>
            </div>

            </div>
        </nav>


        @yield('content')


        <div id="aside" ng-controller="AsideCtrl as vm">
            <div class="_af _bgb aside_overlay" ng-click="vm.close()"  ng-class="{'active':vm.aside_opened}"></div>
            <div class="_al _mhwmax _zi9999 _bgw _aside left" ng-class="{'active':vm.aside_opened}">

                <ul class="_p0">

                    <span class="_clear _posr _db _p15 _posr">
                        <div class="_mt30">
                            <a href="{{ Auth::check() ? auth()->user()->link() : url('/login') }}" 
                                class="_lh1 _et2 _cw _thvrw _fw600">
                                <img src="{{  Auth::check() ? auth()->user()->avatar('aside') : url('media/-/avatar/aside.jpg') }}" 
                                class="_mb10 _clear _brds3" height="60" width="60">
                                {{ Auth::check() ? auth()->user()->name : 'Sign in to access your account' }}
                            </a>
                            <a href="{{ Auth::check() ? auth()->user()->url : url('/register') }}">
                                <small class="_clear _cw">
                                    {{ Auth::check() ? ('@'.auth()->user()->username) : 'or sign up now' }}
                                </small>
                            </a>
                        </div>
                        <img src="{{ url('img/aside.jpg') }}" class="_af _posa _zi-1 _w100">
                        <div class="_bgbt2 _af _posa _zi-1"></div>
                    </span>

                    @if(auth()->check())

                    <a href="{{ auth()->user()->link() }}" class="_li _fs13 _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">person</i> Profile
                    </a>
                    <a href="{{ route('threads') }}" class="_li _fs13 _hvrd _cg">
                        <span class="_posr _dib">
                            <i class="material-icons _fs20 _mr15">message</i>
                            @if(auth()->user()->getMeta('has_messages'))
                            <span class="_p5 _brds50 _bgr _a2 _posa _mr5"></span>
                            @endif
                        </span> Messages
                    </a>
                    <a href="{{ route('settings.orders') }}" class="_li _fs13 _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">shopping_basket</i> Orders
                    </a>
                    <a href="{{ url('/new-product') }}" class="_li _fs13 _hvrd _cg" id="new-product">
                        <i class="material-icons _fs20 _mr15">add</i> Add Product
                    </a>
                    <a href="{{ url('/settings') }}" class="_li _fs13 _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">settings</i> Settings
                    </a>
                    
                    <a href="{{ url('/logout') }}" class="_li _fs13 _hvrd _cg _bb1"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();" id="logout">
                        <i class="material-icons _fs20 _mr15">exit_to_app</i> Logout
                    </a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="_d0">
                        {{ csrf_field() }}
                    </form>

                @endif

                <span class="_li _fs13 _cg _mt10 _pt15"> Language 
                    <i class="material-icons _fs16 _va4">translate</i> 

                    <span class="_right">
                            <a onclick="event.preventDefault();
                            document.getElementById('setlcl813').submit();" href="/" class="_tb _fs13 _pl0 _pt0 _ci">
                            English
                            <form id="setlcl813" action="{{ url('/settings/locale') }}" method="POST" class="_d0">
                                {{ csrf_field() }}
                                <input type="hidden" name="locale" value="en">
                            </form>
                        </a>

                        <span class="_ml5 _mr5"> · </span>

                        <a onclick="event.preventDefault();
                            document.getElementById('setlcl814').submit();" href="/" class="_tb _fs13 _cp _pt0 _ci">
                            polski
                            <form id="setlcl814" action="{{ url('/settings/locale') }}" method="POST" class="_d0">
                                {{ csrf_field() }}
                                <input type="hidden" name="locale" value="pl">
                            </form>
                        </a>
                    </span>

                </span>
                <a href="{{ url('/articles/help') }}" class="_li _fs13 _hvrd _cg"> Get help</a>
                <a href="{{ url('/articles/policy') }}" class="_li _fs13 _hvrd _cg"> Privacy Policy</a>
                <a href="{{ url('/articles/terms') }}" class="_li _fs13 _hvrd _cg"> Terms of Service</a>
                <a href="{{ url('/articles/about') }}" class="_li _fs13 _hvrd _cg"> About Nixler </a>


            </ul>
        </div>
    </div>

</div>


<!-- Scripts -->
<script src="{{ url('js/app.js') }}"></script>
@include('cookieConsent::index')

</body>

@endsection