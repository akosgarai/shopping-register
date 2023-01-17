<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ $basket->shop->name }} <span class="badge bg-light">
                <a href="{{ URL::route('receipts.view', [ 'id' => $basket->id ]) }}" target="blank">{{ $basket->receipt_id }}</a>
            </span>
        </h4>
        <h5 class="card-subtitle">{{ $basket->date }}</h5>
        <p class="card-text">{{ $basket->total }} Ft<br/>
            {{ $basket->basketItems->count() }} items</p>
    </div>
</div>
