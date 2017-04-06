@extends('layouts.general')

@section('app')
<body>

    <div id="app" class="_mt70 _pb15 _db" ng-app="nx">

        <nav class="_at _b0 _zi999 _bgw _z013 _cb _pl15 _pr15 _ma" ng-controller="NavCtrl as vm">

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
                    <img src="{{ auth()->user()->avatar('nav') }}" height="25px" class="_va2 _brds50">
                </a>
                @endif
            </div>

        </nav>

        @yield('content')


        <div id="aside" ng-controller="AsideCtrl as vm">
            <div class="_af _bgb aside_overlay" ng-click="vm.close()"  ng-class="{'active':vm.aside_opened}"></div>
            <div class="_al _mhwmax _zi9999 _bgw _aside left" ng-class="{'active':vm.aside_opened}">

                <ul class="_p0">

                    <span class="_clear _posr _db _p15 _posr">
                        <div class="_mt30">
                            <a href="{{ Auth::check() ? auth()->user()->url : url('/login') }}" 
                                class="_lh1 _et2 _cw _thvrw _fw600">
                                <img src="{{  Auth::check() ? auth()->user()->avatar('aside') : '' }}" 
                                class="_mb10 _clear _brds50" height="60" width="60">
                                {{ Auth::check() ? auth()->user()->name : 'Sign In to access account' }}
                            </a>
                            <a href="{{ Auth::check() ? auth()->user()->url : url('/register') }}">
                                <small class="_clear _cw">
                                    {{ Auth::check() ? ('@'.auth()->user()->username) : 'Or Sign up now' }}
                                </small>
                            </a>
                        </div>
                        <img src="{{ url('img/aside.jpg') }}" class="_af _posa _zi-1 _w100">
                        <div class="_bgbt2 _af _posa _zi-1"></div>
                    </span>

                    @if(auth()->check())

                    <a href="{{ auth()->user()->link() }}" class="_li _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">person</i> Profile
                    </a>
                    <a href="{{ route('orders') }}" class="_li _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">shopping_basket</i> Orders
                    </a>
                    <a href="{{ url('/new-product') }}" class="_li _hvrd _cg _bb1" id="new-product">
                        <i class="material-icons _fs20 _mr15">add</i> Add Product
                    </a>
                    <a href="{{ url('/settings') }}" class="_li _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">settings</i> Settings
                    </a>
                    <a href="{{ url('/pages/help') }}" class="_li _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">help</i> Help
                    </a>
                    <a href="{{ url('/policy') }}" class="_li _hvrd _cg _bb1">
                        <i class="material-icons _fs20 _mr15">accessibility</i> Privacy
                    </a>
                    <li class="_li">
                        <a href="{{ url('/logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();" id="logout">
                        <i class="material-icons _fs20 _mr15">exit_to_app</i> Logout
                    </a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="_d0">
                        {{ csrf_field() }}
                    </form>
                </li>

                @endif

            </ul>
        </div>
    </div>

</div>


<!-- Scripts -->
<script src="{{ url('js/app.js') }}"></script>
@include('cookieConsent::index')

</body>

@endsection