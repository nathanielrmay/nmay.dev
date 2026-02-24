document.addEventListener("DOMContentLoaded", function() {
    // Find all schedule containers
    const containers = document.querySelectorAll('.schedule-container');
    
    containers.forEach(container => {
        // Time Format (Scoped to each container)
        const timeElements = container.querySelectorAll('.local-time');
        timeElements.forEach(el => {
            const utcTime = el.getAttribute('data-utc');
            if (utcTime) {
                const date = new Date(utcTime);
                const localTime = date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
                el.textContent = localTime;
            }
        });

        // Column Toggle (Scoped to each container)
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

        // Limit Control (Scoped to each container)
        const limitSelect = container.querySelector('.limit-select');
        if (limitSelect) {
            const applyLimit = () => {
                const val = limitSelect.value;
                const rows = container.querySelectorAll('.sched-row');
                rows.forEach(row => {
                    const idx = parseInt(row.getAttribute('data-idx'));
                    row.style.display = (val === 'all' || idx <= (val === 'all' ? Infinity : parseInt(val))) ? '' : 'none';
                });
            };
            limitSelect.addEventListener('change', applyLimit);
            applyLimit();
        }
    });
});
