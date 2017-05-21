@extends('layouts.general')

@section('app')
<div class="container _mt50" ng-controller="OrderCtrl as vm">

<script>
window.cities = <?php echo $cities->toJson(); ?>;
window.price = <?php echo $product->price; ?>;
</script>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default _b0 _z013">
                
                <span class="_c2 _lh1 _mb0 _fs18 _p15 _clear _pb0">
                    @lang('Add shipping details')
                    <span ng-if="vm.city.shipping" ng-bind="vm.city.shipping" class="_right _fs14 _cbl ng-cloak"></span>
                </span>

                <div class="panel-body _pb15 _pl5 _pr5 _mb10">
                    <form role="form" method="POST" action="{{ route('order', ['id' => $product->id]) }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="variant" value="{{ request()->input('variant') }}">
                        <input type="hidden" name="quantity" value="{{ request()->input('quantity') }}">
                        <input type="hidden" name="step" value="2">

                        <div class="col-xs-4 _mb15">
                            <small class="_clear _pb1">@lang('Your city')</small>
                            <select selector model="vm.city" class="_b1 _bcg _brds3" options="vm.cities" placeholder="@lang('City')" require>
                            </select>

                            <input type="hidden" name="city_id" ng-value="vm.city.id">

                            @if ($errors->has('city_id'))
                            <span class="_pt1 _pb1 _clear _cr">{{ $errors->first('city_id') }}</span>
                            @endif
                        </div>
                        
                        <div class="col-xs-5 _mb15">
                            <small class="_clear _pb1">@lang('Your address')</small>
                            <input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="@lang('Street address, block, flat')" name="address" required>

                            @if ($errors->has('address'))
                            <span class="_pt1 _pb1 _clear _cr">{{ $errors->first('address') }}</span>
                            @endif
                        </div>

                        <div class="col-xs-3 _mb15">
                            <small class="_clear _pb1">@lang('Your phone')</small>
                            <input class="_b1 _bcg _fe _brds3 _fes" type="text" placeholder="" name="phone" required>

                            @if ($errors->has('phone'))
                            <span class="help-block _mb _mt0">
                                <span class="_fs13 _lh1">{{ $errors->first('phone') }}</span>
                            </span>
                            @endif
                        </div>

                        <div class="col-xs-12">
                        <div class="_clear _c3 _bg5 _p10 _brds3 ng-cloak" ng-if="vm.city && !vm.city.shipping">
                                @lang('Shipping is not available in this city.')' <a class="_c4" href="{{ route('find-thread', ['id' => $product->owner->id]) }}">@lang('Ask the seller')</a> @lang('about delivery to your city or choose another one.')
                        </div>

                            <p class="_clear _fs13 _c2 _mb0 _tac _mt5 ng-cloak" ng-if="vm.city.shipping_price">@lang('You will pay on delivery') {{ $product->currency }} <span ng-bind="vm.price()"></span>.</p>


                        <button type="submit" class="_btn _bga _cb _mt15 block _fs15" ng-disabled="!vm.city.shipping">
                                @lang('Buy')
                                <i class="material-icons _fs18 _va4 _ml4">chevron_right</i>
                            </button>
                        </div>
                     </form>

                 </div>
            </div>
        </div>
    </div>
</div>

@endsection
