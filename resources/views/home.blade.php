@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Last Baskets') }}</div>

                <div class="card-body">
                    <livewire:livewire-column-chart
                        key="{{ $lastBasketsModel->reactiveKey() }}"
                        :column-chart-model="$lastBasketsModel"
                        />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-scripts')
    @vite(['resources/js/dashboard.js'])
@endsection
