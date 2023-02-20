<div>
    @if($action == 'create')
        @include('livewire.component.company.create')
    @elseif($action == 'update')
        @include('livewire.component.company.update')
    @elseif($action == 'delete')
        @include('livewire.component.company.delete')
    @elseif($action == 'view')
        @include('livewire.component.company.view')
    @endif
</div>
