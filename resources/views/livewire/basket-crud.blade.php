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
                    @include('livewire.component.offcanvasform.selectorshop', ['modelId' => 'basketShop', 'shops' => $shops, 'selected' => $basketShop])
                    @include('livewire.component.offcanvasform.datetimelocalinput', ['modelId' => 'basketDate', 'formLabel' => __('Date'), 'selected' => $basketShop])
                    @include('livewire.component.offcanvasform.textinput', ['modelId' => 'basketReceiptId', 'formLabel' => __('Receipt ID')])
                    <div class="current-basket-items">
                    </div>
                    @include('livewire.component.offcanvasform.basketitem', [
                        'itemModelId' => 'newBasketItemId',
                        'items' => $items,
                        'selected' => '',
                        'priceModelId' => 'newBasketItemPrice',
                        'buttonFunction' => 'addBasketItem',
                        'buttonLabel' => __('Add'),
                        'templateIndex' => 'new',
                    ])
                    @include('livewire.component.offcanvasform.numberinputmoney', ['modelId' => 'basketTotal', 'formLabel' => __('Total')])
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
                    @include('livewire.component.offcanvasform.selectorshop', ['modelId' => 'basketShop', 'shops' => $shops, 'selected' => $basketShop])
                    @include('livewire.component.offcanvasform.datetimelocalinput', ['modelId' => 'basketDate', 'formLabel' => __('Date')])
                    @include('livewire.component.offcanvasform.textinput', ['modelId' => 'basketReceiptId', 'formLabel' => __('Receipt ID')])
                    <div class="current-basket-items">
                    @foreach($basketItems as $key => $basketItem)
                        @include('livewire.component.offcanvasform.basketitem', [
                            'itemModelId' => 'basketItems.' . $key. '.item_id',
                            'items' => $items,
                            'selected' => $basketItem['item_id'],
                            'priceModelId' => 'basketItems.' . $key . '.price',
                            'buttonFunction' => 'deleteBasketItem(' . $key . ')',
                            'buttonLabel' => __('Delete'),
                            'templateIndex' => $key,
                        ])
                    @endforeach
                    </div>
                    @include('livewire.component.offcanvasform.basketitem', [
                        'itemModelId' => 'newBasketItemId',
                        'items' => $items,
                        'selected' => '',
                        'priceModelId' => 'newBasketItemPrice',
                        'buttonFunction' => 'addBasketItem',
                        'buttonLabel' => __('Add'),
                        'templateIndex' => 'new',
                    ])
                    @include('livewire.component.offcanvasform.numberinputmoney', ['modelId' => 'basketTotal', 'formLabel' => __('Total')])
                    @include('livewire.component.offcanvasform.textinput', ['modelId' => 'createdAt', 'formLabel' => __('Created'), 'readonly' => true])
                    @include('livewire.component.offcanvasform.textinput', ['modelId' => 'updatedAt', 'formLabel' => __('Updated'), 'readonly' => true])
                    <div class="mb-3" @if($basketImageURL != "") style="display: none;" @endif>
                        <label for="basketImage" class="form-label">{{ __('Receipt ID') }}</label>
                        <input type="file" class="form-control" id="basketImage" wire:model="basketImage">
                    </div>
                    <button type="submit" class="btn btn-primary mb-3">{{ __('Update') }}</button>
                    <button class="btn btn-danger mb-3" wire:click="deleteBasketImage" @if($basketImageURL == "") style="display: none;" @endif >{{ __('Delete Image') }}</button>
                </form>
                <img id="basketReceiptImage" src="{{ $basketImageURL }}" class="img-fluid" @if($basketImageURL == "") style="display: none;" @endif />
            </div>
        </div>
    </div>
    @include('livewire.component.offcanvasscipts')
</div>
