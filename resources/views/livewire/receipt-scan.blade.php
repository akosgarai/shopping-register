<div class="container">
    <ul class="nav">
        @include('livewire.component.navitem', [
            'itemLabel' => __('Choose Image'),
            'itemActive' => $action == self::ACTION_PICK,
            'itemClick' => 'offcanvasOpen',
            ])
        @include('livewire.component.navitem', [
            'itemLabel' => __('Edit Image'),
            'itemActive' => $action == self::ACTION_EDIT,
            'itemClick' => '$set("action", "' . self::ACTION_EDIT . '")',
            ])
        @include('livewire.component.navitem', [
            'itemLabel' => __('Select Parser'),
            'itemActive' => $action == self::ACTION_PARSE,
            'itemClick' => '$set("action", "' . self::ACTION_PARSE . '")',
            ])
        @include('livewire.component.navitem', [
            'itemLabel' => __('Create Basket'),
            'itemActive' => $action == self::ACTION_BASKET,
            'itemClick' => '$set("action", "' . self::ACTION_BASKET . '")',
            ])
    </ul>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="pickImageOffcanvas" aria-labelledby="pickImageOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="pickImageOffcanvasLabel">{{ __('Choose Image') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click='$set("action", "")'></button>
            </div>
            <div class="offcanvas-body">
                Content goes here.
            </div>
        </div>
    </div>
    @if($action == self::ACTION_PICK)
        <script>
            window.addEventListener('livewire:load', function () {
                var offcanvas = new bootstrap.Offcanvas(document.getElementById('pickImageOffcanvas'));
                offcanvas.show();
            });
        </script>
    @endif
    @include('livewire.component.offcanvasscipts')
</div>
