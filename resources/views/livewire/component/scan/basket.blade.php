<div>
    <form wire:submit.prevent="validateInputs">
        @include('livewire.component.forms.textinput', ['modelId' => 'basketId', 'formLabel' => __('Receipt ID')])
        @include('livewire.component.forms.datetimelocalinput', ['modelId' => 'basketDate', 'formLabel' => __('Date')])
        <div class="d-flex flex-row-reverse">
            @if ($basketId != '' && $basketDate != '')
                <button type="submit" class="btn btn-success">{{ __('Setup Company') }}</button>
            @endif
        </div>
    </form>
@if(count($suggestions) > 0)
    <h4>{{ __('Similar Baskets') }}</h4>
    @foreach($suggestions as $key => $suggestion)
        <span style="margin-right: 10px;" >
            <a href="#" wire:click.prevent="selectPreview( {{ $key }} )" >{{ $suggestion['receipt_id'] }}</a> ({{ $suggestion['percentage'] }}%)</span>
        @endforeach
    @if($basketPreview)
        @include('livewire.component.basket.view', ['model' => $basketPreview, 'edit' => true])
    @endif
@endif
</div>
