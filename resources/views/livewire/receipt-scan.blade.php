<div class="container">
    <ul class="nav">
        @include('livewire.component.navitem', [
            'itemLabel' => __('Choose Image'),
            'itemActive' => $action == self::ACTION_PICK,
            'itemClick' => '$set("action", "' . self::ACTION_PICK . '")',
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
</div>
