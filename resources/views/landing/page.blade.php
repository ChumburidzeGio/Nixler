@extends('layouts.general')

@section('styles')
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,600" rel="stylesheet" type="text/css">
@endsection

@section('app')
<body class="_bgw _ffroboto">
    <div id="app" class="_clear _mt0">



        <div id="header" class="_clear _bgw _c2 _tac">


            <div class="container">
                <div class="_tac _clear _mt70">
                    <div class="row">
                        <div class="col-sm-5">
                            <h1 class="_fs70 _fw00 _ttu _mt0 _ls100 _fs50sm _c3">Nixler</h1>
                            <h3 class="_mt0 _fs20 _ls15 _c3 _lh13">
                                {{ trans('landing.slogan') }}
                            </h3>
                            <br>

                            <div class="_p15 _bb1 _pt0">
                                <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                                    {{ csrf_field() }}

                                    <div class="form-group _m0 _mb10 {{ $errors->has('email') ? ' has-error' : '' }}">

                                        <input id="email" type="email" class="_b1 _bcg _fe _brds3" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">

                                        @if ($errors->has('email'))
                                        <span class="help-block _mb _mt0">
                                            <span class="_fs13 _lh1">{{ $errors->first('email') }}</span>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group _m0 {{ $errors->has('password') ? ' has-error' : '' }}">

                                        <input id="password" type="password" class="_b1 _bcg _fe _brds3" name="password" required placeholder="Password">

                                        @if ($errors->has('password'))
                                        <span class="help-block _mb _mt0">
                                            <span class="_fs13 _lh1">{{ $errors->first('password') }}</span>
                                        </span>
                                        @endif
                                    </div>

                                    <input type="hidden" name="remember" value="1">

                                    <button type="submit" class="_btn _bgi _cw _mt15 block">Sign in</button>
                                    <a class="_ci _tac _clear _crp _mt10 _fs12 _mb5" href="{{ url('/password/reset') }}">
                                     Forgot password ?</a>

                                 </form>
                             </div>
                             <div class="_tac">

                                <a href="{{ url('/auth/facebook') }}" class="_btn _bgi _cw _mt15 _z013 _pt5 _pb5 _thvrw" style="background:#3b5998">
                                    <img class="_mr5 _va3 _brds1" src="/img/facebook-lite.svg" height="15px"> 
                                    Sign in with Facebook
                                </a>

                                <p class="_clear _fw600 _fs14 _p10 _cb _pb15 _mt15">
                                    Don't have account yet?
                                    <a class="_cbl" href="{{ url('register') }}">Join us</a>
                                </p>

                            </div>

                        </div>
                        <div class="col-sm-7">
                            <img src="/img/landing.png" class="_w100 _ml15 _pl15 _mr15">
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



{{--<!--div id="about" class="_clear _bgw _c2 _tac _pb15 _bt1 _bcg">
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
</div-->



<!--div id="newletter" class="_clear _bgw _cwt9 _tac _pb15">
    <h3 class="_mt70 _fs16 _ls15 _pl15 _pr15 _c2">
        {{ trans('landing.subscribe.title') }}
    </h3>
    <div class="_p15 _mb15 _pb15">
        <form class="_fjcc _df" 
        action="{{ url('/marketing/subscribe') }}" method="POST">

        {{ csrf_field() }}

        <div class="_left _dib">
            <input type="email" required="" name="email" class="_fe _b1 _brds3 _cb _bcg" placeholder="{{ trans('landing.subscribe.placeholder') }}">
        </div>
        <button class="_btn _bg4 _ml5 _cw _left _left _pt5 _pb5 _fs15">{{ trans('landing.subscribe.button') }}</button>
    </form>
    <a href="{{ url('policy') }}" class="_clear _fs12 _cg _mt15">
        {!! trans('landing.subscribe.help') !!}
    </a>
    @if (session('subscribe'))
    <span class="_c2 _mt10 _clear _p5 _bg5">{{ session('subscribe') }}</span>
    @endif
</div>
</div-->


<!--div id="contact" class="_clear _bg3 _cwt9 _tac _pb15">

    <div class="container _pt15 _pb15">
        <h3 class="_mt70 _fs20 _ls15 _pl15 _pr15 _pb15">
            {{ trans('landing.contact.title') }}
        </h3>
        <h4 class="_mt15 _fs16 _ls10 _fw600">{{ trans('landing.contact.phone') }}</h4>
        <h4 class="_mt15 _fs16 _ls10 _fw600 _mb15 _pb15">Email: info@nixler.pl</h4>
    </div>

</div-->--}}

<div id="footer" class="_clear _bg5 _cg _tal _pb15 _pt15">
<br>
<br>
    <div class="container">
        <div class="_p15 _pt5 _mb15">
        <span class="col-xs-4"><a href="/" class="_ci">Nixler</a> © 2017</span>
            <span class="col-xs-4">
                <a href="/" class="_mr10 _ci">About</a>
                <a href="/" class="_mr10 _ci">Terms</a>
                <a href="/" class="_mr10 _ci">Jobs</a>
            </span>
            <div class=" col-xs-4">
                <div class="_tbs _right _dib">
                    <a onclick="event.preventDefault();
                    document.getElementById('setlcl813').submit();" href="/" class="_tb _fs14 _pl0 _pt0 _ci">
                    English
                    <form id="setlcl813" action="{{ url('/settings/locale') }}" method="POST" class="_d0">
                        {{ csrf_field() }}
                        <input type="hidden" name="locale" value="en">
                    </form>
                </a>

                <a onclick="event.preventDefault();
                document.getElementById('setlcl814').submit();" href="/" class="_tb _fs14 _cp _pt0 _ci">
                polski
                <form id="setlcl814" action="{{ url('/settings/locale') }}" method="POST" class="_d0">
                    {{ csrf_field() }}
                    <input type="hidden" name="locale" value="pl">
                </form>
            </a>
        </div>
    </div>
</div>

</div>

</div>


</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-72740986-2', 'auto');ga('send', 'pageview');
</script>

</body>
@endsection