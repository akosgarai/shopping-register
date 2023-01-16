@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @foreach ($baskets as $receipt)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $receipt->shop->name }}</h4>
                    <h5 class="card-subtitle">{{ $receipt->date }}</h5>
                    <p class="card-text">{{ $receipt->total }} Ft<br/>
                        {{ $receipt->basketItems->count() }} items</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
