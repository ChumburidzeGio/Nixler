@extends('layouts.general')

@section('app')
    
    <div class="_pb15 _mih100vh">
        <nav class="_clear _b0 _bgw _bb1 _cb _ma _tac _mb15" ng-controller="NavCtrl as vm">

        @if(session('message'))
        <span class="_clear _tac _bgbl _p5 _cw _thvrw _crp _fs13">
            {{ session('message') }}
        </span>
        @endif

        @impersonating
        <a href="{{ route('impersonate.leave') }}" class="_clear _tac _bg3 _p5 _cw _thvrw _crp _fs13">
            @lang('Leave impersonation')
        </a>
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
                    @if(auth()->user()->notifications_count)
                    <span class="_brds50 _a2 _posa _fs11 _cw _bgr" id="msg-badge">
                        {{ auth()->user()->notifications_count }}
                    </span>
                    @endif
                </a>

                <a class="_tb" href="{{ auth()->user()->link() }}">
                    <img src="{{ auth()->user()->avatar('nav') }}" height="25px" width="25px" class="_va2 _brds3">
                </a>
                @else 

                <a href="{{ route('login') }}" class="_tb _posr _pt15 _thvri _cbl" id="login">
                    @lang('Log in')
                </a>
                
                <a href="{{ route('register') }}" class="_tb _posr _pt15 _thvri" id="register">
                    @lang('Sign up')
                </a>

                @endif
            </div>

            <div class="_dib _oh _posr _brds3" style="width: 100%; max-width: 500px;margin-top: 7px;" id="search-container">
                <form class="_fg _db _w100" action="{{ route('feed') }}">
                    <input class="_fe _b1 _bw2 _bcgt _bgwt9 _fcsbuy _fcsbw" placeholder="@lang('Search for products and accounts')" style="padding-left: 45px; height: 34px;" name="query" minlength="3" required="" value="{{ request()->input('query') }}" id="search">
                        <i class="material-icons _a8 _fs20 _ml15" style="margin-top: 8px;">search</i>
                </form>
            </div>

            </div>
        </nav>


        @yield('content')


        <div id="aside" ng-controller="AsideCtrl as vm">
            <div class="_af aside_overlay" ng-click="vm.close()"  ng-class="{'active':vm.aside_opened}"></div>
            <div class="_al _mhwmax _zi9999 _bgw _aside left" ng-class="{'active':vm.aside_opened}">

                <ul class="_p0">

                    <span class="_clear _posr _db _p15 _posr">
                        <div class="_mt30">
                            <a href="{{ Auth::check() ? auth()->user()->link() : url('/login') }}" 
                                class="_lh1 _et2 _cw _thvrw _fw600">
                                <img ng-src="{{  Auth::check() ? auth()->user()->avatar('aside') : url('/avatars/1/aside') }}" 
                                class="_mb10 _clear _brds3 _z013" height="80" width="80">
                                {{ Auth::check() ? auth()->user()->name : __('Sign in to access your account') }}
                            </a>
                            <a href="{{ Auth::check() ? auth()->user()->url : url('/register') }}">
                                <small class="_clear _cw">
                                    {{ Auth::check() ? ('@'.auth()->user()->username) : __('or sign up now') }}
                                </small>
                            </a>
                        </div>
                        <img ng-src="{{  Auth::check() ? auth()->user()->avatar('aside') : url('/avatars/1/aside') }}" class="_af _posa _zi-1 _w100 _blr30">
                        <div class="_bgbt2 _af _posa _zi-1"></div>
                    </span>

                    @if(auth()->check())

                    <a href="{{ auth()->user()->link() }}" class="_li _fs13 _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">person</i> @lang('Profile')
                    </a>
                    <a href="{{ route('threads') }}" class="_li _fs13 _hvrd _cg">
                        <span class="_posr _dib">
                            <i class="material-icons _fs20 _mr15">message</i>
                            @if(auth()->user()->getMeta('has_messages'))
                            <span class="_p5 _brds50 _bgr _a2 _posa _mr5"></span>
                            @endif
                        </span> @lang('Messages')
                    </a>
                    <a href="{{ route('settings.orders') }}" class="_li _fs13 _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">shopping_basket</i> @lang('Orders')
                    </a>
                    <a href="{{ url('/new-product') }}" class="_li _fs13 _hvrd _cg" id="new-product">
                        <i class="material-icons _fs20 _mr15">add</i> @lang('Add Product')
                    </a>
                    <a href="{{ route('stock') }}" class="_li _fs13 _hvrd _cg" id="new-product">
                        <i class="material-icons _fs20 _mr15">store_mall_directory</i> @lang('My Products')
                    </a>
                    <a href="{{ url('/settings') }}" class="_li _fs13 _hvrd _cg">
                        <i class="material-icons _fs20 _mr15">settings</i> @lang('Settings')
                    </a>
                    
                    <a href="{{ url('/logout') }}" class="_li _fs13 _hvrd _cg _bb1"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();" id="logout">
                        <i class="material-icons _fs20 _mr15">exit_to_app</i> @lang('Logout')
                    </a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="_d0">
                        {{ csrf_field() }}
                    </form>

                @endif

                <span class="_li _fs13 _cg _mt10 _pt15"> @lang('Language')
                    <i class="material-icons _fs16 _va4">translate</i> 

                    <span class="_right">

                        @foreach(config('app.locales_in_country.'.config('app.country')) as $locale => $locale_name)
                            <a onclick="event.preventDefault();
                            document.getElementById('setlcl8{{ $locale }}').submit();" href="/" class="_tb _fs13 _pl0 _pt0 _ci">
                            {{ $locale_name }}
                            <form id="setlcl8{{ $locale }}" action="{{ url('/settings/locale') }}" method="POST" class="_d0">
                                {{ csrf_field() }}
                                <input type="hidden" name="locale" value="{{ $locale }}">
                            </form>
                        </a>

                        @if (!$loop->last) <span class="_ml5 _mr5"> · </span> @endif
                        @endforeach
                    </span>

                </span>
                <!--a href="{{ url('/articles/help') }}" class="_li _fs13 _hvrd _cg"> @lang('Get help')</a-->
                <a href="{{ url('/articles/privacy') }}" class="_li _fs13 _hvrd _cg"> @lang('Privacy Policy')</a>
                <a href="{{ url('/articles/terms') }}" class="_li _fs13 _hvrd _cg"> @lang('Terms of Service')</a>
                <a href="{{ url('/about') }}" class="_li _fs13 _hvrd _cg"> @lang('About Nixler') </a>


            </ul>
        </div>
    </div>

