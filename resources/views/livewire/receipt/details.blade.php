<div class="card text-center">
    <div class="card-body">
        <h4 class="card-title">{{ $basket->shop->company->name }}</h4>
        <h5 class="card-subtitle">{{ $basket->shop->company->address->raw }}</h5>
        <h4 class="card-title">{{ $basket->shop->name }}</h4>
        <h5 class="card-subtitle">{{ $basket->shop->address->raw }}</h5>
        <h5 class="card-subtitle mt-1">{{ __('Tax number:') }} {{ $basket->shop->company->tax_number }}</h5>
    </div>
    <ul class="list-group list-group-flush">
        @foreach ($basket->basketItems as $item)
            <li class="list-group-item">
                <div class="d-flex justify-content-between flex-wrap">
                    <div>{{ $item->item->name }}</div>
                    <div>{{ $item->price }} Ft</div>
                </div>
            </li>
        @endforeach
    </ul>
    <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap fw-bold">
            <div class="text-uppercase">{{ __('Sum') }}</div>
            <div>{{ $basket->total }} Ft</div>
        </div>
        <div class="d-flex justify-content-between flex-wrap">
            <div>{{ __('Receipt ID') }}</div>
            <div>{{ $basket->receipt_id }}</div>
        </div>
        <div class="d-flex justify-content-between flex-wrap">
            <div>{{ __('Date') }}</div>
            <div>{{ $basket->date }}</div>
        </div>
    </div>
</div>
