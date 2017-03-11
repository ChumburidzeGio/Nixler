@extends('user::settings.layout')

@section('title')
{{ trans('user::settings.emails.title')}}
@endsection

@section('settings')

@foreach($emails as $mail)
<div class="_li">
	<span class="_cb _clear">
		{{ $mail->address }} 
		@if($mail->is_default)  
			<span class="_bga _p5 _fs11 _cb _brds3">
				{{ trans('user::settings.emails.is_default')}}
			</span> 
		@endif
	</span>
	<span class="">
		@if(!$mail->is_verified) {{ trans('user::settings.emails.not_verified')}} · @endif
		
		{{ trans('user::settings.emails.added_at')}} {{ $mail->created_at->format('F jS, Y') }}
		<span class="_right">
			@if(!$mail->is_verified)
			<a class="_cdp" href="{{ url('/settings/emails/'.$mail->id.'/verify') }}">
				{{ trans('user::settings.emails.resend_code')}}
			</a> · 
			@endif
			@if($mail->is_verified && !$mail->is_default)
			<a class="_cdp" href="{{ url('/settings/emails/'.$mail->id.'/default') }}">
				{{ trans('user::settings.emails.make_default')}}
			</a> · 
			@endif
			<a class="_cdp" href="{{ url('/settings/emails/'.$mail->id.'/delete') }}">
				{{ trans('user::settings.emails.delete')}}
			</a>
		</span>

		@if(!$mail->is_verified && $mail->verification_code)
		<form class="row _mt5" method="POST" action="{{ url('/settings/emails/'.$mail->id.'/code') }}">
			{{ csrf_field() }}

			<div class="col-xs-4">
				<input class="_fe _fes _b1 _brds3 _w25" 
					type="text" 
					onkeyup="this.value=this.value.replace(/[^\d]/,'')"
					pattern=".{6}" required="1" maxlength="6"
					name="{{ $mail->id }}code" 
					placeholder="{{ trans('user::settings.emails.verification_code')}}">
			</div>
			<div class="col-xs-3"><button class="_btn _bgi _cw">
				{{ trans('user::settings.emails.verify')}}
			</button></div>

			@if ($errors->has($mail->id.'code'))
			<span class="col-xs-12">{{ $errors->first($mail->id.'code') }}</span>
			@endif

		</form>
		@endif

	</span>
</div>

<hr>
@endforeach

<form class="form-horizontal" role="form" method="POST" action="{{ url('/settings/emails') }}">
	{{ csrf_field() }}

	<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
		<label for="email" class="col-md-4 control-label">
				{{ trans('user::settings.emails.email_label')}}
		</label>

		<div class="col-md-6">
			<input id="email" type="email" class="form-control" name="email" required autofocus >

			@if ($errors->has('email'))
			<span class="help-block">
				<strong>{{ $errors->first('email') }}</strong>
			</span>
			@endif
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<button type="submit" class="btn btn-primary">
				{{ trans('user::settings.emails.create_new')}}
			</button>
		</div>
	</div>
</form>

@endsection