document.addEventListener("DOMContentLoaded", function() {
    const containers = document.querySelectorAll('.roster-container');
    
    containers.forEach(container => {
        // Column Toggle (Scoped)
        const toggles = container.querySelectorAll('.col-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const targetClass = this.getAttribute('data-target');
                const cells = container.querySelectorAll('.' + targetClass);
                cells.forEach(cell => {
                    cell.style.display = this.checked ? '' : 'none';
                });
            });
        });
    });
});
