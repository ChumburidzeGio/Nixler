@extends('users.settings.layout')

@section('layout')

@if(isset($order))
<div class="_bgw _z013 _brds3 _mb15">

    <div class="_lh1 _p10 _pl15 _pr15 _cb _fs14 _bb1">@lang('Information about order') #{{ $order->id }}
        <span class="_pl5 _pr5 _c2 _bg5 _fs13 _brds3 _mr5 _dib _m5">@lang($order->status)</span>
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
                {{ $order->price_formated }}
            </span>
        </div>
        @endif
    </div>

    <div class="_p15 _pt2 _cb _bb1 _bt1">

        <li class="_clear">
            <span class="_cg">@lang('Type')</span>: @lang('COD (Cash on Delivery)')
        </li>

        <li class="_clear">
            <span class="_cg">@lang('Delivery')</span>: {{ $order->shipping_window_from->format('F jS') }} - {{ $order->shipping_window_to->formatLocalized('%d %B %Y') }} ({{ money($order->currency, $order->shipping_cost) }})
        </li>

        <li class="_clear">
            <span class="_cg">@lang('Total')</span>:  
            <span>
                {{ money($order->currency, $order->amount) }}
            </span>
        </li>

        <li class="_clear _mb15">
            <span class="_cg">@lang('Quantity')</span>:  
            <span>
                {{ $order->quantity }}
            </span>
        </li>

        <li class="_clear _telipsis">
            <span class="_cg">@lang('Name')</span>:  
            <span>
                {{ $order->user->name }}
            </span>
        </li>

        <li class="_clear _telipsis">
            <span class="_cg">@lang('Phone')</span>:  
            <span>
                +{{ $order->user->phone }}
            </span>
        </li>

        <li class="_clear _telipsis">
            <span class="_cg">@lang('Address')</span>:  
            <span>
                {{ $order->user->city->name }}, {{ $order->address }}
            </span>
        </li>

    </div>

    <div class="_p10 _cb _pb5">

        <div class="_tbs _db _ov _tac">

            <span class="_tb _crp _pl0">
                <i class="material-icons _c4" tooltips tooltip-template="@lang('Product ordered')">
                    shopping_basket
                </i>
            </span>

            <span class="_tb _pl0"><i class="material-icons _c3">chevron_right</i></span>

            <span class="_tb _crp _pl0{{ $order->stagePassed('confirmation') ? ' _c4' : ' _c2' }}">
                @if($order->isStatus('confirmed') || !$order->isStatus('rejected'))
                <i class="material-icons" tooltips tooltip-template="@lang('Confirmed by merchant')">
                    accessibility
                </i>
                @elseif($order->isStatus('rejected'))
                <i class="material-icons" tooltips tooltip-template="@lang('Rejected')">
                    remove_shopping_cart
                </i>
                @endif
            </span>

            @if(!$order->isStatus('rejected'))
            <span class="_tb _pl0"><i class="material-icons _c3">chevron_right</i></span>

            <span class="_tb _crp _pl0{{ $order->stagePassed('shipping') ? ' _c4' : ' _c2' }}">
                <i class="material-icons" tooltips tooltip-template="@lang('Product sent')">
                    local_shipping
                </i>
            </span>

            <span class="_tb _pl0"><i class="material-icons _c3">chevron_right</i></span>

            <span class="_tb _crp _pl0{{ $order->isStatus('closed') ? ' _c4' : ' _c2' }}">
                <i class="material-icons" tooltips tooltip-template="@lang('Product delievered & order closed')">
                    check
                </i>
            </span>
            @endif

        </div>
    </div>


    <div class="_mb5 _p10 _ta0 _oh _pl15">
        <form id="logout-form" action="{{ route('order.commit', ['id' => $order->id]) }}" method="POST">
           {{ csrf_field() }}

           @can('update-status', [$order, 'confirmed'])
           <button type="submit" class="_c2 _bg5 _brds3 _btn _mr10" name="status" value="confirmed">@lang('Confirm')</button>
           @endcan

           @can('update-status', [$order, 'rejected'])
           <button type="submit" class="_c2 _bg5 _brds3 _btn" name="status" value="rejected">@lang('Reject')</button>
           @endcan

           @can('update-status', [$order, 'sent'])
           <button type="submit" class="_c2 _bg5 _brds3 _btn" name="status" value="sent">@lang('Set as Sent')</button>
           @endcan

           @can('update-status', [$order, 'closed'])
           <button type="submit" class="_c2 _bg5 _brds3 _btn" name="status" value="closed">@lang('Set as Delivered')</button>
           @endcan

       </form>

       <div class="_tac">
        @if($order->status == 'closed' || $order->status == 'rejected')
        
        @if($order->user_id == auth()->id())
        @lang('Order is closed, no opperations available. For return or other issues please') <a class="_cbl" href="{{ route('find-thread', ['id' => $order->merchant_id]) }}">@lang('contact merchant')</a>.
        @endif

        <small class="_mt10 _clear">@lang('In case of problem please') <a class="_cbl" href="{{ route('find-thread', ['id' => 1]) }}">@lang('contact us')</a>.</small>
        @endif
    </div>

    <!--a class="_c2 _bg5 _brds3 _btn _right">Report Order</a-->
