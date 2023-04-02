<div class="container">
    <button class="btn btn-primary mb-3" type="button" wire:click="setAction('{{ parent::ACTION_CREATE }}')"><i class="bi bi-plus-circle me-3"></i>{{ __('New Item') }}</button>
    {{ $items->links() }}
    <table class="table table-striped table-hover">
        <thead>
            <tr class="table-dark">
                @include('livewire.component.datatable.header-orderable', ['columnId' => 'id', 'columnLabel' => '#'])
                @include('livewire.component.datatable.header-orderable', ['columnId' => 'name', 'columnLabel' => __('Name')])
                @include('livewire.component.datatable.header-orderable', ['columnId' => 'created_at', 'columnLabel' => __('Created')])
                @include('livewire.component.datatable.header-orderable', ['columnId' => 'updated_at', 'columnLabel' => __('Updated')])
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <th scope="row">{{ $item->id }}</th>
                <td><a href="#" wire:click.prevent="loadForView({{ $item->id }})">{{ $item->name }}</a></td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
                <td>
                    <button class="btn btn-primary" type="button" wire:click="load({{ $item->id }})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    @if($item->basket_items_count == 0)
                        <button class="btn btn-danger" type="button" wire:click="loadForDelete({{ $item->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
    <livewire:component.panel :open="in_array($action, self::ACTIONS)" :position="'left'"
        :panelName="self::PANEL_NAME"
        :panelTitle="__('Item')"
        :backdrop="true"
        :contentTemplate="'livewire.component.item.panel'"
        :contentParameters="[ 'action' => $action, 'item' => $panelItem, 'viewData' => $viewData]">
</div>
