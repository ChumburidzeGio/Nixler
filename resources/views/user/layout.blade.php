@extends('layouts.app')

@section('content')
<div class="container">
<div class="col-md-10 col-md-offset-1 col-xs-12 _p0">

    <div class="_bgw _b1 _brds3 _clear">
        <div class="_posr">
            <img src="{{ $user->cover('profile') }}" class="_clear _w100">

            @if(auth()->check() && auth()->user()->id == $user->id)
              <form id="avatar-upload-form" action="{{ $user->link('/photos') }}" 
               method="POST" enctype="multipart/form-data" class="_posa _a2 _p10">

               {{ csrf_field() }}

               <label for="picker-input" class="_m0"> 
                 <span class="label _bgi _crp">Change</span> 
               </label>
              
              <input type="hidden" name="_t" value="2">
               <input type="file" id="picker-input" 
               onchange="event.preventDefault();
               document.getElementById('avatar-upload-form').submit();"
               name="_s" style="visibility: hidden;position:absolute;width:0;">

             </form>
             @endif
        </div>
        <div class="_tac _pb15 _bb1">
          <div class="_posr">
              <img src="{{ $user->avatar('profile') }}" class="_brds2 _dib _ma _mb10 _b1 _bcg _bw2 _clear _mt-50" height="100" width="100">

              @if(auth()->check() && auth()->user()->id == $user->id)
              <form id="avatar-upload-form" action="{{ $user->link('/photos') }}" 
               method="POST" enctype="multipart/form-data" class="_posa _a5">

               {{ csrf_field() }}

               <label for="picker-input" class="_m0"> 
                 <span class="label _bgi _crp">Change</span> 
               </label>
              
              <input type="hidden" name="_t" value="1">
               <input type="file" id="picker-input" 
               onchange="event.preventDefault();
               document.getElementById('avatar-upload-form').submit();"
               name="_s" style="visibility: hidden;position:absolute;width:0;">

             </form>
             @endif
         </div>

             <a href="{{ url('/login') }}" class="_lh1 _et2 _fs24 _clear">{{ $user->name }}</a>
             <small class="_clear">Member since: {{ $user->created_at->format('F jS, Y') }}</small>
            
            @if(auth()->guest() || auth()->user()->id !== $user->id)
             <div class="_clear">

                <div class="_btn _mt5 _bgi _cw" onclick="event.preventDefault();
                        document.getElementById('shfol12').submit();">
                        <i class="material-icons _va4 _fs18">
                        {{ auth()->check() && auth()->user()->isFollowing($user->id) ? 'check' : 'person_add' }}</i>
                        {{ auth()->check() && auth()->user()->isFollowing($user->id) ? 'Following' : 'Follow' }}
                    <form id="shfol12" action="{{ $user->link('/follow') }}" method="POST" class="_d0">
                        {{ csrf_field() }}
                    </form>
                </div>

                <div class="_btn _bgw _cg _mt5 _b1 _bcg">Message</div>
             </div>
             @endif
        </div>

        <div class="_tbs  _tal _pt4 _fw600 _pl5">
            <a class="_fs12 _tb{{$page == 'liked' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link() }}">
                Liked <span class="_fw300 _ml5">{{ $user->liked_count }}</span>
            </a>
            <a class="_fs12 _tb{{$page == 'products' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link('/products') }}">
                Selling <span class="_fw300 _ml5">{{ $user->selling_count }}</span>
            </a>
            <a class="_fs12 _tb{{$page == 'followers' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link('/followers') }}">
                Followers <span class="_fw300 _ml5">{{ $user->followers_count }}</span>
            </a>
            <a class="_fs12 _tb{{$page == 'followings' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link('/followings') }}">
                Followings <span class="_fw300 _ml5">{{ $user->followings_count }}</span>
            </a>
            <a class="_fs12 _tb{{$page == 'media' ? ' _bb1 _bw2 _ci _bci' : ''}}" href="{{ $user->link('/photos') }}">
                Photos <span class="_fw300 _ml5">{{ $user->media_count }}</span>
            </a>
            <!--a class="_tb _right _fw300" href="/">Share</a>
            <a class="_tb _right _fw300" href="/" onclick='event.preventDefault();if(prompt("Please write shortly the reason")){document.getElementById("avatar-upload-form").submit();}'>Report</a-->
        </div>

    </div>

    <div class="_mt15 _pt5">


            @yield('user_content')

</div>
</div>
@endsection