@extends('users.profile.layout')

@section('user_content')
<div class="row">

	@forelse($data as $fl)
	
	<div class="col-sm-6">
		<a class="_lim _clear _pl0 _bgw _pl10 _hvrl" href="{{ $fl->link() }}">
			<img src="{{ $fl->avatar('aside') }}" class="_z013 _brds2 _dib _left" height="60" width="60">
			<div class="_pl15 _pr15 _pb10 _oh">
				<span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs17">
					{{ $fl->name }}
				</span>
				<span class="_cbt8 _clear _fs12  _telipsis _w100 _oh _pr10 _oh">{{ '@'.$fl->username }}</span>
			</div>

			@if(auth()->id() != $fl->id)
		<button class="_btn _bg5 _c2 _ttu _a3 _posa _mr15" style="line-height: 29px;" onclick="event.preventDefault();
    document.getElementById('shfol14{{ $fl->id }}').submit();" tabindex="0">
			<i class="material-icons _fs20 _va6">{{ $fl->following ? 'check' : 'person_add' }}</i>
		      {{ $fl->following ? __('Following') : __('Follow') }}
		      <form id="shfol14{{ $fl->id }}" action="{{ route('user.follow', ['id' => $fl->username]) }}" method="POST" class="_d0">
		        {{ csrf_field() }}
		    </form>
		</button>
		@endif

		</a>
	</div>

	@empty

	<div class="_tac _pt15 _mt70 _c3">
		<h5 class="_fw400">@lang('There is no accounts to show')</h5>
	</div>

	@endforelse

</div>
@endsection
