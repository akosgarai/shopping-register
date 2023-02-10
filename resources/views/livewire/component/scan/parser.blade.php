<div class="d-grid gap-2">
    @foreach($parsers as $parser)
        <input type="button" class="btn btn-primary" wire:click="$emitUp('parse.text.with', '{{$parser['name']}}')" value="{{ __($parser['label']) }}" >
    @endforeach
</div>
