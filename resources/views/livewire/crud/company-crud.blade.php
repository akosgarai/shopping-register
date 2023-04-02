<div class="container">
    <button class="btn btn-primary mb-3" type="button" wire:click="setAction('{{ parent::ACTION_CREATE }}')"><i class="bi bi-plus-circle me-3"></i>{{ __('New Company') }}</button>
    {{ $companies->links() }}
    <table class="table table-striped table-hover">
        <thead>
            <tr class="table-dark">
                @include('livewire.component.datatable.header-orderable', ['columnId' => 'id', 'columnLabel' => '#'])
                @include('livewire.component.datatable.header-orderable', ['columnId' => 'name', 'columnLabel' => __('Name')])
                @include('livewire.component.datatable.header-orderable', ['columnId' => 'tax_number', 'columnLabel' => __('Tax Number')])
                <th scope="col">{{ __('Address') }}</th>
                @include('livewire.component.datatable.header-orderable', ['columnId' => 'created_at', 'columnLabel' => __('Created')])
                @include('livewire.component.datatable.header-orderable', ['columnId' => 'updated_at', 'columnLabel' => __('Updated')])
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
            <tr>
                <th scope="row">{{ $company->id }}</th>
                <td><a href="#" wire:click.prevent="loadForView({{ $company->id }})">{{ $company->name }}</a></td>
                <td>{{ $company->tax_number }}</td>
                <td>{{ $company->address->raw }}</td>
                <td>{{ $company->created_at }}</td>
                <td>{{ $company->updated_at }}</td>
                <td>
                    <button class="btn btn-primary" type="button" wire:click="load({{ $company->id }})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    @if($company->shops_count == 0)
                        <button class="btn btn-danger" type="button" wire:click="loadForDelete({{ $company->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $companies->links() }}
    <livewire:component.panel :open="in_array($action, self::ACTIONS)" :position="'left'"
        :panelName="self::PANEL_NAME"
        :panelTitle="__('Company')"
        :backdrop="true"
        :contentTemplate="'livewire.component.company.panel'"
        :contentParameters="[ 'action' => $action, 'addresses' => $addresses, 'company' => $panelCompany, 'viewData' => $viewData ]">
</div>
