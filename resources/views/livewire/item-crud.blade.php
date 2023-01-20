<div class="container">
    <button class="btn btn-primary mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#newItem" aria-controls="newItem" wire:click="setAction('new')">{{ __('New Item') }}</button>
    <table class="table table-striped table-hover">
        <thead>
            <tr class="table-dark">
                <th scope="col">#</th>
                <th scope="col">{{ __('Name') }}</th>
                <th scope="col">{{ __('Created') }}</th>
                <th scope="col">{{ __('Updated') }}</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <th scope="row">{{ $item->id }}</th>
                <td>{{ $item->name }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
                <td>
                    <button class="btn btn-primary" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#updateItem"
                        aria-controls="updateItem" wire:click="load({{ $item->id }})">{{ __('Edit') }}
                    </button>
                    @if($item->basketItems->count() == 0)
                        <button class="btn btn-danger" type="button" wire:click="delete({{ $item->id }})">{{ __('Delete') }}
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'new') show @endif" data-bs-scroll="true" tabindex="-1" id="newItem" aria-labelledby="newItemLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="newItemLabel">{{ __('New Item') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                @include('livewire.component.textinput', ['modelId' => 'itemName', 'formLabel' => __('Item Name')])
                <button type="button" class="btn btn-primary" wire:click="saveNew">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'update') show @endif" data-bs-scroll="true" tabindex="-1" id="updateItem" aria-labelledby="updateItemLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="updateItemLabel">{{ __('Update Item') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                @include('livewire.component.textinput', ['modelId' => 'itemName', 'formLabel' => __('Item Name')])
                @include('livewire.component.textinput', ['modelId' => 'createdAt', 'formLabel' => __('Created'), 'readonly' => true])
                @include('livewire.component.textinput', ['modelId' => 'updatedAt', 'formLabel' => __('Updated'), 'readonly' => true])
                <button type="button" class="btn btn-primary" wire:click="update">{{ __('Update') }}</button>
            </div>
        </div>
    </div>
    @include('livewire.component.offcanvasscipts')
</div>