</div>


</div>

@else


<div class="_crd _mb15">
    <h2 class="_crd-header">@lang('My orders')</h2>
    <div class="_crd-content">

        @if($orders->count())
        <span class="_clear _pl15 _pr15 _mt5 _mb10 _c2">

            <div class="row">
                <div class="col-xs-1">@lang('ID')</div>
                <div class="col-xs-4 _oh">@lang('Product')</div>
                <div class="col-xs-2">@lang('Amount')</div>
                <div class="col-xs-2">@lang('Status')</div>
                <div class="col-xs-3">@lang('Delivery dates')</div>
            </div>

        </span>
        @endif

        <div id="orders">
            @forelse($orders as $order)
            <a class="_lim _clear _pl15 _pr15 _hvrl _bt1 _bcg{{ $order->user_id == auth()->id() ? ' _bgcrm' : '' }}" 
                href="{{ route('settings.orders', ['id' => $order->id]) }}">

                <div class="row">
                    <span class="_oh col-xs-1 _pt5 _pl5 _pr0">
                        <i class="material-icons _va4 _fs18">
                            {{ $order->user_id == auth()->id() ? 'call_made' : 'call_received'}}
                        </i> 
                        #{{ $order->id }}
                    </span>
                    <div class="col-xs-4">
                        <img src="{{ $order->product->photo('similar') }}" class="_z013 _brds2 _dib _left" height="30" width="30">

                        <div class="_pl15 _pr15 _oh _pt5">
                            <span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs15">
                                {{ $order->product->title }}
                            </span>
                        </div>
                    </div>

                    <span class="_oh col-xs-2 _pt5">{{ money($order->currency, $order->amount) }}</span>
                    <span class="_oh col-xs-2 _pt5">@lang($order->status)</span>
                    <span class="_oh col-xs-3 _pt5">{{ $order->shipping_window_from->formatLocalized('%d %B') }} - {{ $order->shipping_window_to->formatLocalized('%d %B') }}</span>
                </div>

            </a>

            @empty
            <div class="_posr _clear _mih250 _tac">
                <div class="_a0 _posa">
                    <span class="_fs16 _c2">@lang('A list of orders is empty.')</span><br>
                    @lang('Here will appear orders when you will buy something or when someone will buy your products.')<br>

                    <a class="_btn _bga _c2 _mt15" style="line-height: 29px;" href="{{ route('feed') }}">
                        @lang('Start shopping')
                        <i class="material-icons _fs20 _va6">chevron_right</i>
                    </a>

                </div>
            </div>

            @endforelse
        </div>

    </div>
</div>

@endif
@endsection