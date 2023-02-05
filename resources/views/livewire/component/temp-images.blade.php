<div>
    <form wire:submit.prevent="saveNewImage">
        <div class="mb-3">
            <label for="newImage" class="form-label">{{ __('Upload Image') }}</label>
            <input type="file" class="form-control" id="tempImage" wire:model="newImage" >
            <span id="errors-newImage" class="text-danger"></span>
        </div>
        <button type="submit" class="btn btn-primary" id="uploadTempImageButton" @if($newImage == '') style="display:none;" @endif>{{ __('Save') }}</button>
    </form>
    <div>
        <h6>{{ __('Or choose') }}</h6>
        <div class="d-flex flex-column flex-wrap align-content-stretch" id="uploaded-temp-images">
            @foreach($uploadedImages as $image)
                <div class="mb-3">
                    <div class="d-flex justify-content-between flex-wrap flex-grow">
                        <a href="#" wire:click.prevent="$emitUp('temp.image', 'load', '{{ $image }}')">
                            <img src="{{ route('image.viewTemp', ['filename' => $image]) }}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;"></a>
                        <div class="align-self-center">
                            <button type="button" class="btn btn-danger" wire:click.prevent="deleteImage('{{ $image }}')">{{ __('Delete') }}</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
