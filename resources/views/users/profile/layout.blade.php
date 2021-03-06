@extends('layouts.app')

@section('body_class', '_bgw')

@section('content')
  
<div style="margin-top: -15px;" ng-controller="ProfileCtrl as vm" class="_clear">
  <div class="col-xs-12 _p0">

    <div class="_bgcrm _bb1 _clear">
      <div class="_posr">

        @if($user->firstMedia('cover'))
        <img src="{{ media($user->firstMedia('cover'), 'cover', 'profile') }}" class="_clear _w100 _z013" height="130px">
        @else
        <div class="_w100 _bgg _clear" style="height:130px"></div> 
        @endif

        @if(auth()->check() && auth()->user()->id == $user->id)
        <form id="cover-upload-form" action="{{ route('user.uploadPhoto', ['id' => $user->username]) }}" 
           method="POST" enctype="multipart/form-data" class="_posa _a2 _p10">

           {{ csrf_field() }}

           <label for="cover-picker-input" class="_m0"> 
             <span class="label _bgi _crp">@lang('Change')</span> 
         </label>

         <input type="hidden" name="_t" value="2">
         <input type="file" id="cover-picker-input" 
         onchange="event.preventDefault();
         document.getElementById('cover-upload-form').submit();"
         name="_s" style="visibility: hidden;position:absolute;width:0;">

     </form>
     @endif
 </div>
 <div class="_tac _pb15">
  <div class="_posr">
    <img src="{{ $user->avatar('profile') }}" class="_brds3 _dib _ma _mb10 _b1 _bcw _bw2 _clear _mt-50" height="120px" width="120px" alt="{{ $user->name }}">

    @if(auth()->check() && auth()->user()->id == $user->id)
    <form id="avatar-upload-form" action="{{ route('user.uploadPhoto', ['id' => $user->username]) }}" 
       method="POST" enctype="multipart/form-data" class="_posa _a5">

       {{ csrf_field() }}

       <label for="avatar-picker-input" class="_m0"> 
         <span class="label _bgi _crp">@lang('Change')</span> 
     </label>

     <input type="hidden" name="_t" value="1">
     <input type="file" id="avatar-picker-input" 
     onchange="event.preventDefault();
     document.getElementById('avatar-upload-form').submit();"
     name="_s" style="visibility: hidden;position:absolute;width:0;">

 </form>
 @endif
</div>

<h1 class="_lh1 _et2 _fs24 _c2 _lh1 _fw400 _m0 _mb5 _mt10">{{ $user->name }}</h1>
<p class="_clear _fs14 _cbt8">
 @if(!$user->getMeta('headline'))
 @lang('Member since'): {{ $user->created_at->formatLocalized('%d %B %Y') }}
 @else
 {{ $user->getMeta('headline') }}
 @endif
 @if($user->getMeta('website'))
 · <a href="{{ $user->getMeta('website') }}" target="_blank" rel="nofollow noopener">
 <i class="material-icons _va7">link</i> {{ parse_url($user->getMeta('website'), PHP_URL_HOST) }}
</a>
@endif
</p>
@if(auth()->guest() || auth()->user()->id !== $user->id)
<div class="_clear">

    <div class="_btn _bgi _cw _mt5 _fs15 _p3 _pl15 _pr15 _mr10" onclick="event.preventDefault();
    document.getElementById('shfol12').submit();">
    <i class="material-icons _mr5 _va5 _fs20">
      {{ auth()->check() && auth()->user()->isFollowing($user->id) ? 'check' : 'person_add' }}</i>
      {{ auth()->check() && auth()->user()->isFollowing($user->id) ? __('Following') : __('Follow') }}
      <form id="shfol12" action="{{ route('user.follow', ['id' => $user->username]) }}" method="POST" class="_d0">
        {{ csrf_field() }}
    </form>
</div>

<a class="_btn _bg5 _cb _mt5 _fs15 _p3 _pl15 _pr15" href="{{ route('find-thread', ['id' => $user->id]) }}" id="messageAccount">
  <i class="material-icons _mr5 _va5 _fs20">message</i> @lang('Message')
</a>

@can('impersonate')
<a class="_btn _bg5 _cb _mt5 _fs15 _p3 _pl15 _pr15 _ml10" href="{{ route('impersonate', $user->id) }}">
  <i class="material-icons _mr5 _va5 _fs20">adb</i> @lang('Impersonate')
</a>
@endcan

</div>
@endif
</div>

<div class="_tbs _tal _pt4 _fw600 _pl5 container">
<a class="_fs12 _tb{{$tab == 'about' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link('about') }}">
    @lang('About')
</a>
  <a class="_fs12 _tb{{$tab == 'profile' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link() }}">
    @lang('Liked')
</a>
<a class="_fs12 _tb{{$tab == 'products' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link('products') }}">
    @lang('Selling') <span class="_fw300 _ml5">{{ $user->products_count }}</span>
</a>
<a class="_fs12 _tb{{$tab == 'followers' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link('followers') }}">
    @lang('Followers') <span class="_fw300 _ml5">{{ $user->followers_count }}</span>
</a>
<a class="_fs12 _tb{{$tab == 'followings' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link('followings') }}">
    @lang('Following', ['amount' => 1])
</a>
<a class="_fs12 _tb{{$tab == 'photos' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link('photos') }}">
    @lang('Photos')
</a>
            <!--a class="_tb _right _fw300" href="/">Share</a>
            <a class="_tb _right _fw300" href="/" onclick='event.preventDefault();if(prompt("Please write shortly the reason")){document.getElementById("avatar-upload-form").submit();}'>Report</a-->
            </div>

        </div>

        <div class="_mt15 _pt5 container">

            
            @yield('user_content')

</div>
</div>
</div>
@endsection