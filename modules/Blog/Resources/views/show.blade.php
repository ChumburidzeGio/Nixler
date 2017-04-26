@extends('layouts.app')

@section('body_class', '_bgw')

@section('content')

<div class="container">

	<div class="col-md-8 col-md-offset-2 _p0">
		<div class=" _brds3 _clear _mb15">

			<article>
				<h1 style="font-size: 40px;line-height: 48px;padding: 0 0 17px;" class="_cb">{{ $post->title }}</h1>

				<section class="content _c2 _fs16 _mt30" style="line-height: 26px;">
					{!! $post->body !!}
				</section>
			</article>

		</div>
	</div>

</div>

@endsection

