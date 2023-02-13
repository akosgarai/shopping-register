<div>
    @if(!$shopId)
        <h3>{{ __('You have to setup the Shop first.') }}</h3>
    @else
        <h3>{{ __('You have Shop. Very nice.') }} - {{ $scannedTotal }}</h3>
    @endif
</div>
