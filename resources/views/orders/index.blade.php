@extends('users.settings.layout')

@section('layout')

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
                href="{{ route('orders.show', ['id' => $order->id]) }}">

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

@endsection