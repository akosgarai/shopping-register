<div class="container">
    <ul class="nav">
        @include('livewire.component.navitem', [
            'itemLabel' => __('Choose Image'),
            'itemActive' => $action == self::ACTION_PICK,
            'itemClick' => 'offcanvasOpen',
            ])
        @if ($imagePath != '')
            @include('livewire.component.navitem', [
                'itemLabel' => __('Edit Image'),
                'itemActive' => $action == self::ACTION_EDIT,
                'itemClick' => 'editStep',
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
        @endif
    </ul>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="pickImageOffcanvas" aria-labelledby="pickImageOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="pickImageOffcanvasLabel">{{ __('Choose Image') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click='offcanvasClose'></button>
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
                        <a href="#" wire:click.prevent="loadTempImage('{{ $image }}')">
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
    <div id="image-editor" @if($action != self::ACTION_EDIT) style="display:none;" @endif>
        <livewire:component.imageditor />
        <div wire:ignore>
            <div id="tui-image-editor"></div>
        </div>
        <script>
            let instance = null;
            function loadImageInstance (url) {
                instance.loadImageFromURL(url, 'sample').then((result) => {
                    resizeEditor();
                });
            }
            function resizeEditor() {
                // setup the editor height based on the canvas-container height
                const canvasContainer = document.querySelector('.tui-image-editor-canvas-container');
                const canvasContainerHeight = parseFloat(canvasContainer.style.maxHeight);
                document.querySelector('#tui-image-editor').style.height = canvasContainerHeight + 'px';
            }
            window.addEventListener('receiptScan.edit', event => {
                document.querySelector('#image-editor').style.display = 'block';
                loadImageInstance(event.detail.imagePath);
            });
            document.addEventListener('livewire:load', function () {
                instance = new ImageEditor('#tui-image-editor', {
                    cssMaxWidth: 700,
                    cssMaxHeight: 1000,
                    selectionStyle: {
                        cornerSize: 20,
                        rotatingPointOffset: 70,
                    },
                });
            });
            // Listen for the editor.* events
            document.addEventListener('editor.crop', function (e) {
                instance.startDrawingMode('CROPPER');
            });
            document.addEventListener('editor.apply.crop', function (e) {
                instance.crop(instance.getCropzoneRect()).then(function () {
                    instance.stopDrawingMode();
                    resizeEditor();
                });
            });
            document.addEventListener('editor.cancel', function (e) {
                instance.stopDrawingMode();
            });
            document.addEventListener('editor.filter', function (e) {
                if (e.detail.value) {
                    instance.applyFilter(e.detail.filter, null);
                } else {
                    instance.removeFilter(e.detail.filter);
                }
            });
        document.addEventListener('editor.complete', function (e) {
            Livewire.emit('edit.finished', instance.toDataURL());
        });
        </script>
    </div>
    <div id="parser-selector" @if($action != self::ACTION_PARSE) style="display:none;" @endif class="row">
        @if($imagePath != '')
            <div class="col-sm-6">
                <img src="{{ route('image.viewTemp', ['filename' =>  $imagePath]) }}" class="img-fluid img-thumbnail">
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
    @if($action == self::ACTION_PICK)
        <script>
            window.addEventListener('livewire:load', function () {
                var offcanvas = new bootstrap.Offcanvas(document.getElementById('pickImageOffcanvas'));
                offcanvas.show();
                hideImageUploadSubmitButton();
            });
        </script>
    @endif
    @if($action == self::ACTION_EDIT && $imagePath)
        <script>
            document.addEventListener('livewire:load', function () {
                loadImageInstance('{{ route('image.viewTemp', ['filename' => $imagePath]) }}');
            });
        </script>
    @endif
    @include('livewire.component.offcanvasscipts')
</div>
