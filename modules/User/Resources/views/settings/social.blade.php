@extends('user::settings.layout')

@section('title')
{{ trans('user::settings.social.title')}}
@endsection

@section('settings')

<form class="form-horizontal" role="form" method="POST" action="{{ url('/settings/social') }}">
	{{ csrf_field() }}

	<div class="form-group{{ $errors->has('facebook') ? ' has-error' : '' }}">
		<label for="facebook" class="col-md-4 control-label">
			Facebook
		</label>

		<div class="col-md-6">
			<input id="facebook" type="text" class="form-control" name="facebook" value="{{ $facebook }}" placeholder="{{ trans('user::settings.social.facebook_plac')}}">

			@if ($errors->has('facebook'))
			<span class="help-block">
				<strong>{{ $errors->first('facebook') }}</strong>
			</span>
			@endif
		</div>
	</div>

	<div class="form-group{{ $errors->has('linkedin') ? ' has-error' : '' }}">
		<label for="linkedin" class="col-md-4 control-label">
			Linkedin
		</label>

		<div class="col-md-6">
			<input id="linkedin" type="text" class="form-control" name="linkedin" value="{{ $linkedin }}" placeholder="{{ trans('user::settings.social.linkedin_plac')}}">

			@if ($errors->has('linkedin'))
			<span class="help-block">
				<strong>{{ $errors->first('linkedin') }}</strong>
			</span>
			@endif
		</div>
	</div>


	<div class="form-group{{ $errors->has('twitter') ? ' has-error' : '' }}">
		<label for="twitter" class="col-md-4 control-label">
			Twitter
		</label>

		<div class="col-md-6">
			<input id="twitter" type="text" class="form-control" name="twitter" value="{{ $twitter }}" placeholder="{{ trans('user::settings.social.twitter_plac')}}">

			@if ($errors->has('twitter'))
			<span class="help-block">
				<strong>{{ $errors->first('twitter') }}</strong>
			</span>
			@endif
		</div>
	</div>


	<div class="form-group{{ $errors->has('vk') ? ' has-error' : '' }}">
		<label for="vk" class="col-md-4 control-label">
			VK
		</label>

		<div class="col-md-6">
			<input id="vk" type="text" class="form-control" name="vk" value="{{ $vk }}" placeholder="{{ trans('user::settings.social.vk_plac')}}">

			@if ($errors->has('vk'))
			<span class="help-block">
				<strong>{{ $errors->first('vk') }}</strong>
			</span>
			@endif
		</div>
	</div>


	<div class="form-group{{ $errors->has('blog') ? ' has-error' : '' }}">
		<label for="blog" class="col-md-4 control-label">
			{{ trans('user::settings.social.blog')}}
		</label>

		<div class="col-md-6">
			<input id="blog" type="text" class="form-control" name="blog" value="{{ $blog }}" placeholder="{{ trans('user::settings.social.blog_plac')}}">

			@if ($errors->has('blog'))
			<span class="help-block">
				<strong>{{ $errors->first('blog') }}</strong>
			</span>
			@endif
		</div>
	</div>

	<div class="form-group{{ $errors->has('website') ? ' has-error' : '' }}">
		<label for="website" class="col-md-4 control-label">
			{{ trans('user::settings.social.website')}}
		</label>

		<div class="col-md-6">
			<input id="website" type="text" class="form-control" name="website" value="{{ $website }}" placeholder="{{ trans('user::settings.social.website_plac')}}">

			@if ($errors->has('website'))
			<span class="help-block">
				<strong>{{ $errors->first('website') }}</strong>
			</span>
			@endif
		</div>
	</div>


	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<button type="submit" class="btn btn-primary">
				{{ trans('user::settings.social.save')}}
			</button>
		</div>
	</div>
</form>

@endsection