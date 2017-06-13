@extends('users.profile.layout')

@section('user_content')

<script>window.stream = {!! $data->toJson() !!};</script>

@include('products.index')

@endsection