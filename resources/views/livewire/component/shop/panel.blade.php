<div>
    @if($action == 'create')
        @include('livewire.component.shop.create')
    @elseif($action == 'update')
        @include('livewire.component.shop.update')
    @elseif($action == 'delete')
        @include('livewire.component.shop.delete')
    @elseif($action == 'view')
        @include('livewire.component.shop.view')
    @endif
</div>
