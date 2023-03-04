<div class="container">
    <button class="btn btn-primary mb-3" type="button" wire:click="setAction('{{ parent::ACTION_CREATE }}')"><i class="bi bi-plus-circle me-3"></i>{{ __('New Basket') }}</button>
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
                <td><a href="#" wire:click.prevent="loadForView({{ $basket->id }})">{{ $basket->receipt_id }}</a></td>
                <td>{{ $basket->basket_items_count }}</td>
                <td>{{ $basket->created_at }}</td>
                <td>{{ $basket->updated_at }}</td>
                <td>
                    <button class="btn btn-primary" type="button" wire:click="load({{ $basket->id }})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    @if($basket->basket_items_count == 0)
                        <button class="btn btn-danger" type="button" wire:click="loadForDelete({{ $basket->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <livewire:component.panel :open="in_array($action, self::ACTIONS)" :position="'left'"
        :panelName="self::PANEL_NAME"
        :panelTitle="__('Basket')"
        :contentTemplate="'livewire.component.basket.panel'"
        :contentParameters="[ 'action' => $action, 'shopOptions' => $shopOptions, 'itemOptions' => $itemOptions, 'basket' => $panelBasket, 'formData' => $formData, 'viewData' => $viewData]">
</div>
