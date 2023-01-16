@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @foreach ($baskets as $receipt)
        <div class="col-md-4">
            <livewire:receipt.card :basket="$receipt" />
        </div>
        @endforeach
    </div>
</div>
@endsection
