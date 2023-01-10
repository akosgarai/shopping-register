<div class="row">
    @if ($uploadedImageUrl !== '')
        <div class="col-6">
            <img src="{{ $uploadedImageUrl }}" alt="Uploaded image" style="max-height: 400px;">
        </div>
    @endif
    @if ($parsedText !== '')
        <div class="col-6">
            <pre>
                    {{ $parsedText }}
            </pre>
        </div>
    @endif
</div>
