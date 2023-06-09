@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Last Baskets') }}</div>

                <div class="card-body chart-container">
                    <livewire:livewire-column-chart
                        key="{{ $lastBasketsModel->reactiveKey() }}"
                        :column-chart-model="$lastBasketsModel"
                        />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Last Basket Items') }}</div>

                <div class="card-body chart-container">
                    <livewire:livewire-column-chart
                        key="{{ $lastItemsModel->reactiveKey() }}"
                        :column-chart-model="$lastItemsModel"
                        />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Frequently Bought Items') }}</div>

                <div class="card-body chart-container">
                    <livewire:livewire-column-chart
                        key="{{ $frequentItemsModel->reactiveKey() }}"
                        :column-chart-model="$frequentItemsModel"
                        />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Frequently Bought Item Prices') }}</div>

                <div class="card-body chart-container">
                    <livewire:livewire-line-chart
                        key="{{ $frequentItemsPriceModel->reactiveKey() }}"
                        :line-chart-model="$frequentItemsPriceModel"
                        />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Daily Expenses') }}</div>

                <div class="card-body chart-container">
                    <livewire:livewire-line-chart
                        key="{{ $basketPriceModel->reactiveKey() }}"
                        :line-chart-model="$basketPriceModel"
                        />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Monthly Expenses') }}</div>

                <div class="card-body chart-container">
                    <livewire:livewire-line-chart
                        key="{{ $basketMonthPriceModel->reactiveKey() }}"
                        :line-chart-model="$basketMonthPriceModel"
                        />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Frequently Used Shops') }}</div>

                <div class="card-body chart-container">
                    <livewire:livewire-column-chart
                        key="{{ $frequentShopsModel->reactiveKey() }}"
                        :column-chart-model="$frequentShopsModel"
                        />
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">{{ __('Expenses By Shops') }}</div>

                <div class="card-body chart-container">
                    <livewire:livewire-pie-chart
                        key="{{ $expensesByShopsModel->reactiveKey() }}"
                        :pie-chart-model="$expensesByShopsModel"
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
