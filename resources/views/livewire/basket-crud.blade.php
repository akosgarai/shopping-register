<div class="container">
    <button class="btn btn-primary mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#newBasket" aria-controls="newBasket" wire:click="setAction('new')">{{ __('New Basket') }}</button>
    <table class="table table-striped table-hover">
        <thead>
            <tr class="table-dark">
                <th scope="col">#</th>
                <th scope="col">{{ __('Date') }}</th>
                <th scope="col">{{ __('Shop') }}</th>
                <th scope="col">{{ __('Total') }}</th>
                <th scope="col">{{ __('Receipt ID') }}</th>
                <th scope="col">{{ __('Items') }}</th>
                <th scope="col">{{ __('Created') }}</th>
                <th scope="col">{{ __('Updated') }}</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($baskets as $basket)
            <tr>
                <th scope="row">{{ $basket->id }}</th>
                <td>{{ $basket->date }}</td>
                <td>{{ $basket->shop->name }}<br />{{ $basket->shop->address->raw }}</td>
                <td>{{ $basket->total }}</td>
                <td>{{ $basket->receipt_id }}</td>
                <td>{{ $basket->basketItems->count() }}</td>
                <td>{{ $basket->created_at }}</td>
                <td>{{ $basket->updated_at }}</td>
                <td>
                    <button class="btn btn-primary mb-1" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#updateBasket"
                        aria-controls="updateBasket" wire:click="load({{ $basket->id }})">{{ __('Edit') }}
                    </button>
                    @if($basket->basketItems->count() == 0)
                        <button class="btn btn-danger mb-1" type="button" wire:click="delete({{ $basket->id }})">{{ __('Delete') }}
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'new') show @endif" data-bs-scroll="true" tabindex="-1" id="newBasket" aria-labelledby="newBasketLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="newBasketLabel">{{ __('New Basket') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                <form wire:submit.prevent="saveNew">
                    <div class="mb-3">
                        <label for="basketShop" class="form-label">{{ __('Shop') }}</label>
                        <select class="form-select" wire:model="basketShop" id="basketShop">
                            <option value="" @if($basketShop == "") selected @endif>{{ __('Select Shop') }}</option>
                            @foreach ($shops as $shop)
                                <option value="{{ $shop['id'] }}" @if($basketShop == $shop['id']) selected @endif>{{ $shop['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="basketDate" class="form-label">{{ __('Date') }}</label>
                        <input type="datetime-local" class="form-control" id="basketDate" wire:model="basketDate">
                    </div>
                    <div class="mb-3">
                        <label for="basketTotal" class="form-label">{{ __('Total') }}</label>
                        <input type="number" class="form-control" id="basketTotal" step="0.01" wire:model="basketTotal">
                    </div>
                    <div class="mb-3">
                        <label for="basketReceiptId" class="form-label">{{ __('Receipt ID') }}</label>
                        <input type="text" class="form-control" id="basketReceiptId" wire:model="basketReceiptId">
                    </div>
                    <div class="mb-3">
                        <label for="basketImage" class="form-label">{{ __('Receipt ID') }}</label>
                        <input type="file" class="form-control" id="basketImage" wire:model="basketImage">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'update') show @endif" data-bs-scroll="true" tabindex="-1" id="updateBasket" aria-labelledby="updateBasketLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="updateBasketLabel">{{ __('Update Basket') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                <form wire:submit.prevent="update">
                    <div class="mb-3">
                        <label for="basketShop" class="form-label">{{ __('Shop') }}</label>
                        <select class="form-select" wire:model="basketShop" id="basketShop">
                            <option value="" @if($basketShop == "") selected @endif>{{ __('Select Shop') }}</option>
                            @foreach ($shops as $shop)
                                <option value="{{ $shop['id'] }}" @if($basketShop == $shop['id']) selected @endif>{{ $shop->name }}, {{ $shop->address->raw }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="basketDate" class="form-label">{{ __('Date') }}</label>
                        <input type="datetime-local" class="form-control" id="basketDate" wire:model="basketDate">
                    </div>
                    <div class="mb-3">
                        <label for="basketTotal" class="form-label">{{ __('Total') }}</label>
                        <input type="number" class="form-control" id="basketTotal" step="0.01" wire:model="basketTotal">
                    </div>
                    <div class="mb-3">
                        <label for="basketReceiptId" class="form-label">{{ __('Receipt ID') }}</label>
                        <input type="text" class="form-control" id="basketReceiptId" wire:model="basketReceiptId">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Created') }}</label>
                        <input type="text" class="form-control" wire:model="createdAt" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Updated') }}</label>
                        <input type="text" class="form-control" wire:model="updatedAt" readonly disabled>
                    </div>
                    <button type="submit" class="btn btn-primary mb-3">{{ __('Update') }}</button>
                </form>
                @if($basketImageURL != "")
                    <img src="{{ $basketImageURL }}" class="img-fluid" />
                @endif
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.offcanvas').forEach((element) => {
            element.addEventListener('hidden.bs.offcanvas', event => {
                Livewire.emit('offcanvasClose');
            });
        });
    </script>
</div>
