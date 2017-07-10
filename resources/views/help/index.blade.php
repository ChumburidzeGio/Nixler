@extends('layouts.app')

@section('body_class', '_bgcrm')

@section('nav_class', '_mb0 ')

@section('content')

<div class="container _lst">
	
	<div class="_p15 _pt5"> 
	@foreach($qa as $sections)
			
			<div class="col-md-6">

			@foreach($sections as $q => $a)

				@if($q == '_title')
					<span class="_fs16 _mt10 _mb10 _clear _cbl">
						<i class="material-icons _mr0">chevron_right</i>
						{{ $a }}
					</span>
				@else
					<span class="_fs16 _clear _hvrl _p5 _pl10 _pr10 _brds3">{{ $q }}</span>
				@endif

			@endforeach
			
			</div>

	@endforeach
	</div>

</div>

@endsection