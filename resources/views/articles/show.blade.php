@extends('layouts.app')

@section('body_class', '_bgw')

@section('content')

<div class="container">

	<div class="col-md-10 col-md-offset-1 _p0">
		<div class=" _brds3 _clear _mb15">

			<article class="article">
				<h1 class="_cbt9 article-title">{{ $article->title }}</h1>

				<section class="content _c2 _fs16 _mt15 _anc article-body">
					{!! $article->body_parsed !!}
				</section>
			</article>

		</div>
	</div>

</div>

@endsection

