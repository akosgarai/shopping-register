<div>
@if($visibleBasket)
    <div class="text-center">
        {{ $visibleBasket['shop']['company']['name'] }}<br>
        {{ $visibleBasket['shop']['company']['address']['raw'] }}<br>
        {{ $visibleBasket['shop']['name'] }}<br>
        {{ $visibleBasket['shop']['address']['raw'] }}<br>
        {{ $visibleBasket['shop']['company']['tax_number'] }}<br>
    </div>
    <hr>
    @foreach($visibleBasket['basket_items'] as $key => $basketItem)
        <div class="d-flex justify-content-between flex-wrap">
            <div>{{ $basketItem['item']['name'] }}</div>
            <div>{{ $basketItem['price'] }}</div>
        </div>
    @endforeach
    <hr>
    <div class="d-flex justify-content-between flex-wrap">
        <div>{{ __('Total') }}</div>
        <div>{{ $visibleBasket['total'] }}</div>
    </div>
    <div class="d-flex justify-content-between flex-wrap">
        <div>{{ __('Date') }}</div>
        <div>{{ $visibleBasket['date'] }}</div>
    </div>
    <div class="d-flex justify-content-between flex-wrap">
        <div>{{ __('Receipt ID') }}</div>
        <div>{{ $visibleBasket['receipt_id'] }}</div>
    </div>
    @if($visibleBasket['receipt_url'])
        <img src="{{ route('image.viewReceipt', ['filename' =>  $visibleBasket['receipt_url']]) }}" class="img-fluid" />
        @if($edit) <button wire:click="$emitUp('basket.image', 'change', {{ $visibleBasket['id'] }})" class="btn btn-primary">{{ __('Change Image') }}</button> @endif
    @else
        @if($edit) <button wire:click="$emitUp('basket.image', 'add', {{ $visibleBasket['id'] }})" class="btn btn-primary">{{ __('Add Image') }}</button> @endif
    @endif
@endif
</div>
