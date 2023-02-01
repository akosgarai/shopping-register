<script>
    document.querySelectorAll('.offcanvas').forEach((element) => {
        element.addEventListener('hidden.bs.offcanvas', event => {
            Livewire.emit('offcanvasClose');
            // hide the offcanvas error messages. the error field has an errors- id prefix.
            document.querySelectorAll('.offcanvas [id^="errors-"]').forEach((element) => {
                element.style.display = 'none';
            });
        });
    });
    window.addEventListener('model.validation', event => {
        const data = event.detail;
        const id = data.type + data.model;
        Object.keys(data.messages).forEach(key => {
            const message = data.messages[key].join('<br>');
            const errorId = 'errors-' + key;
            // set error message
            const errorField = document.querySelector('#' + id + ' span[id="' + errorId + '"]');
            errorField.innerHTML = message;
            errorField.style.display = 'block';
        });
    });
    window.addEventListener('basket.loaded', event => {
        const url = event.detail.url;
        const image = document.querySelector('#basketReceiptImage');
        if (url) {
            image.src = url;
            image.style.display = 'block';
        } else {
            image.style.display = 'none';
        }
        // add the basket items to the basket list.
        // first clear the current list, then add the new items.
        const basketTable = document.querySelector('#updateBasket .current-basket-items');
        basketTable.innerHTML = '';
        Object.keys(event.detail.items).forEach(key => {
            const eventData = {
                'basketItemIndex': key,
                'selectedItemId': event.detail.items[key].item_id,
                'itemPrice': event.detail.items[key].price,
            };
            createBasketItemEditor(eventData, '#updateBasket');
        });
    });
    window.addEventListener('basketItem.added', event => {
        // clear the error messages
        document.querySelectorAll('.offcanvas [id^="errors-newBasketItem"]').forEach((element) => {
            element.style.display = 'none';
        });
        // clone the basket item template, configure it based on the event data, and append it to the basket items list
        const template = document.querySelector('#basketItemTemplate-new');
        // clear the select and price input elements
        template.querySelector('select').value = '';
        template.querySelector('input').value = '';
        createBasketItemEditor(event.detail, '.show');
    });
    window.addEventListener('basketItem.removed', event => {
        // remove the basket item from the basket items list
        const basketItem = document.querySelector('#basketItemTemplate-' + event.detail.basketItemIndex);
        basketItem.remove();
    });

    window.addEventListener('receiptScan.pick', event => {
        const offcanvas = new bootstrap.Offcanvas(document.getElementById('pickImageOffcanvas'));
        offcanvas.show();
    });
    window.addEventListener('tempImages.refresh', event => {
        rebuildTempImagesSelector(event.detail.images);
    });

    function createBasketItemEditor(data, offcanvas) {
        // clone the basket item template, configure it based on the event data, and append it to the basket items list
        const template = document.querySelector('#basketItemTemplate-new');
        const clone = template.cloneNode(true);
        clone.id = 'basketItemTemplate-' + data.basketItemIndex;
        // setup the select element. wire:model and id.
        const select = clone.querySelector('select');
        select.id = 'basketItems.' + data.basketItemIndex + '.item_id';
        select.setAttribute('wire:model', 'basketItems.' + data.basketItemIndex + '.item_id');
        select.querySelectorAll('option').forEach((element) => {
            if (element.value == data.selectedItemId) {
                element.selected = true;
            }
        });
        // replacte the error span ids with the new index
        const itemErrorSpan = clone.querySelector('#errors-newBasketItemId');
        itemErrorSpan.id = 'errors-basketItems.' + data.basketItemIndex + '.item_id';
        priceErrorSpan = clone.querySelector('#errors-newBasketItemPrice');
        priceErrorSpan.id = 'errors-basketItems.' + data.basketItemIndex + '.price';
        // setup the price input element. wire:model and id.
        const price = clone.querySelector('input');
        price.id = 'basketItems.' + data.basketItemIndex + '.price';
        price.setAttribute('wire:model', 'basketItems.' + data.basketItemIndex + '.price');
        price.value = data.itemPrice;
        // setup the button element. wire:click and label.
        const button = clone.querySelector('button');
        button.setAttribute('onclick', 'Livewire.emit("deleteBasketItem", ' + data.basketItemIndex + ')');
        button.innerHTML = 'Delete';
        // append the clone to the basket items list
        const basketItemsList = document.querySelector('.offcanvas' + offcanvas + ' .current-basket-items');
        basketItemsList.appendChild(clone);
    }
    function rebuildTempImagesSelector(images)
    {
        // delete the images from the list then refresh the list
        // based on the event data
        const imagesContainer = document.querySelector('#uploaded-temp-images');
        imagesContainer.innerHTML = '';
        images.forEach(image => {
            let imageContainer = document.createElement('div');
            imageContainer.classList.add('mb-3');
            let imageFlex = document.createElement('div');
            imageFlex.classList.add('d-flex', 'justify-content-between', 'flex-wrap', 'flex-grow');
            let loadAnchor = document.createElement('a');
            loadAnchor.href = '#';
            loadAnchor.addEventListener('click', function(e) {
                e.preventDefault();
                @this.loadTempImage(image);
            });
            let img = document.createElement('img');
            img.src = "{{ route('image.viewTemp', ['filename' => ':filename']) }}";
            img.src = img.src.replace(':filename', image);
            img.classList.add('img-thumbnail');
            img.style.maxWidth = '100px';
            img.style.maxHeight = '100px';
            loadAnchor.appendChild(img);
            imageFlex.appendChild(loadAnchor);
            let buttonContainer = document.createElement('div');
            buttonContainer.classList.add('align-self-center');
            let button = document.createElement('button');
            button.type = 'button';
            button.classList.add('btn', 'btn-danger');
            button.addEventListener('click', function(e) {
                e.preventDefault();
                @this.deleteTempImage(image);
            });
            button.innerText = '{{ __('Delete') }}';
            buttonContainer.appendChild(button);
            imageFlex.appendChild(buttonContainer);
            imageContainer.appendChild(imageFlex);
            imagesContainer.appendChild(imageContainer);
        });
    }
</script>
