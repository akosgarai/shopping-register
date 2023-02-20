<div>
    @if($action == 'create')
        @include('livewire.component.address.create')
    @elseif($action == 'update')
        @include('livewire.component.address.update')
    @elseif($action == 'delete')
        @include('livewire.component.address.delete')
    @elseif($action == 'view')
        @include('livewire.component.address.view')
    @endif
</div>
