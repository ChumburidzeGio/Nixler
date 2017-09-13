@extends('layouts.dashboard')

@section('layout')

<div class="_crd _mb15">

    <h2 class="_crd-header">
        @lang('Information about order') #{{ $order->id }}
        <span class="_cb _bga _fs13 _brds3 _p5 _ml5">@lang($order->status)</span>
    </h2>

    <div class="_crd-content">

        @if(session('flash') == 'thanks')
        <div class="_bb1 _bcg">
         <div class="_posr _clear _mih250 _tac _bt1 _bcg _bgwt8 _cb _b3 _bw2 _bcbl">
            <div class="_a0 _posa">
                <i class="material-icons _fs40 _clear _mb10">check</i>
                <span class="_fs16">@lang('Thank you for purchasing the product!')</span><br>
            </div>
            <img src="/img/bln.png" class="_a2 _m10">
        </div>
        </div>
        @endif

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

        <div class="_p15 _pt2 _cb _bb1 _bt1 row">

            <div class="col-md-6 col-xs-12">

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

            </div>

            <div class="col-md-6 col-xs-12">

            <li class="_clear _telipsis">
                <span class="_cg">@lang('Name')</span>:  
                <span>
                    {{ $order->user->name }}
                </span>
            </li>

            <li class="_clear _telipsis">
                <span class="_cg">@lang('Payment Status')</span>:  
                <span>
                    {{ $order->payment_status }}
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

        </div>

        <div class="_tbs _db _ov _ml15 _pl5">

            <span class="_tb _pb5 _crp _pl0">
                <i class="material-icons _c4" tooltips tooltip-template="@lang('Product ordered')">
                    shopping_basket
                </i>
            </span>

            <span class="_tb _pb5 _pl0"><i class="material-icons _c3">chevron_right</i></span>

            <span class="_tb _pb5 _crp _pl0{{ $order->stagePassed('confirmation') ? ' _c4' : ' _c2' }}">
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
            <span class="_tb _pb5 _pl0"><i class="material-icons _c3">chevron_right</i></span>

            <span class="_tb _pb5 _crp _pl0{{ $order->stagePassed('shipping') ? ' _c4' : ' _c2' }}">
                <i class="material-icons" tooltips tooltip-template="@lang('Product sent')">
                    local_shipping
                </i>
            </span>

            <span class="_tb _pb5 _pl0"><i class="material-icons _c3">chevron_right</i></span>

            <span class="_tb _pb5 _crp _pl0{{ $order->isStatus('closed') ? ' _c4' : ' _c2' }}">
                <i class="material-icons" tooltips tooltip-template="@lang('Product delievered & order closed')">
                    check
                </i>
            </span>
            @endif

        </div>

        @if($order->canUpdate())
        <div class="_mb5 _p15 _ta0 _oh _pl15 _bt1">
            <form id="logout-form" action="{{ route('orders.commit', ['id' => $order->id]) }}" method="POST">
               {{ csrf_field() }}

               @can('update-status', [$order, 'confirmed'])
               <button type="submit" class="_cw _bgi _brds3 _btn _mr10" name="status" value="confirmed">@lang('Confirm')</button>
               @endcan

               @can('update-status', [$order, 'rejected'])
               <button type="submit" class="_cb _bgcrm _b1 _brds3 _btn" name="status" value="rejected">@lang('Reject')</button>
               @endcan

               @can('update-status', [$order, 'sent'])
               <button type="submit" class="_cw _bgi _brds3 _btn" name="status" value="sent">@lang('Set as Sent')</button>
               @endcan

               @can('update-status', [$order, 'closed'])
               <button type="submit" class="_cw _bgi _brds3 _btn" name="status" value="closed">@lang('Set as Delivered')</button>
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
        @endif

    </div>

</div>

@endsection