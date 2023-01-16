<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ $basket->shop->name }}</h4>
        <h5 class="card-subtitle">{{ $basket->date }}</h5>
        <p class="card-text">{{ $basket->total }} Ft<br/>
            {{ $basket->basketItems->count() }} items</p>
    </div>
</div>
