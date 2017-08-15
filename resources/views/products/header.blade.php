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