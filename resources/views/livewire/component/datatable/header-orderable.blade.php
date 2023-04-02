<th scope="col">
    <a href="#" wire:click.prevent="orderColumnChangeTo('{{ $columnId }}')"
       class="{{ $orderColumn == $columnId ? 'text-white fst-italic' : '' }}"
        >{{ $columnLabel }}</a>
    @if($orderColumn == $columnId)
        @if($orderDirection == 'asc')
            <i class="bi bi-caret-up-fill"></i>
        @else
            <i class="bi bi-caret-down-fill"></i>
        @endif
    @endif
</th>
