<script>
    document.querySelectorAll('.offcanvas').forEach((element) => {
        element.addEventListener('hidden.bs.offcanvas', event => {
            Livewire.emit('offcanvasClose');
        });
    });
</script>
