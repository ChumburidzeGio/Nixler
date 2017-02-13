@extends('layouts.general')

@section('styles')
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,600" rel="stylesheet" type="text/css">
<style type="text/css">
    ._c1{color: #E7E247;}._bg1{background: #E7E247;}
    ._c2{color: #3D3B30;}._bg2{background: #3D3B30;}
    ._c3{color: #4D5061;}._bg3{background: #4D5061;}
    ._c4{color: #5C80BC;}._bg4{background: #5C80BC;}
    ._c5{color: #E9EDDE;}._bg5{background: #E9EDDE;}
</style>
@endsection

@section('app')
<body class="_bgw _ffroboto">
    <div id="app" class="_clear _mt0">



        <div id="header" class="_clear _bgw _c2 _tac">

            <div class="container">

                <div class="_tbs _mt15">
                    <a href="{{ url('/') }}#features" class="_tb _fs14">
                        {{ trans('landing.menu.features') }}
                    </a>
                    <a href="{{ url('/') }}#about" class="_tb _fs14">
                        {{ trans('landing.menu.about') }}
                    </a>
                    <a href="{{ url('/') }}#contact" class="_tb _fs14">
                        {{ trans('landing.menu.contact') }}
                    </a>
                    <a href="https://github.com/nixler" target="_blank" class="_tb _fs14 hidden-xs">
                        {{ trans('landing.menu.open_source') }}
                    </a>
                </div>
            </div>



            <div class="container">
                <div class="_tac _clear _mt70">
                    <div class="row">
                        <div class="col-sm-5">
                            <h1 class="_fs70 _fw00 _ttu _mt0 _ls100 _fs50sm _c3">Nixler</h1>
                            <h3 class="_mt0 _fs20 _ls15 _c3 _lh13">
                                {{ trans('landing.slogan') }}
                            </h3>
                            <br>
                            <h3 class="_mt70 _fs16 _ls15 _pl15 _pr15 _c2">
                                {{ trans('landing.subscribe.title') }}
                            </h3>
                            <div class="_p15 _mb15 _pb15">
                                <form class="_fjcc _df" action="/subscribe">
                                    <div class="_left _dib">
                                        <input type="email" required="" name="email" class="_fe _b1 _brds3 _cb _bcg" placeholder="{{ trans('landing.subscribe.placeholder') }}">
                                    </div>
                                    <button class="_btn _bg4 _ml5 _c5 _left _left _pt5 _pb5 _fs15">{{ trans('landing.subscribe.button') }}</button>
                                </form>
                                <a href="{{ url('policy') }}" class="_clear _fs12 _cg _mt15">
                                    {!! trans('landing.subscribe.help') !!}
                                </a>
                                @if (session('subscribe'))
                                    <span class="_c2 _mt10 _clear _p5 _bg5">{{ session('subscribe') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <img src="/img/landing.png" class="_w100">
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
        </div>





        <div id="features" class="_clear _bg5 _c2 _tac _bt1 _bcg">
            <h3 class="_mt70 _fs28 _ls15 _pl15 _pr15 _pb15 _ttu">
                {{ trans('landing.what.title') }}
            </h3>

            <div class="container">
                @foreach($what as $items)
                <div class="row">
                    @each('landing.feature', $items, 'feature')
                </div>
                @endforeach
            </div>

            <h3 class="_mt70 _fs28 _ls15 _pl15 _pr15 _pb15 _ttu">
                {{ trans('landing.why.title') }}
            </h3>

            <div class="container">
                @foreach($why as $items)
                <div class="row">
                    @each('landing.feature', $items, 'feature')
                </div>
                @endforeach
            </div>
            <br>
        </div>



        <div id="about" class="_clear _bgw _c2 _tac _pb15 _bt1 _bcg">
            <h3 class="_mt70 _fs28 _ls15 _pl15 _pr15 _ttu">
                {{ trans('landing.who.title') }}
            </h3>

            <div class="container _pb15 _mb15">
                @foreach($who as $items)
                <div class="row">
                    @each('landing.people', $items, 'person')
                </div>
                @endforeach
            </div>
        </div>



        <div id="contact" class="_clear _bg3 _cwt9 _tac _pb15">

            <div class="container _pt15 _pb15">
                <h3 class="_mt70 _fs20 _ls15 _pl15 _pr15 _pb15">
                    {{ trans('landing.contact.title') }}
                </h3>
                <h4 class="_mt15 _fs16 _ls10 _fw600">{{ trans('landing.contact.phone') }}</h4>
                <h4 class="_mt15 _fs16 _ls10 _fw600 _mb15 _pb15">Email: info@nixler.pl</h4>
            </div>

        </div>



        <div id="footer" class="_clear _bg2 _cwt9 _tal _pb15">

            <div class="container">
                <p class="_p15 _m0 _pb5 _mt5">
                   Nixler © 2016 <a href="{{ url('policy') }}" class="_right">{{ trans('landing.footer.policy') }}</a>
                </p>
            </div>

        </div>


    </div>

</body>
@endsection