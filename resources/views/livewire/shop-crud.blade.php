<div class="container">
    <button class="btn btn-primary mb-3" type="button" wire:click="setAction('{{ parent::ACTION_CREATE }}')"><i class="bi bi-plus-circle me-3"></i>{{ __('New Shop') }}</button>
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
                <td><a href="#" wire:click.prevent="loadForView({{ $shop->id }})">{{ $shop->name }}</a></td>
                <td>{{ $shop->company->name }}</td>
                <td>{{ $shop->address->raw }}</td>
                <td>{{ $shop->created_at }}</td>
                <td>{{ $shop->updated_at }}</td>
                <td>
                    <button class="btn btn-primary" type="button" wire:click="load({{ $shop->id }})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    @if($shop->baskets_count == 0)
                        <button class="btn btn-danger" type="button" wire:click="loadForDelete({{ $shop->id }})">
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
        :panelTitle="__('Shop')"
        :contentTemplate="'livewire.component.shop.panel'"
        :contentParameters="[ 'action' => $action, 'addresses' => $addresses, 'companies' => $companies, 'shop' => $panelShop, 'viewData' => $viewData ]">
</div>
