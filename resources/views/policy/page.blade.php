@extends('layouts.general')

@section('app')
<body class="_bgw _ffroboto">

    <div id="app" class="_clear">
    	<div class="container"> 
    		<h1>Nixler Privacy Policy</h1>
	    	@foreach(trans('policy.text') as $title => $text)
	    		@if(!is_integer($title))
	    		<h3 id="{{ str_slug($title) }}">{{ $title }}</h3>
	    		@endif
	    		<p>{!! nl2br($text) !!}</p>
	    	@endforeach
    	</div>
    </div>	

</body>
@endsection