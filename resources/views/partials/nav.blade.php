<nav class="_clear _b0 _bgw _bb1 _cb _ma _tac _mb15" ng-controller="NavCtrl as vm" id="navbar">

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
            <span class="_logo _left _fs18 _cinherit _ml0 _mr0" id="navbar-logo">
                <i class="material-icons _fs20 _mr15 _crp _va4 _cinherit" ng-click="vm.openAside()" id="menu">menu</i> 
                <a href="{{ url('/') }}" class="_cinherit _thvrb">{{ config('app.name')}}</a>
            </span>

            <div class="_dib _right _tbs">
                @if(auth()->check())

                @if(auth()->user()->products_count)
                <a href="{{ url('/new-product') }}" class="_tb _posr">
                    <i class="material-icons _cinherit _fs20 _mt10">add</i> 
                </a>
                @endif

                <a href="{{ route('threads') }}" class="_tb _posr">
                    <i class="material-icons _cinherit _fs20 _mt10">message</i> 
                    @if(auth()->user()->notifications_count)
                    <span class="_brds50 _a2 _posa _fs11 _cw _bgr" id="msg-badge">
                        {{ auth()->user()->notifications_count }}
                    </span>
                    @endif
                </a>

                <a class="_tb" href="{{ auth()->user()->link() }}" id="navbar-avatar">
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

            <div class="_dib _oh _posr _brds3" id="search-container">
                <form class="_fg _db _w100" action="{{ route('feed') }}">
                    <input class="_fe _b1 _bw2 _bcgt _bgwt9 _fcsbuy _fcsbw" placeholder="@lang('Search for products and accounts')" name="query" minlength="3" required="" value="{{ request()->input('query') }}" id="search">
                    <i class="material-icons _a8 _fs20 _ml15">search</i>
                </form>
            </div>

        </div>
    </nav>