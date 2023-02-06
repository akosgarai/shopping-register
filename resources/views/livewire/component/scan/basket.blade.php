<div>
    <form wire:submit.prevent="$emitUp('basket.data.update', 'basketId', @this.basketId)">
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'basketId', 'formLabel' => __('Receipt ID')])
        <button type="submit" class="btn btn-primary">{{ __('Next') }}</button>
    </form>
@if(count($suggestions) > 0)
    <h4>{{ __('Similar Baskets') }}</h4>
    @foreach($suggestions as $key => $suggestion)
        <span style="margin-right: 10px;" >
            <a href="#" wire:click.prevent="selectPreview( {{ $key }} )" >{{ $suggestion['receipt_id'] }}</a> ({{ $suggestion['percentage'] }}%)</span>
        @endforeach
    @if($basketPreview)
        @include('livewire.component.basket.view', ['visibleBasket' => $basketPreview, 'edit' => true])
    @endif
@endif
</div>
