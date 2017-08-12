@extends('layouts.general')

@section('app')
<div class="container _mt50" ng-controller="OrderCtrl as vm">

    @if(app()->environment('production', 'development'))
    <script>
    fbq('track', 'InitiateCheckout');
    </script>
    @endif
    
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default _b0 _z013">


                <span class="_c2 _lh1 _mb0 _fs18 _p15 _clear _pb0">
                    @lang('Confirm your phone')
                    <span ng-if="vm.city.shipping" ng-bind="vm.city.shipping" class="_right _fs14 _cbl"></span>
                </span>

                <div class="_clear _pb15 _pl5 _pr5">
                    <form novalidate role="form" method="POST" action="{{ route('order', ['id' => $id]) }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="variant" value="{{ request()->input('variant') }}">
                        <input type="hidden" name="quantity" value="{{ request()->input('quantity') }}">
                        <input type="hidden" name="step" value="3">

                        <div class="col-xs-12 _pt10">

                            <p class="_clear _cbl _mt10 _tac">@lang('A 5-digit activation code will be texted to your phone within a few minutes.')</p>

                                <div class="_mb15 col-sm-6 col-sm-offset-3 _mt10 {{ $errors->has('pcode') ? ' has-error' : '' }}">

                                    <div class="row">

                                        <div class="col-xs-6">
                                            <small class="_clear _pb1 _mt5">@lang('Confirmation code')</small>
                                        </div>
                                        <div class="col-xs-6">
                                            <input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="2124431" name="pcode" required="">
                                        </div>
                                    </div>

                                    @if ($errors->has('pcode'))
                                    <span class="help-block _mb _mt0">
                                        <span class="_fs13 _lh1">{{ $errors->first('pcode') }}</span>
                                    </span>
                                    @endif
                                </div>



                            <button type="submit" class="_btn _bga _cb block _fs15 _right _mt15">
                                @lang('Confirm') <i class="material-icons _fs18 _va4 _ml4">chevron_right</i>
                            </button>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection
