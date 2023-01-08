<div class="card">
    <div class="card-header">{{ __('Upload document') }}</div>

    <div class="card-body">
        @if ($message !== '')
            <div class="alert alert-success" role="alert">
                {{ $message }}
            </div>
        @endif
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
</div>
