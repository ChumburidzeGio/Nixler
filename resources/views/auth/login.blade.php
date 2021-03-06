@extends('layouts.modal')

@section('modal')
<div class="col-md-6 col-md-offset-3">
    <div class="panel panel-default _b0 _z013">
        <div class="panel-heading _oh _posr _tac">
            <a href="javascript:history.back()" class="_posa _a8 _pt10 _ml15">
                <i class="material-icons _fs18 _mr10 _va4">arrow_back</i>
                <span class="hidden-xs">@lang('Go back')</span>
            </a>
            @lang('Sign in now')
        </div>

        @if($product)
        @include('products.header', compact('product'))
        @endif

        <div class="panel-body _pb5 _bb1">

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                {{ csrf_field() }}

                <div class="form-group _m0 _mb10 {{ $errors->has('email') ? ' has-error' : '' }}">

                    <input id="email" type="text" class="_b1 _bcg _fe _brds3" name="email" value="{{ old('email') }}" required autofocus placeholder="@lang('Email or Username')">

                    @if ($errors->has('email'))
                    <span class="help-block _mb _mt0">
                        <span class="_fs13 _lh1">{{ $errors->first('email') }}</span>
                    </span>
                    @endif
                </div>

                <div class="form-group _m0 {{ $errors->has('password') ? ' has-error' : '' }}">


                    <div class="_posr">
                        <input id="password" type="@{{ password_visible ? 'text' : 'password' }}" 
                        class="_b1 _bcg _fe _brds3" name="password" ng-model="password" 
                        required placeholder="@lang('Password')">
                        <span class="_a3 _mr15 _posa _crp _fs11 _ttu _cbl _fw600" 
                        ng-show="password.length"
                        ng-click="password_visible=!password_visible"
                        ng-bind="password_visible ? '@lang('Hide')' : '@lang('Show')'"></span>
                    </div>
                    

                    @if ($errors->has('password'))
                    <span class="help-block _mb _mt0">
                        <span class="_fs13 _lh1">{{ $errors->first('password') }}</span>
                    </span>
                    @endif
                </div>

                <input type="hidden" name="remember" value="1">

                <button type="submit" class="_btn _bgi _cw _mt15 block">@lang('Sign in')</button>
                <a class="_ci _tac _clear _crp _mt10 _fs12 _mb5" href="{{ url('/password/reset') }}" id="password-reset">
                   @lang('Forgot password?')</a>

               </form>
           </div>
           <div class="_tac">

            <a href="{{ url('/auth/facebook') }}" class="_btn _bgi _cw _mt15 _z013 _pt5 _pb5 _thvrw fb-bg">
                <img class="_mr5 _va3 _brds1" src="/img/facebook-lite.svg" height="15px"> 
                @lang('Sign in with Facebook')
            </a>

            <p class="_clear _fw600 _fs12 _p10 _cb _pb15 _mt15">
                @lang('Don\'t have account yet?')
                <a class="_cbl" href="{{ url('register') }}">@lang('Join us')</a>
            </p>

        </div>

    </div>

    <div class="_tac _anc _fs13">
        @lang('By clicking Sign in with Facebook, I agree to Nixler\'s <a href=":terms">Terms of Service</a> and <a href=":privacy">Privacy Policy</a>.', [
        'terms' => url('articles/terms'),
        'privacy' => url('articles/privacy'),
        ])
    </div>

</div>
@endsection
