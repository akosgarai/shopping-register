<div class="container">
    <button class="btn btn-primary mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#newAddress" aria-controls="newAddress" wire:click="setAction('new')">{{ __('New Address') }}</button>
    <table class="table table-striped table-hover">
        <thead>
            <tr class="table-dark">
                <th scope="col">#</th>
                <th scope="col">{{ __('Address') }}</th>
                <th scope="col">{{ __('Created') }}</th>
                <th scope="col">{{ __('Updated') }}</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($addresses as $address)
            <tr>
                <th scope="row">{{ $address->id }}</th>
                <td>{{ $address->raw }}</td>
                <td>{{ $address->created_at }}</td>
                <td>{{ $address->updated_at }}</td>
                <td>
                    <button class="btn btn-primary" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#updateAddress"
                        aria-controls="updateAddress" wire:click="load({{ $address->id }})">{{ __('Edit') }}
                    </button>
                    @if($address->companies->count() == 0 && $address->shops->count() == 0)
                        <button class="btn btn-danger" type="button" wire:click="delete({{ $address->id }})">{{ __('Delete') }}
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'new') show @endif" data-bs-scroll="true" tabindex="-1" id="newAddress" aria-labelledby="newAddressLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="newAddressLabel">{{ __('New Address') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                <div class="mb-3">
                    <label for="addressRaw" class="form-label">{{ __('Address') }}</label>
                    <input type="text" class="form-control" id="addressRaw" wire:model="addressRaw">
                    <span id="errors-addressRaw" class="text-danger" style="display: none;"></span>
                </div>
                <button type="button" class="btn btn-primary" wire:click="saveNew">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'update') show @endif" data-bs-scroll="true" tabindex="-1" id="updateAddress" aria-labelledby="updateAddressLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="updateAddressLabel">{{ __('Update Address') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                <div class="mb-3">
                    <label for="addressRaw" class="form-label">{{ __('Address') }}</label>
                    <input type="text" class="form-control" id="addressRaw" wire:model="addressRaw">
                    <span id="errors-addressRaw" class="text-danger" style="display: none;"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Created') }}</label>
                    <input type="text" class="form-control" wire:model="createdAt" readonly disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Updated') }}</label>
                    <input type="text" class="form-control" wire:model="updatedAt" readonly disabled>
                </div>
                <button type="button" class="btn btn-primary" wire:click="update">{{ __('Update') }}</button>
            </div>
        </div>
    </div>
    @include('livewire.component.offcanvasscipts')
</div>
