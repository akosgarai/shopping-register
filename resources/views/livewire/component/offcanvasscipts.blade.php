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
            const errorField = document.querySelector('#' + id + ' #' + errorId);
            errorField.innerHTML = message;
            errorField.style.display = 'block';
        });
    });
</script>
