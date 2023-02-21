<div class="container">
    <button class="btn btn-primary mb-3" type="button" wire:click="setAction('{{ parent::ACTION_CREATE }}')"><i class="bi bi-plus-circle me-3"></i>{{ __('New Address') }}</button>
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
                <td><a href="#" wire:click.prevent="loadForView({{ $address->id }})">{{ $address->raw }}</a></td>
                <td>{{ $address->created_at }}</td>
                <td>{{ $address->updated_at }}</td>
                <td>
                    <button class="btn btn-primary" type="button" wire:click="load({{ $address->id }})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    @if($address->companies_count == 0 && $address->shops_count == 0)
                        <button class="btn btn-danger" type="button" wire:click="loadForDelete({{ $address->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <livewire:component.panel :open="in_array($action, self::ACTIONS)" :position="'left'"
        :panelName="'addressPanel'"
        :panelTitle="__('Address')"
        :contentTemplate="'livewire.component.address.panel'"
        :contentParameters="[ 'action' => $action, 'address' => $panelAddress]">
</div>
