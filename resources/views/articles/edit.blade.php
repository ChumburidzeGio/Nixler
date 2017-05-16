@extends('layouts.app')

@section('content')

<div class="container" ng-controller="SellCtrl as vm">
	<div class="row">

		<div class="col-sm-8 col-xs-12">

			<form class="_mb2 _fg" name="article" action="{{ route('articles.update', ['id' => $article->slug]) }}" method="POST">
				{{ csrf_field() }}

				<div class="_z013 _bgw _mb10 _brds2">

					<div class="_p15 _bb1 _posr">
						<h1 class="_fs18 _ci _lh1 _clear _telipsis _m0">
							@if($article->slug)
							Editing article #{{ $article->id }}
							@else 
							Adding new article
							@endif
						</h1>
						@if($article->slug)
						<a href="{{ route('articles.show', $article->slug) }}" class="_a3 _mr15" target="_blank">
							<i class="material-icons _fs18 _va4">open_in_new</i> View article
						</a>
						@endif
					</div>

					<div class="_p15">


						<div class="_mb15">
							<small class="_pb5">Article name</small>

							<input id="title" type="text" required name="title" minlength="2" maxlength="90" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" value="{{ $article->title }}"> 

							@if ($errors->has('title'))
							<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('title') }}</span>
							@endif
						</div>


						<div class="_mb15">
							<small class="_clear _pb5">Article slug</small>

							<input id="slug" type="text" required name="slug" minlength="2" maxlength="90" class="_b1 _bcg _fe _brds3 _fes" autocomplete="off" value="{{ $article->slug }}"> 

							@if ($errors->has('slug'))
							<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('slug') }}</span>
							@endif
						</div>


						<small class="_clear _pb5">
							Article body
						</small>

						<textarea type="text" class="_b1 _bcg _fe _brds3 _mih70" msd-elastic ng-model="vm.body" rows="8" ng-init="vm.body='{{ addslashes($article->body) }}'" id="body" name="body"></textarea>

						@if ($errors->has('decription'))
						<span class="_pt1 _pb1 _clear _cr">{{ $errors->first('decription') }}</span>
						@endif

					</div>



					<div class="_p15 _pt10 _clear _tar">
						<button class="_btn _bga _cb _hvra _ml10" type="submit" name="action" value="publish" id="publish"> 
							<i class="material-icons _mr5 _va5 _fs20">store</i> Publish
						</button>
					</div>



				</div>

			</form>

			<form id="delete-form" action="{{ route('articles.destroy', $article->id) }}" method="POST" class="_d0">
				<input type="hidden" name="_method" value="DELETE">
				{{ csrf_field() }}
			</form>

		</div>



		<div class="col-md-4 col-xs-12">

			<div class="_card _z013 _bgw _oh _p0"> 

				<span class="_fs13 _clear _li _bb1 _cb">
					Markdown support
				</span>
				<div class="_p10">
					Nixler uses Markdown for formatting. Here are the basics.
					<hr class="_mt5 _mb5">
					<span class="_cg _clear">Header</span>
					<code># Material & Care</code>
					<hr class="_mt5 _mb5">
					<span class="_cg _clear">Bold</span>
					<code>*100 day* return policy</code>
					<hr class="_mt5 _mb5">
					<span class="_cg _clear">Emphasis</span>
					<code>Whisk the eggs _vigorously_.</code>
					<hr class="_mt5 _mb5">
					<span class="_cg _clear">Highlight</span>
					<code>`Carefully` crack the eggs.</code>
				</div>
			</div>

		</div>

	</div>
</div>

@endsection