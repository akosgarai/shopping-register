<div>
    <form wire:submit.prevent="validateInputs">
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'basketId', 'formLabel' => __('Receipt ID')])
        @include('livewire.component.offcanvasform.datetimelocalinput', ['modelId' => 'basketDate', 'formLabel' => __('Date')])
        <div class="d-flex flex-row-reverse">
            <button type="submit" class="btn btn-success">{{ __('Setup Company') }}</button>
        </div>
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
