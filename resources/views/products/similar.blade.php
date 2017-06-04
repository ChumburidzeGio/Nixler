<a class="_lim _clear _pl0" href="{{ $product->url() }}">
<img src="{{ route('photo', [
            	'id' => array_get($product->firstPhoto->first(), 'id', '-'),
            	'type' => 'product',
            	'place' => 'similar'
            ]) }}" class="_z013 _brds2 _dib _left" height="60" width="60">
	<div class="_pl15 _pr15 _pb10 _oh">
		<span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs15">
			{{ $product->title }}
		</span>
		<span class="_cgr _clear _fs13  _telipsis _w100 _oh _pr10 _oh">{{ $product->price_formated }}</span>
	</div>
</a>