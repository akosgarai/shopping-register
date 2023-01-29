<li class="nav-item">
    <a class="nav-link @if($itemActive) text-info bg-dark @endif"
       href="#"
       wire:click.prevent="{{ $itemClick }}">{{ $itemLabel }}</a>
</li>
