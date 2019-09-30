@extends('layouts.app')

@section('content')
    <div class="container">
        @if(Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
                @php
                    Session::forget('success');
                @endphp
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning" uk-alert>
                {{ Session::get('error') }}
                @php
                    Session::forget('error');
                @endphp
            </div>
        @endif
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-3 my-3">
                    <div class="card">
                        <div class="card-header">
                            <!-- The product name (duh..) -->
                            {{ $product->name }}
                        </div>
                        <div class="card-body">
                            <img 
                                src="https://images.unsplash.com/photo-1564584217132-2271feaeb3c5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80" 
                                alt="T-shirt" 
                                class="img-fluid rounded mb-2"
                            >
                            <h3>
                                <!-- We format the number to a price with currency behind it -->
                                {{ number_format($product->price, 2) }} USD
                            </h3>
                            <a href="{{ route('add', [ $product->getRouteKey() ]) }}">
                                <!-- The button for adding the product to the cart -->
                                <button class="btn btn-primary btn-block">Add to cart</button>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection