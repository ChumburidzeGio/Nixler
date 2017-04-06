@extends('layouts.app')

@section('content')
<div class="container" ng-controller="OrderCtrl as vm">
<script>
window.addresses = <?php echo json_encode($addresses); ?>;
window.variants = <?php echo $variants->flatten()->toJson(); ?>;
window.price = <?php echo $product->price; ?>;
window.max_quantity = <?php echo $product->in_stock > 99 ? 99 : $product->in_stock; ?>;
</script>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default _b0 _z013">
                <div class="panel-heading">

                    <div class="_lim _clear _pl0">
                        <img src="{{ $product->firstMedia('photo')->photo('thumb') }}" class="_left _dib" height="100" width="100">
                        <div class="_pl15 _pr15 _pb10 _oh">
                            <a class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs18" href="{{ $product->url() }}">
                                {{ $product->title }}
                            </a>
                            <span class="_cbt8 _clear _telipsis _w100 _oh _pr10 _oh _fs13">
                                By <a href="{{ $merchant->link() }}" class="_ci">{{ $merchant->name }}</a>
                            </span>
                            <span class="_cbt8 _clear _telipsis _w100 _oh _pr10 _oh _fs13">
                                {{ $product->currency }} {{ $product->price }}
                            </span>
                        </div>
                    </div>

                </div>
                <div class="panel-body _pb15 _pl5 _pr5 _mb10">
                    <form class="form-horizontal _row" role="form" method="POST" action="{{ route('order.store') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="_mb15 {{ $variants->count() ? 'col-sm-3' : 'col-sm-5'}} form-group _m0 {{ $errors->has('quantity') ? ' has-error' : '' }}">

                            <small class="_clear _pb1">Quantity</small>
                            <div class="_clear _posr">
                                <i class="material-icons _a7 _posa _brds2 _cg _crp _fs20 _ml10" ng-click="vm.less()">remove</i>
                                <input name="quantity" type="text" required ng-model="vm.quantity" class="_b1 _bcg _fe _brds3 _fes _tac _lh15" autocomplete="off" readonly=""> 
                                <i class="material-icons _a3 _posa _brds2 _cg _crp _fs20 _mr10" ng-click="vm.more()">add</i>
                            </div>

                            @if ($errors->has('quantity'))
                            <span class="help-block _mb _mt0">
                                <span class="_fs13 _lh1">{{ $errors->first('quantity') }}</span>
                            </span>
                            @endif
                        </div>

                        @if($variants->count())
                        <div class="_mb15 col-sm-4 form-group _m0 {{ $errors->has('variant') ? ' has-error' : '' }}">
                           
                            <small class="_clear _pb1">Product variant</small>
                            <select selector model="vm.variant" class="_b1 _bcg _brds3"
                                options="vm.variants" placeholder="Variant"
                                {{ old('variant') ? 'ng-init="vm.variant="'.old('variant').'"' : ''}}>
                            </select>

                            <input type="hidden" name="variant" ng-value="vm.variant">

                            @if ($errors->has('variant'))
                            <span class="help-block _mb _mt0">
                                <span class="_fs13 _lh1">{{ $errors->first('variant') }}</span>
                            </span>
                            @endif
                        </div>
                        @endif

                        <div class="_mb15 {{ $variants->count() ? 'col-sm-5' : 'col-sm-7'}} form-group _m0 {{ $errors->has('address') ? ' has-error' : '' }}">
                            <small class="_clear _pb1">Your address</small>
                            <select selector model="vm.address" label-attr="name" class="_b1 _bcg _brds3"
                                options="vm.addresses" 
                                placeholder="Address" 
                                {{ old('address') ? 'ng-init="vm.address="'.old('address').'"' : ''}}>
                            </select>

                            <input type="hidden" name="address" ng-value="vm.address.id">

                            @if ($errors->has('address'))
                            <span class="help-block _mb _mt0">
                                <span class="_fs13 _lh1">{{ $errors->first('address') }}</span>
                            </span>
                            @endif
                        </div>


                        <div class="_mb15 col-sm-12 form-group _m0 {{ $errors->has('comment') ? ' has-error' : '' }}">
                            <small class="_clear _pb1">Your comment for merchant</small>
                            
                            <textarea name="comment" type="text" ng-model="vm.order.comment" class="_b1 _bcg _fe _brds3" msd-elastic></textarea>

                            @if ($errors->has('comment'))
                            <span class="help-block _mb _mt0">
                                <span class="_fs13 _lh1">{{ $errors->first('comment') }}</span>
                            </span>
                            @endif
                        </div>

                        <div class="col-xs-12 _mt10">

                            <div ng-if="vm.address.shipping" class="_c3">

                                <span class="_c4">
                                    <span ng-bind="vm.price() | currency:'{{ $product->currency }} '"></span> in total
                                </span>

                                <div class="_fs13">
                                    <span ng-bind="vm.address.shipping.price | currency:'{{ $product->currency }} '"></span> for shipping | Delivery in <span ng-bind="vm.address.shipping.window_from"></span>
                                    -  <span ng-bind="vm.address.shipping.window_to"></span> days

                                    <br>

                                    Way of payment cash on delievery

                                </div>

                            </div>

                            <div ng-if="!vm.address.shipping && vm.address" class="_c3 _bg5 _p5">

                                Shipping is not available on this address please choose another or cancel the order

                            </div>

                            <div ng-if="!vm.addresses.length" class="_c3 _bg5 _p5">

                                You don't have any address yet. Please go to settings and 
                                <a href="{{ route('settings.addresses') }}" class="_c4">add new address</a>.

                            </div>

                            <button type="submit" class="_btn _bga _cb _mt15 block _fs15 _right" 
                                ng-disabled="!vm.canSubmit()">Buy</button>

                        </div>

                     </form>
                 </div>

            </div>
        </div>
    </div>
</div>

@endsection
