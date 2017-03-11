@extends('layouts.app')

@section('content')

<div class="container">

		<div class="row">

			<div class="col-md-12">

				<div class="row _mt15">
				@each('product::short-card', $products, 'product')
				</div>

			</div>


		</div>
</div>

@endsection
