@extends('layouts.general')

@section('app')

@include('/partials/seo')

<div class="_pb15 _mih90vh">

    @include('/partials/nav')

    @yield('content')

    @include('/partials/aside')

</div>

@include('/partials/footer')

@include('/partials/help-modal')

@endsection