<div class="container">
    <button class="btn btn-primary mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#newAddress" aria-controls="newAddress" wire:click="setAction('new')">{{ __('New Address') }}</button>
    <table class="table table-striped table-hover">
        <thead>
            <tr class="table-dark">
                <th scope="col">#</th>
                <th scope="col">Address</th>
                <th scope="col">Created</th>
                <th scope="col">Updated</th>
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
                    <a class="btn btn-primary" href="{{ route('address', ['action' => 'update', 'id' => $address->id]) }}" role="button">{{ __('Edit') }}</a>
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
                </div>
                <button type="button" class="btn btn-primary" wire:click="saveNewAddress">{{ __('Save') }}</button>
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
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Created') }}</label>
                    <input type="text" class="form-control" wire:model="createdAt" readonly disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Updated') }}</label>
                    <input type="text" class="form-control" wire:model="updatedAt" readonly disabled>
                </div>
                <button type="button" class="btn btn-primary" wire:click="updateAddress">{{ __('Update') }}</button>
            </div>
        </div>
    </div>
</div>
