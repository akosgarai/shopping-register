<div class="container">
    <button class="btn btn-primary mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#newShop" aria-controls="newShop" wire:click="setAction('new')">{{ __('New Shop') }}</button>
    <table class="table table-striped table-hover">
        <thead>
            <tr class="table-dark">
                <th scope="col">#</th>
                <th scope="col">{{ __('Name') }}</th>
                <th scope="col">{{ __('Company') }}</th>
                <th scope="col">{{ __('Address') }}</th>
                <th scope="col">{{ __('Created') }}</th>
                <th scope="col">{{ __('Updated') }}</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shops as $shop)
            <tr>
                <th scope="row">{{ $shop->id }}</th>
                <td>{{ $shop->name }}</td>
                <td>{{ $shop->company->name }}</td>
                <td>{{ $shop->address->raw }}</td>
                <td>{{ $shop->created_at }}</td>
                <td>{{ $shop->updated_at }}</td>
                <td>
                    <button class="btn btn-primary mb-1" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#updateShop"
                        aria-controls="updateShop" wire:click="loadShop({{ $shop->id }})">{{ __('Edit') }}
                    </button>
                    @if($shop->baskets->count() == 0)
                        <button class="btn btn-danger mb-1" type="button" wire:click="deleteShop({{ $shop->id }})">{{ __('Delete') }}
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'new') show @endif" data-bs-scroll="true" tabindex="-1" id="newShop" aria-labelledby="newShopLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="newShopLabel">{{ __('New Shop') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                <form wire:submit.prevent="saveNewShop">
                    <div class="mb-3">
                        <label for="shopName" class="form-label">{{ __('Shop Name') }}</label>
                        <input type="text" class="form-control" id="shopName" wire:model="shopName">
                    </div>
                    <div class="mb-3">
                        <label for="shopCompany" class="form-label">{{ __('Company') }}</label>
                        <select class="form-select" wire:model="shopCompany" id="shopCompany">
                            <option value="" @if($shopCompany == "") selected @endif>{{ __('Select Company') }}</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company['id'] }}" @if($shopCompany == $company['id']) selected @endif>{{ $company['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="shopAddress" class="form-label">{{ __('Address') }}</label>
                        <select class="form-select" wire:model="shopAddress" id="shopAddress">
                            <option value="" @if($shopAddress == "") selected @endif>{{ __('Select Address') }}</option>
                            @foreach ($addresses as $address)
                                <option value="{{ $address['id'] }}" @if($shopAddress == $address['id']) selected @endif>{{ $address['raw'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'update') show @endif" data-bs-scroll="true" tabindex="-1" id="updateShop" aria-labelledby="updateShopLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="updateShopLabel">{{ __('Update Shop') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                <form wire:submit.prevent="updateShop">
                    <div class="mb-3">
                        <label for="shopName" class="form-label">{{ __('Shop Name') }}</label>
                        <input type="text" class="form-control" id="shopName" wire:model="shopName">
                    </div>
                    <div class="mb-3">
                        <label for="shopCompany" class="form-label">{{ __('Company') }}</label>
                        <select class="form-select" wire:model="shopCompany" id="shopCompany">
                            <option value="" @if($shopCompany == "") selected @endif>{{ __('Select Company') }}</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company['id'] }}" @if($shopCompany == $company['id']) selected @endif>{{ $company['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="shopAddress" class="form-label">{{ __('Address') }}</label>
                        <select class="form-select" wire:model="shopAddress" id="shopAddress">
                            <option value="" @if($shopAddress == "") selected @endif>{{ __('Select Address') }}</option>
                            @foreach ($addresses as $address)
                                <option value="{{ $address['id'] }}" @if($shopAddress == $address['id']) selected @endif>{{ $address['raw'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Created') }}</label>
                        <input type="text" class="form-control" wire:model="createdAt" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Updated') }}</label>
                        <input type="text" class="form-control" wire:model="updatedAt" readonly disabled>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
