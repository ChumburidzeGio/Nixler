@extends('layouts.dashboard')

@section('layout')

<div class="_crd _mb15">
	<h2 class="_crd-header">@lang('Latest articles')</h2>
	<div class="_crd-content">
		
		@if($articles->count())
		<span class="_clear _pl15 _pr15 _mt5 _mb10 _c2">

			<div class="row">
				<div class="col-xs-6">@lang('Article')</div>
				<div class="col-xs-3 _oh">@lang('Slug')</div>
				<div class="col-xs-3 _oh">@lang('Last Update')</div>
			</div>

		</span>
		@endif

		<div id="articles">
			@forelse($articles as $article)
			<a class="_lim _clear _pl15 _pr15 _hvrl _bt1 _bcg" href="{{ route('articles.edit', ['slug' => $article->slug]) }}">

				<div class="row">
					<div class="col-xs-6">
						<div class="_pl15 _pr15 _oh _pt5">
							<span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs15">
								{{ $article->title }}
							</span>
						</div>
					</div>
					<div class="col-xs-3">
						{{ $article->slug }}
					</div>
					<div class="col-xs-3">
						{{ $article->updated_at->diffForHumans() }}
					</div>
				</div>

			</a>

			@empty
			<div class="_posr _clear _mih250 _tac">
				<div class="_a0 _posa">
					<span class="_fs16 _c2">@lang('A list of articles is empty.')</span><br>
					@lang('Add new article by clicking on add new article button.')<br>

					<a class="_btn _bga _c2 _mt15" style="line-height: 29px;" href="{{ route('article.create') }}">
						<i class="material-icons _fs20 _va6">add</i>
						@lang('Add new article')
					</a>

				</div>
			</div>

			@endforelse
		</div>

	</div>
</div>

{{ $articles->links() }}
@endsection