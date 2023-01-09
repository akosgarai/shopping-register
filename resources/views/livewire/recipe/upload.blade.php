<div class="card">
    <div class="card-header">{{ __('Upload document') }}</div>

    <div class="card-body">
        @if ($message !== '')
            <div class="alert alert-success" role="alert">
                {{ $message }}
            </div>
        @endif
        <div id="tui-image-editor" style="min-height: 800px;"></div>
        <form wire:submit.prevent="save">
            <input type="file" wire:model="file">
            <div wire:loading wire:target="file">Uploading...</div>

            @error('file') <span class="error">{{ $message }}</span> @enderror

            <button type="submit">Upload</button>
        </form>
        @if ($uploadedImageUrl !== '')
            <img src="{{ $uploadedImageUrl }}" alt="Uploaded image" style="max-height: 400px;">
        @endif
        @if ($parsedText !== '')
            <pre>
                    {{ $parsedText }}
            </pre>
        @endif
    </div>
    <script>
        document.addEventListener('livewire:load', function () {
            const instance = new ImageEditor(document.querySelector('#tui-image-editor'), {
                cssMaxWidth: 700,
                cssMaxHeight: 1000,
                includeUI: {
                    menuBarPosition: 'bottom'
                },
                selectionStyle: {
                    cornerSize: 20,
                    rotatingPointOffset: 70,
                },
            });
            document.addEventListener('resized', function () {
                instance.ui.resizeEditor();
            });
            console.log('livewire:load', instance);
        });
    </script>
</div>
