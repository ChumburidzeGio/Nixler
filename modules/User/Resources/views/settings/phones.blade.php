@extends('user::settings.layout')

@section('title')
{{ trans('user::settings.phones.title')}}
@endsection

@section('settings')

@foreach($phones as $mail)
<div class="_li">
	<span class="_cb _clear">
		{{ $mail->phone_number }} 
		@if($mail->is_default)  
			<span class="_bga _p5 _fs11 _cb _brds3">
				{{ trans('user::settings.phones.is_default')}}
			</span> 
		@endif
	</span>
	<span class="">
		@if(!$mail->is_verified) {{ trans('user::settings.phones.not_verified')}} · @endif
		
		{{ trans('user::settings.phones.added_at')}} {{ $mail->created_at->format('F jS, Y') }}
		<span class="_right">
			@if(!$mail->is_verified)
			<a class="_cdp" href="{{ url('/settings/phones/'.$mail->id.'/verify') }}">
				{{ trans('user::settings.phones.resend_code')}}
			</a> · 
			@endif
			@if($mail->is_verified && !$mail->is_default)
			<a class="_cdp" href="{{ url('/settings/phones/'.$mail->id.'/default') }}">
				{{ trans('user::settings.phones.make_default')}}
			</a> · 
			@endif
			<a class="_cdp" href="{{ url('/settings/phones/'.$mail->id.'/delete') }}">
				{{ trans('user::settings.phones.delete')}}
			</a>
		</span>

		@if(!$mail->is_verified && $mail->verification_code)
		<form class="row _mt5" method="POST" action="{{ url('/settings/phones/'.$mail->id.'/code') }}">
			{{ csrf_field() }}

			<div class="col-xs-4">
				<input class="_fe _fes _b1 _brds3 _w25" 
					type="text" 
					onkeyup="this.value=this.value.replace(/[^\d]/,'')"
					pattern=".{6}" required="1" maxlength="6"
					name="{{ $mail->id }}code" 
					placeholder="{{ trans('user::settings.phones.verification_code')}}">
			</div>
			<div class="col-xs-3"><button class="_btn _bgi _cw">
				{{ trans('user::settings.phones.verify')}}
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

<form class="form-horizontal" role="form" method="POST" action="{{ url('/settings/phones') }}">
	{{ csrf_field() }}

	<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
		<label for="phone" class="col-md-4 control-label">
				{{ trans('user::settings.phones.phone_label')}}
		</label>

		<div class="col-md-6">
			<input id="phone" type="text" class="form-control" name="phone" required autofocus >

			@if ($errors->has('phone'))
			<span class="help-block">
				<strong>{{ $errors->first('phone') }}</strong>
			</span>
			@endif
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<button type="submit" class="btn btn-primary">
				{{ trans('user::settings.phones.create_new')}}
			</button>
		</div>
	</div>
</form>

@endsection