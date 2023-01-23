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
    window.addEventListener('basket-image', event => {
        const url = event.detail.url;
        const image = document.querySelector('#basketReceiptImage');
        if (url) {
            image.src = url;
            image.style.display = 'block';
        } else {
            image.style.display = 'none';
        }
    });
    window.addEventListener('basketItem.added', event => {
        // clear the error messages
        document.querySelectorAll('.offcanvas [id^="errors-newBasketItem"]').forEach((element) => {
            element.style.display = 'none';
        });
        // clone the basket item template, configure it based on the event data, and append it to the basket items list
        const template = document.querySelector('#basketItemTemplate-new');
        const clone = template.cloneNode(true);
        clone.id = 'basketItemTemplate-' + event.detail.basketItemIndex;
        // setup the select element. wire:model and id.
        const select = clone.querySelector('select');
        select.id = 'basketItems.' + event.detail.basketItemIndex + '.item_id';
        select.setAttribute('wire:model', 'basketItems.' + event.detail.basketItemIndex + '.item_id');
        // the index of the selected option is the value of the event.detail.selectedItemId
        select.querySelectorAll('option').forEach((element) => {
            if (element.value == event.detail.selectedItemId) {
                element.selected = true;
            }
        });
        // replacte the error span ids with the new index
        const itemErrorSpan = clone.querySelector('#errors-newBasketItemId');
        itemErrorSpan.id = 'errors-basketItems.' + event.detail.basketItemIndex + '.item_id';
        priceErrorSpan = clone.querySelector('#errors-newBasketItemPrice');
        priceErrorSpan.id = 'errors-basketItems.' + event.detail.basketItemIndex + '.price';
        // setup the price input element. wire:model and id.
        const price = clone.querySelector('input');
        price.id = 'basketItems.' + event.detail.basketItemIndex + '.price';
        price.value = event.detail.itemPrice;
        price.setAttribute('wire:model', 'basketItems.' + event.detail.basketItemIndex + '.price');
        // setup the button element. wire:click and label.
        const button = clone.querySelector('button');
        button.setAttribute('onclick', 'Livewire.emit("deleteBasketItem", ' + event.detail.basketItemIndex + ')');
        button.innerHTML = event.detail.buttonLabel;
        // append the clone to the basket items list
        const basketItemsList = document.querySelector('.offcanvas.show .current-basket-items');
        basketItemsList.appendChild(clone);
        // clear the select and price input elements
        template.querySelector('select').value = '';
        template.querySelector('input').value = '';
    });
    window.addEventListener('basketItem.removed', event => {
        // remove the basket item from the basket items list
        const basketItem = document.querySelector('#basketItemTemplate-' + event.detail.basketItemIndex);
        basketItem.remove();
    });
</script>
