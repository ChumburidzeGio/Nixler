@extends('user::profile.layout')

@section('user_content')
<div class="row">

@each('product::short-card', $data, 'product')

</div>
@endsection