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
                <form wire:submit.prevent="saveTempImage">
                    <div class="mb-3">
                        <label for="tempImage" class="form-label">{{ __('Upload Image') }}</label>
                        <input type="file" class="form-control" id="tempImage" wire:model="tempImage"
                            onclick="hideImageUploadSubmitButton()"
                            onchange="validateImage(this.value);">
                        <span id="errors-tempImage" class="text-danger" style="display: none;"></span>
                    </div>
                    <button type="submit" class="btn btn-primary" id="uploadTempImageButton" @if($tempImage == '') style="display:none;" @endif>{{ __('Save') }}</button>
                </form>
                <div>
                    <h6>{{ __('Or') }}</h6>
                    @foreach($prevTempImages as $image)
                        <a href="#" wire:click="loadTempImage('{{ $image }}')">
                            <img src="{{ route('image.viewTemp', ['filename' => $image]) }}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;"></a>
                    @endforeach
                </div>
            </div>
        </div>
        <script>
            function hideImageUploadSubmitButton() {
                document.querySelector('#uploadTempImageButton').style.display = 'none';
            }
            function validateImage(path) {
                if (path !== '') {
                    document.querySelector('#uploadTempImageButton').style.display = 'block';
                }
            }
        </script>
    </div>
    @if($action == self::ACTION_PICK)
        <script>
            window.addEventListener('livewire:load', function () {
                var offcanvas = new bootstrap.Offcanvas(document.getElementById('pickImageOffcanvas'));
                offcanvas.show();
                hideImageUploadSubmitButton();
            });
        </script>
    @endif
    @if($action == self::ACTION_EDIT)
        @if ($imagePath)
            <img src="{{ route('image.viewTemp', ['filename' => $imagePath]) }}" alt="tempImage" class="img-fluid" style="width: 100%;">
        @endif
    @endif
    @include('livewire.component.offcanvasscipts')
</div>
