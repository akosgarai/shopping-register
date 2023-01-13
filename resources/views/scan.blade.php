@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="accordion" id="imageAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingPicker">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePicker" aria-expanded="true" aria-controls="collapsePicker">
                        {{ _('Pick Image') }}
                    </button>
                </h2>
                <div id="collapsePicker" class="accordion-collapse collapse show" aria-labelledby="headingPicker" data-bs-parent="#imageAccordion">
                    <div class="accordion-body">
                        <livewire:component.filepicker />
                    </div>
                </div>
            </div>
            <div class="accordion-item" style="display: none;">
                <h2 class="accordion-header" id="headingEdit">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEdit" aria-expanded="false" aria-controls="collapseEdit">
                        {{ _('Edit Image') }}
                    </button>
                </h2>
                <div id="collapseEdit" class="accordion-collapse collapse" aria-labelledby="headingEdit" data-bs-parent="#imageAccordion">
                    <div class="accordion-body">
                        <livewire:component.imageditor />
                        <div id="tui-image-editor"></div>
                    </div>
                </div>
            </div>
            <div class="accordion-item" style="display: none;">
                <h2 class="accordion-header" id="headingFixData">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFixData" aria-expanded="false" aria-controls="collapseFixData">
                        {{ _('Fix Extraction Data') }}
                    </button>
                </h2>
                <div id="collapseFixData" class="accordion-collapse collapse" aria-labelledby="headingFixData" data-bs-parent="#imageAccordion">
                    <div class="accordion-body">
                        <livewire:receipt.upload />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', function () {
        const instance = new ImageEditor('#tui-image-editor', {
            cssMaxWidth: 700,
            cssMaxHeight: 1000,
            selectionStyle: {
                cornerSize: 20,
                rotatingPointOffset: 70,
            },
        });
        let imageFile = null;
        function resizeEditor() {
            // setup the editor height based on the canvas-container height
            const canvasContainer = document.querySelector('.tui-image-editor-canvas-container');
            const canvasContainerHeight = parseFloat(canvasContainer.style.maxHeight);
            document.querySelector('#tui-image-editor').style.height = canvasContainerHeight + 'px';
        }
        // Listen for the event that is emitted after the file is uploaded
        document.addEventListener('imageUploaded', function (e) {
            imageFile = e.detail.fileTmpUrl;
            instance.loadImageFromURL(imageFile, 'sample').then((result) => {
                resizeEditor();
                // display the editor accordion.
                document.querySelector('#headingEdit').parentNode.style.display = 'block';
                // Hide the filepicker with triggering a click event on the editor accordion item
                document.querySelector('#headingEdit button').click();
            });
        });
        // Listen for the editor.crop event
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
        document.addEventListener('editor.complete', function (e) {
            // display the fix data accordion.
            document.querySelector('#headingFixData').parentNode.style.display = 'block';
            // Hide the editor with triggering a click event on the editor accordion item
            document.querySelector('#headingFixData button').click();
            Livewire.emit('edit.finished', imageFile, instance.toDataURL());
        });
        document.addEventListener('editor.filter', function (e) {
            if (e.detail.value) {
                instance.applyFilter(e.detail.filter, null);
            } else {
                instance.removeFilter(e.detail.filter);
            }
        });
    });
</script>
@endsection
