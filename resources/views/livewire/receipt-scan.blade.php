<div class="container">
    <ul class="nav">
        @include('livewire.component.navitem', [
            'itemLabel' => __('Choose Image'),
            'itemActive' => $action == self::ACTION_PICK,
            'itemClick' => '$emitSelf("action.change", "' . self::ACTION_PICK . '")',
            ])
        @if ($imagePath != '')
            @include('livewire.component.navitem', [
                'itemLabel' => __('Edit Image'),
                'itemActive' => $action == self::ACTION_EDIT,
                'itemClick' => '$emitSelf("action.change", "' . self::ACTION_EDIT . '")',
                ])
            @include('livewire.component.navitem', [
                'itemLabel' => __('Select Parser'),
                'itemActive' => $action == self::ACTION_PARSE,
                'itemClick' => '$emitSelf("action.change", "' . self::ACTION_PARSE . '")',
                ])
            @if ($parserApplication != '')
                @include('livewire.component.navitem', [
                    'itemLabel' => __('Create Basket'),
                    'itemActive' => array_search($action, self::ACTION_STEP) > 2,
                    'itemClick' => '$emitSelf("action.change", "' . self::ACTION_BASKET . '")',
                    ])
            @endif
        @endif
    </ul>
    <div id="image-editor" @if($action != self::ACTION_EDIT) style="display:none;" @endif class="mt-3">
        <livewire:component.image-manipulator :image="$imagePath" />
    </div>
    @if($action == '')
        <div class="col-sm-12">
            Default screen. No action has been selected.
        </div>
    @endif
    @if(array_search($action, self::ACTION_STEP) > 1)
        <div class="row">
            @if($imagePath != '')
                <div class="col-sm-6">
                    <img src="{{ route('image.viewTemp', ['filename' =>  $imagePath, 'v' => time()]) }}" class="img-fluid img-thumbnail">
                </div>
            @endif
            @if($rawExtractedText != '')
                <div class="col-sm-6">
                    <pre>
                            {{ $rawExtractedText }}
                    </pre>
                </div>
            @endif
        </div>
    @endif
    <livewire:component.panel :open="$action == self::ACTION_PARSE" :position="'right'"
        :panelName="self::PANEL_PARSER"
        :panelTitle="__('Select Parser')"
        :contentTemplate="'livewire.component.scan.parser-template'"
        :contentParameters="[ 'parsers' => self::PARSERS ]">
    <livewire:component.panel :open="$action == self::ACTION_BASKET" :position="'right'"
        :panelName="self::PANEL_BASKET_ID"
        :panelTitle="__('Basket ID')"
        :contentTemplate="'livewire.component.scan.receiptid'"
        :contentParameters="[ 'basket' => $basket ]">
    <livewire:component.panel :open="$action == self::ACTION_COMPANY" :position="'right'"
        :panelName="self::PANEL_BASKET_COMPANY"
        :panelTitle="__('Company')"
        :contentTemplate="'livewire.component.scan.companyid'"
        :contentParameters="[ 'basket' => $basket ]">
    <livewire:component.panel :open="$action == self::ACTION_SHOP" :position="'right'"
        :panelName="self::PANEL_BASKET_SHOP"
        :panelTitle="__('Shop')"
        :contentTemplate="'livewire.component.scan.shopid'"
        :contentParameters="[ 'basket' => $basket ]">
    <livewire:component.panel :open="$action == self::ACTION_PICK" :position="'left'"
        :panelName="self::PANEL_PICK_IMAGE"
        :panelTitle="__('Choose Image')"
        :contentTemplate="'livewire.component.images.temp'"
        :contentParameters="[ ]">
    <div wire:loading.flex class="loading">
        @include('components.loader-tesseract')
    </div>
</div>
