@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        <div class="col-sm-7">

            <div class="_bgw _z013 _brds3 _mb15" ng-init="vm.close = closeThisDialog">

                <div class="_lh1 _p10 _pl15 _pr15 _cb _fs14 _bb1">Information about order #{{ $order->id }}
                    <span class="_pl5 _pr5 _c2 _bg5 _fs13 _brds3 _mr5 _dib _m5">{{ $order->status }}</span>
                </div>


                <div class="_lim _clear _mt5 _mb5">
                    @if($order->product)
                    @if($order->product->firstMedia('photo'))
                    <img src="{{ $order->product->firstMedia('photo')->photo('thumb') }}" class="_left _dib" height="100" width="100">
                    @endif
                    <div class="_pl15 _pr15 _pb10 _oh">
                        <a class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs18" href="{{ $order->product->url() }}">
                            {{ $order->product->title }}
                        </a>
                        <span class="_cbt8 _clear _telipsis _w100 _oh _pr10 _oh _fs13">
                            By <a href="{{ $order->product->owner->link() }}" class="_ci">{{ $order->product->owner->name }}</a>
                        </span>
                        <span class="_cbt8 _clear _telipsis _w100 _oh _pr10 _oh _fs13">
                            {{ $order->currency }} {{ $order->product->price }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="_p15 _pt2 _cb _bb1 _bt1">

                    <li class="_clear">
                        <span class="_cg">Type</span>: COD (Cash on Delivery)
                    </li>

                    <li class="_clear">
                        <span class="_cg">Delivery</span>: {{ $order->shipping_window_from->format('F jS') }} - {{ $order->shipping_window_to->format('F jS') }}
                    </li>

                    @if($order->address)
                    <li class="_clear _telipsis">
                        <span class="_cg">Address</span>:  
                        <span>
                            {{ $order->address->name }} ({{ $order->address->street }})
                        </span>
                    </li>
                    @endif

                    <li class="_clear">
                        <span class="_cg">Quantity</span>:  
                        <span>
                            {{ $order->quantity }}
                        </span>
                    </li>

                    <li class="_clear">
                        <span class="_cg">Shipping cost</span>:  
                        <span>
                            {{ $order->currency }} {{ $order->shipping_cost }}
                        </span>
                    </li>

                    <li class="_clear">
                        <span class="_cg">Total</span>:  
                        <span>
                            {{ $order->currency }} {{ $order->amount }}
                        </span>
                    </li>

                    @if($order->note)
                    <li class="_clear">
                        <span class="_cg">Note</span>:  
                        <span>
                            {{ $order->note }}
                        </span>
                    </li>
                    @endif

                </div>

                <div class="_p10 _cb _pb5">

                    <div class="_tbs _db _ov _tac">

                        <span class="_tb _crp _pl0">
                            <i class="material-icons _c4" tooltips tooltip-template="Product ordered">
                                shopping_basket
                            </i>
                        </span>

                        <span class="_tb _pl0"><i class="material-icons _c3">chevron_right</i></span>

                        <span class="_tb _crp _pl0{{ $order->stagePassed('confirmation') ? ' _c4' : ' _c2' }}">
                            @if($order->isStatus('confirmed') || !$order->isStatus('rejected'))
                            <i class="material-icons" tooltips tooltip-template="Confirmed by merchant">
                                accessibility
                            </i>
                            @elseif($order->isStatus('rejected'))
                            <i class="material-icons" tooltips tooltip-template="Rejected">
                                remove_shopping_cart
                            </i>
                            @endif
                        </span>

                        @if(!$order->isStatus('rejected'))
                        <span class="_tb _pl0"><i class="material-icons _c3">chevron_right</i></span>

                        <span class="_tb _crp _pl0{{ $order->stagePassed('shipping') ? ' _c4' : ' _c2' }}">
                            <i class="material-icons" tooltips tooltip-template="Product sent">
                                local_shipping
                            </i>
                        </span>

                        <span class="_tb _pl0"><i class="material-icons _c3">chevron_right</i></span>

                        <span class="_tb _crp _pl0{{ $order->isStatus('closed') ? ' _c4' : ' _c2' }}">
                            <i class="material-icons" tooltips tooltip-template="Product delievered & order closed">
                                check
                            </i>
                        </span>
                        @endif

                    </div>
                </div>


                <div class="_bt1">

                    <div class="_mb5 _p10 _ta0 _oh _pl15">
                        <form id="logout-form" action="{{ route('order.commit', ['id' => $order->id]) }}" method="POST">
                         {{ csrf_field() }}

                        @can('update-status', [$order, 'confirmed'])
                        <button type="submit" class="_c2 _bg5 _brds3 _btn" name="status" value="confirmed">Confirm</button>
                        @endcan

                        @can('update-status', [$order, 'rejected'])
                        <button type="submit" class="_c2 _bg5 _brds3 _btn" name="status" value="rejected">Reject</button>
                        @endcan

                        @can('update-status', [$order, 'sent'])
                        <button type="submit" class="_c2 _bg5 _brds3 _btn" name="status" value="sent">Set as Sent</button>
                        @endcan

                        @can('update-status', [$order, 'closed'])
                        <button type="submit" class="_c2 _bg5 _brds3 _btn" name="status" value="closed">Set as Delivered</button>
                        @endcan
                        
                        </form>

                        <div class="_tac">
                        @if($order->status == 'closed' || $order->status == 'rejected')
                            Order is closed, no opperations available
                        @endif
                        </div>

                        <!--a class="_c2 _bg5 _brds3 _btn _right">Report Order</a-->
                    </div>


                </div>

            </div>
        </div>
        <div class="col-sm-5">

            <div class="_z013 _bgw _mb10 _clear _brds3">
                @foreach($orders as $item)
                <a class="_lim _hvrd _cg {{ $order->id == $item->id ? '_bg5' :'' }}" 
                    href="{{ route('order.show', ['id' => $item->id]) }}">
                    
                    <div class="pt5">
                        <i class="material-icons _mr15 _va7 _fs18 _left _mt5">
                            {{ $item->user_id == auth()->id() ? 'call_made' : 'call_received'}}
                        </i> 
                        Order #{{ $item->id }} ({{ $item->currency }} {{ $item->amount }})
                        <span class="_pl5 _pr5 _c2 _fs13 _brds3 _mr5 _dib _m5 {{ $order->id == $item->id ? '_bgw' :'_bg5' }}">
                            {{ $item->status }}
                        </span>
                        @if($item->product)
                        <div class="_clear _fs13"> {{ $item->product->title }} </div>
                        @endif
                        <div class="_clear _fs13 _c2">
                            Delivery at {{ $item->shipping_window_from->format('F jS') }} - {{ $item->shipping_window_to->format('F jS') }} for {{ $item->currency }} {{ $item->shipping_cost }}
                        </div>
                    </div>

                </a>
                @endforeach

            </div>

        </div>
    </div>
</div>

@endsection
