<div>
@if($model)
    <div class="text-center">
        {{ $model['shop']['company']['name'] }}<br>
        {{ $model['shop']['company']['address']['raw'] }}<br>
        {{ $model['shop']['name'] }}<br>
        {{ $model['shop']['address']['raw'] }}<br>
        {{ $model['shop']['company']['tax_number'] }}<br>
    </div>
    <hr>
    @foreach($model['basket_items'] as $key => $basketItem)
        <div class="d-flex justify-content-between flex-wrap">
            <div>{{ $basketItem['item']['name'] }}</div>
            <div>{{ $basketItem['price'] }}</div>
        </div>
    @endforeach
    <hr>
    <div class="d-flex justify-content-between flex-wrap">
        <div>{{ __('Total') }}</div>
        <div>{{ $model['total'] }}</div>
    </div>
    <div class="d-flex justify-content-between flex-wrap">
        <div>{{ __('Date') }}</div>
        <div>{{ $model['date'] }}</div>
    </div>
    <div class="d-flex justify-content-between flex-wrap">
        <div>{{ __('Receipt ID') }}</div>
        <div>{{ $model['receipt_id'] }}</div>
    </div>
    @if($model['receipt_url'])
        <img src="{{ route('image.viewReceipt', ['filename' =>  $model['receipt_url']]) }}" class="img-fluid" />
        @if($edit) <button wire:click="$emitUp('basket.image', 'change', {{ $model['id'] }})" class="btn btn-primary">{{ __('Change Image') }}</button> @endif
    @else
        @if($edit) <button wire:click="$emitUp('basket.image', 'add', {{ $model['id'] }})" class="btn btn-primary">{{ __('Add Image') }}</button> @endif
    @endif
@endif
</div>
