@extends('layouts.general')

@section('app')
<body>

<div id="app" class="_mt70 _pb15 _clear">

<nav class="_at _b0 _zi999 _bgw _z013 _cb _pl15 _pr15 _ma">

    <span class="_logo _left _fs18 _cinherit _ml0 _mr0">
        <i class="material-icons _fs20 _mr15 _crp _va4 _cinherit" @click="asideToggle">menu</i> 
        <a href="{{ url('/') }}" class="_cinherit _thvrb">{{ config('app.name')}}</a>
    </span>

    <div class="_dib _right _tbs">

        <a href="{{ url('search') }}" class="_tb"><i class="material-icons _cinherit _fs20 _mt10">search</i> </a>

        <a href="{{ url('nt/latest') }}" class="_tb"><i class="material-icons _cinherit _fs20 _mt10">notifications</i> </a>

           @if(auth()->check())
            <a class="_tb" href="{{ auth()->check() ? url('/users/'.auth()->id()) : url('/login') }}">
                <img src="{{ auth()->user()->avatar('nav') }}" height="25px" class="_va2 _brds50">
            </a>
        @endif
    </div>

</nav>

@yield('content')


<div id="aside">
    <div class="_af _bgb aside_overlay" :class="{active: aside.isOpen}" @click="asideToggle"></div>
    <div class="_al _mhwmax _zi9999 _bgw _aside left" :class="{active: aside.isOpen}">

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
                            {{ Auth::check() ? (auth()->user()->email) : 'Or Sign up now' }}
                        </small>
                    </a>
                </div>
                <img src="{{ url('img/aside.jpg') }}" class="_af _posa _zi-1 _w100">
                <div class="_bgbt2 _af _posa _zi-1"></div>
            </span>

            @if(auth()->check())

            <a href="/" class="_li _hvrd _cg">
                <xi class="_mr15">person</xi> Profile
            </a>
            <a href="{{ url('/settings') }}" class="_li _hvrd _cg _bb1">
                <xi class="_mr15">settings</xi> Settings
            </a>
            <a href="{{ url('/pages/help') }}" class="_li _hvrd _cg">
                <xi class="_mr15">help</xi> Help
            </a>
            <a href="{{ url('/policy') }}" class="_li _hvrd _cg _bb1">
                <xi class="_mr15">accessibility</xi> Privacy
            </a>
            <li class="_li">
                <a href="{{ url('/logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <xi class="_mr15">exit_to_app</xi> Logout
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
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<script src="https://unpkg.com/vue-select@latest"></script>
<script type="text/javascript">

    Vue.component('xi', {
        template: '<i class="material-icons _fs20"><slot></slot></i>'
    });

    Vue.component('v-select', VueSelect.VueSelect);
    
    var app = new Vue({
      el: '#app',
      data: {
        aside: {
            isOpen: false
        }
      },
      methods: {
        asideToggle: function () {
            this.aside.isOpen = !this.aside.isOpen;
        }
      }
    });
</script>

</body>

@endsection