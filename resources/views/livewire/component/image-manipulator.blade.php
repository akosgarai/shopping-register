<div>
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
            @if($image)
                loadImageInstance('{{ route('image.viewTemp', ['filename' => $image, 'v' => time()]) }}');
            @endif
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
            Livewire.emit('image.editing', 'finished', instance.toDataURL());
        });
    </script>
</div>
