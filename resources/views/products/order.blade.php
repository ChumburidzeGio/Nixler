@extends('layouts.modal')

@section('modal')
<div class="col-md-8 col-md-offset-2" ng-controller="OrderCtrl as vm">

    <form role="form" method="POST" 
    class="{{ $errors->count ? 'has-error' : '' }}"
    action="{{ route('order', ['id' => $product->id]) }}" 
    ng-submit="vm.submit($event, orderForm)" ng-form-commit name="orderForm">

    {{ csrf_field() }}
    <input type="hidden" name="variant" value="{{ request()->input('variant') }}">
    <input type="hidden" name="quantity" value="{{ request()->input('quantity') }}">
    <input type="hidden" name="step" value="2">

    <div class="panel panel-default _b0 _crd">

        <div class="_lim _clear _mt5 _mb5 _bb1 _bcg">
            <img src="{{ $product->photo('similar') }}" class="_left _dib _mt5" height="60" width="60">
            <div class="_pl15 _pr15 _pb10 _oh">
                <a class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs18" href="{{ $product->url() }}">
                    {{ $product->title }}
                </a>
                <span class="_cbt8 _clear _telipsis _w100 _oh _pr10 _oh _fs13">
                    By <a href="{{ $product->owner->link() }}" class="_ci">{{ $product->owner->name }}</a>
                </span>
                <span class="_cbt8 _clear _telipsis _w100 _oh _pr10 _oh _fs13">
                    {{ $product->price_formated }}
                </span>
            </div>
        </div>

        <span class="_c2 _lh1 _mb0 _fs18 _p15 _clear _pb0">
            @lang('Add shipping details')
        </span>

        <div class="panel-body _pb15 _pl5 _pr5">

            <div class="col-sm-4 _mb15">
                <small class="_clear _pb1">@lang('Your city')</small>
                <select selector model="vm.city" class="_b1 _bcg _brds3" options="vm.cities" placeholder="@lang('City')" require>
                </select>

                <input type="hidden" name="city_id" ng-value="vm.city.id">

                @if ($errors->has('city_id'))
                <span class="_pt1 _pb1 _clear _cr">{{ $errors->first('city_id') }}</span>
                @endif
            </div>

            <div class="col-sm-5 _mb15">
                <small class="_clear _pb1">@lang('Your address')</small>
                <input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="@lang('Street address, block, flat')" name="address" required 
                value="{{ old('address', auth()->user()->getMeta('address')) }}">

                @if ($errors->has('address'))
                <span class="_pt1 _pb1 _clear _cr">{{ $errors->first('address') }}</span>
                @endif
            </div>

            <div class="col-sm-3 _mb15">
                <small class="_clear _pb1">@lang('Your phone')</small>
                <input class="_b1 _bcg _fe _brds3 _fes" 
                type="text" 
                placeholder="" 
                name="phone" 
                required
                value="{{ old('phone', auth()->user()->phone) }}"
                onkeyup="this.value=this.value.replace(/[^\d+]/,'')">

                @if ($errors->has('phone'))
                <span class="help-block _mb _mt0">
                    <span class="_fs13 _lh1">{{ $errors->first('phone') }}</span>
                </span>
                @endif
            </div>

        </div>

        <span class="_c2 _lh1 _mb0 _fs18 _p15 _clear _pb0 _pt0 _bt1 _bcg _pt10">
            @lang('Choose payment method')
        </span>

        <div class="_pb15 _pl5 _pr5" id="payment">

            <div class="row _tac">

                <div class="col-md-6 col-xs-6">
                    <div class="_m5 _hvrl _p10 _brds3" ng-class="{'_cbl _bgcrm': vm.pm('crd')}" ng-click="vm.spm('crd')">
                        <i class="material-icons _fs40 _clear _mb5">credit_card</i>
                        @lang('Visa/Mastercard')
                    </div>
                </div>

                <div class="col-md-6 col-xs-6">
                    <div class="_m5 _hvrl _p10 _brds3" ng-class="{'_cbl _bgcrm': vm.pm('cod')}" ng-click="vm.spm('cod')">
                        <i class="material-icons _fs40 _clear _mb5">transfer_within_a_station</i>
                        @lang('Cash on Delivery')
                    </div>
                </div>

            </div>

            <input type="hidden" name="payment_method" ng-value="vm.payment_method">

        </div>

        <div class="_pb10 _pl5 _pr5 _bgcrm _bt1" id="payment-info" ng-if="vm.pm('bnk') || vm.pm('crd') || vm.pm('cod')">

            <div class="row _tac">
                <div class="col-xs-12 _tal _ml15 _mt10 _cb">
                    <p class="_ml5 _mb0">
                        <div ng-if="vm.pm('crd')">
                            @lang('You will be able to pay with Visa/MasterCard using CartuBanks secure payment system.')
                        </div>
                        <div ng-if="vm.pm('cod')">
                            @lang('You will pay with cash on delivery.')
                        </div>
                    </p>
                </div>
            </div>

        </div>

        <div class="_bg0 _tac _bt1 _bb1 _bcg _fs14 _p10 _pl15 _clear" ng-if="vm.city.shipping">

            <div class="col-sm-4">@lang('Total cost') <span class="_fw600 _clear _cb" ng-bind="vm.price() | money"></span></div>
            <div class="col-sm-4">@lang('Shipping cost') <span class="_fw600 _clear _cb" ng-bind="vm.city.shipping_price | money"></span></div>
            <div class="col-sm-4">@lang('Delivery in')<span class="_fw600 _clear _cb" ng-bind="vm.city.shipping">1-3 days</span></div>

        </div>

        <div class="_bg0 _tal _bb1 _bcg _fs14 _p10 _pl15 _clear">

            <span class="_ml5 _cgr _clear">
                <i class="material-icons _mr10 _fs20 _va5">check</i>
                @lang('Product can be returned in 90 days')
            </span>

        </div>

        <div class="panel-body _pb15 _pl5 _pr5 _mb10 _pt0">

            <div class="col-xs-12">
                <div class="_clear _c3 _bg5 _p10 _brds3 ng-cloak" ng-if="vm.city && !vm.city.shipping">
                    @lang('Shipping is not available in this city.') <a class="_cbl" href="{{ route('find-thread', ['id' => $product->owner->id]) }}">@lang('Ask the seller')</a> @lang('about delivery to your city or choose another one.')
                </div>


                <button type="submit" class="_btn _bga _cb _mt15 block _fs15" ng-disabled="!vm.city.shipping">
                    @lang('Buy')
                    <i class="material-icons _fs18 _va4 _ml4">chevron_right</i>
                </button>
            </div>

        </div>
    </div>

</form>

<div class="_tac _anc _fs14 _cbl _mb15">
    @lang('Do you have any questions about your order?')<br>
    @lang('Call us on :number', [
    'number' => config('contact.phone.GE')
    ])
</div>

</div>
@endsection
