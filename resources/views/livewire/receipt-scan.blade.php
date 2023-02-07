<div class="container">
    <ul class="nav">
        @include('livewire.component.navitem', [
            'itemLabel' => __('Choose Image'),
            'itemActive' => $action == self::ACTION_PICK,
            'itemClick' => '$emitSelf("temp.image", "openpanel")'
            ])
        @if ($imagePath != '')
            @include('livewire.component.navitem', [
                'itemLabel' => __('Edit Image'),
                'itemActive' => $action == self::ACTION_EDIT,
                'itemClick' => '$emitSelf("image.editing", "start", "")',
                ])
            @include('livewire.component.navitem', [
                'itemLabel' => __('Select Parser'),
                'itemActive' => $action == self::ACTION_PARSE,
                'itemClick' => 'selectParserClickHandler',
                ])
            @if ($parserApplication != '')
                @include('livewire.component.navitem', [
                    'itemLabel' => __('Create Basket'),
                    'itemActive' => $action == self::ACTION_BASKET,
                    'itemClick' => '$set("action", "' . self::ACTION_BASKET . '")',
                    ])
            @endif
        @endif
    </ul>
    <div id="image-editor" @if($action != self::ACTION_EDIT) style="display:none;" @endif class="mt-3">
        <livewire:component.image-manipulator :image="$imagePath" />
    </div>
    @if($action == self::ACTION_PARSE)
        <div class="col-sm-12">
            <button type="button" class="btn btn-primary" wire:click="parseText('spar')">{{ __('Spar') }}</button>
        </div>
    @elseif($action == self::ACTION_BASKET)
        <ul class="nav">
            @include('livewire.component.navitem', [
                'itemLabel' => __('Basket ID'),
                'itemActive' => $createBasketTab == self::BASKET_TAB_ID,
                'itemClick' => '$emitSelf("basket.data", "basketId")',
                ])
            @include('livewire.component.navitem', [
                'itemLabel' => __('Company'),
                'itemActive' => $createBasketTab == self::BASKET_TAB_COMPANY,
                'itemClick' => '$emitSelf("basket.data", "companyId")',
                ])
            @include('livewire.component.navitem', [
                'itemLabel' => __('Shop'),
                'itemActive' => $createBasketTab == self::BASKET_TAB_SHOP,
                'itemClick' => '$emitSelf("basket.data", "shopId")',
                ])
            @include('livewire.component.navitem', [
                'itemLabel' => __('Items'),
                'itemActive' => $action == self::ACTION_PICK,
                'itemClick' => '$emitSelf("basket.data", "basketId")',
                ])
        </ul>
    @endif
    <div id="parser-selector" @if($action != self::ACTION_PARSE && $action != self::ACTION_BASKET) style="display:none;" @endif class="row">
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
    <livewire:component.panel :open="$createBasketTab == self::BASKET_TAB_ID" :position="'right'"
        :panelName="self::PANEL_BASKET_ID"
        :panelTitle="__('Basket ID')"
        :contentTemplate="'livewire.component.scan.receiptid'"
        :contentParameters="[ 'basket' => $basket ]">
    <livewire:component.panel :open="$createBasketTab == self::PANEL_BASKET_COMPANY" :position="'right'"
        :panelName="self::PANEL_BASKET_COMPANY"
        :panelTitle="__('Company')"
        :contentTemplate="'livewire.component.scan.companyid'"
        :contentParameters="[ 'basket' => $basket ]">
    <livewire:component.panel :open="$createBasketTab == self::BASKET_TAB_SHOP" :position="'right'"
        :panelName="self::PANEL_BASKET_SHOP"
        :panelTitle="__('Shop')"
        :contentTemplate="'livewire.component.scan.shopid'"
        :contentParameters="[ 'basket' => $basket ]">
    <livewire:component.panel :open="$action == self::ACTION_PICK" :position="'left'"
        :panelName="self::PANEL_PICK_IMAGE"
        :panelTitle="__('Choose Image')"
        :contentTemplate="'livewire.component.images.temp'"
        :contentParameters="[ ]">
</div>
