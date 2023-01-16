@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <livewire:receipt.details :basket="$basket" />
    </div>
</div>
@endsection
