<div>
    @if($action == 'create')
        @include('livewire.component.item.create')
    @elseif($action == 'update')
        @include('livewire.component.item.update')
    @elseif($action == 'delete')
        @include('livewire.component.item.delete')
    @elseif($action == 'view')
        @include('livewire.component.item.view')
    @endif
</div>
