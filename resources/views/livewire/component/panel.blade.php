<div>
    <div class="panel {{ $position }} @if($open) visible @endif" aria-labelledby="{{ $panelName }}Label">
        <div class="panel-header">
            <h3 class="panel-title" id="{{ $panelName }}Label">{{ $panelTitle }}</h3>
            <button type="button" class="btn-close text-reset"  aria-label="{{ __('Close') }}" wire:click="$emitSelf('panel.close');"></button>
        </div>
        <div class="panel-body">
            @include($contentTemplate, $contentParameters)
        </div>
    </div>
</div>
