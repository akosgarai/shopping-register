<div>
    <div class="panel {{ $position }} @if($fullWidth) full-width @endif @if($open) visible @endif" aria-labelledby="{{ $panelName }}Label">
        <div class="panel-header">
            <h3 class="panel-title" id="{{ $panelName }}Label">{{ $panelTitle }}</h3>
            <div>
                <button type="button" class="btn text-reset"  aria-label="{{ __('Expand') }}" wire:click="$set('fullWidth', !@this.fullWidth);"><i class="bi bi-arrow-left-right"></i></button>
                <button type="button" class="btn-close text-reset"  aria-label="{{ __('Close') }}" wire:click="$emitUp('action.back');"></button>
            </div>
        </div>
        <div class="panel-body">
            @if($contentTemplate != '')
                @include($contentTemplate, $contentParameters)
            @endif
        </div>
    </div>
    @if($open && $backdrop)
        <div class="backdrop"></div>
    @endif
</div>
