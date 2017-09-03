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
                    <i class="material-icons _fs20 _mr15">message</i>
                    @lang('Messages')
                    @if(auth()->user()->notifications_count)
                    <span class="_right _fs11 _cw _bgr _brds3 _p3 _pl5 _pr5">
                        {{ auth()->user()->notifications_count }}
                    </span>
                    @endif
                </a>
                <a href="{{ route('orders.index') }}" class="_li _fs13 _hvrd _cg">
                    <i class="material-icons _fs20 _mr15">shopping_basket</i> @lang('Orders')
                </a>
                <a href="{{ url('/new-product') }}" class="_li _fs13 _hvrd _cg" id="new-product">
                    <i class="material-icons _fs20 _mr15">add</i> @lang('Add Product')
                </a>
                <a href="{{ route('stock') }}" class="_li _fs13 _hvrd _cg" id="new-product">
                    <i class="material-icons _fs20 _mr15">store_mall_directory</i> @lang('My products')
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
        <a href="{{ url('/articles/privacy') }}" class="_li _fs13 _hvrd _cg"> @lang('Privacy Policy')</a>
        <a href="{{ url('/articles/terms') }}" class="_li _fs13 _hvrd _cg"> @lang('Terms of Service')</a>
        <a href="{{ url('/about') }}" class="_li _fs13 _hvrd _cg"> @lang('About Nixler') </a>

    </ul>
</div>
</div>