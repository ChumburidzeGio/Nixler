<div class="col-md-3 _pb15">
    <a class="_bgw _b1 _brds3 _clear" href="{{ $product->url() }}">

    <img src="{{ $product->photo('short-card') }}" class="_db _w100">

        <div class="_pl15 _pr15 _pt10 _pb10">
            <span class="_cb _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs13">
                {{ $product->title }}
            </span>
            <span class="_cg _clear _fs12  _telipsis _w100 _oh _pr10">
                <b class="_ci">{{ $product->currency }} {{ $product->price }}</b> · {{ '@'.$product->owner_username }} · <i class="material-icons _fs13 _va2 _cg">favorite</i> {{ $product->likes_count }}
            </span>
        </div>

    </a>
</div>