</div>


<div id="footer" class="_clear _ci _tal _pb10 _pt10  _mt15 _fs13">
    <div class="container">
        <div class="_p15 _pt5 _mb15">
        <span class="col-xs-4"><a href="/">Nixler</a> © 2017</span>
            <span class="col-sm-4 col-xs-12 _mb5">
                <a href="{{ url('about') }}" class="_mr10 _crp">@lang('about')</a>
                <a href="{{ url('articles/terms') }}" class="_mr10 _crp">@lang('terms')</a>
            </span>
            <div class="col-sm-4">
                <div class="_tbs _right _dib _pl0 _left-sm">
                    <a onclick="event.preventDefault();
                    document.getElementById('setlcl813').submit();" href="/" class="_tb _pl0 _pt0">
                    English
                    <form id="setlcl813" action="{{ url('/settings/locale') }}" method="POST" class="_d0">
                        {{ csrf_field() }}
                        <input type="hidden" name="locale" value="en">
                    </form>
                </a>

                <a onclick="event.preventDefault();
                document.getElementById('setlcl814').submit();" href="/" class="_tb _pt0 _crp">
                polski
                <form id="setlcl814" action="{{ url('/settings/locale') }}" method="POST" class="_d0">
                    {{ csrf_field() }}
                    <input type="hidden" name="locale" value="pl">
                </form>
            </a>

                <a onclick="event.preventDefault();
                document.getElementById('setlcl815').submit();" href="/" class="_tb _pt0 _crp">
                ქართული
                <form id="setlcl815" action="{{ url('/settings/locale') }}" method="POST" class="_d0">
                    {{ csrf_field() }}
                    <input type="hidden" name="locale" value="ka">
                </form>
            </a>
        </div>
    </div>
</div>

</div>

</div>
@endsection